<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\AreaLingkungan;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Notifikasi;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan formulir pembuatan laporan baru untuk Warga.
     *
     * Data dimuat secara dinamis dari database:
     * 1. Profil dan hierarki wilayah warga (RT & RW)
     * 2. Daftar kategori aktif yang dipetakan ke ikon UI
     * 3. Pilihan area lingkungan / blok
     * 4. Agregasi statistik laporan untuk navigasi sidebar
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['wilayah.parent']);
        }

        // 1. Resolusi Profil Warga & Hierarki Wilayah
        $profil = $this->resolveProfilWarga($user);

        // 2. Kategori Aktif dari Database beserta metadata kontekstual untuk form
        $kategoris = Kategori::where('aktif', true)
            ->orderBy('created_at')
            ->get()
            ->map(function (Kategori $kategori): Kategori {
                $meta = $this->resolveKategoriMeta($kategori->nama);
                $kategori->short_name = $meta['short_name'];
                $kategori->icon_type = $meta['icon_type'];
                $kategori->sort_order = $meta['sort_order'];
                $kategori->tips = $meta['tips'];
                $kategori->placeholder_judul = $meta['placeholder_judul'];
                $kategori->placeholder_uraian = $meta['placeholder_uraian'];
                $kategori->contoh_judul = $meta['contoh_judul'];
                $kategori->suggested_dampak = $meta['suggested_dampak'];
                $kategori->suggested_urgensi = $meta['suggested_urgensi'];

                return $kategori;
            })
            ->sortBy('sort_order')
            ->values();

        // 3. Pilihan Area Lingkungan & Saran Lokasi Dinamis dari Database per RT
        $rtWilayahId = $this->resolveRtWilayahId($user);
        $daftarArea = $this->resolveAreaLingkungan($rtWilayahId);
        $daftarLokasi = $this->resolveRiwayatLokasi($rtWilayahId);

        // 4. Jumlah Laporan Pengguna (untuk badge sidebar)
        $totalLaporanSaya = $user ? Report::where('pelapor_id', $user->id)->count() : 0;

        // 5. Notifikasi Pengguna
        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.buat_laporan', array_merge($profil, [
            'user' => $user,
            'kategoris' => $kategoris,
            'daftarArea' => $daftarArea,
            'daftarLokasi' => $daftarLokasi,
            'totalLaporanSaya' => $totalLaporanSaya,
            'notifikasis' => $notifikasiData['list'],
            'unreadNotifikasiCount' => $notifikasiData['unread_count'],
        ]));
    }

    /**
     * Simpan laporan baru ke database beserta lampiran & notifikasi.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Resolusi wilayah pelapor
        $wilayahId = $user->wilayah_id;
        if (! $wilayahId) {
            $defaultRt = Wilayah::where('tingkat', 'rt')->first();
            $wilayahId = $defaultRt?->id;
        }

        $validated = $request->validated();

        $report = DB::transaction(function () use ($user, $wilayahId, $validated, $request): Report {
            // 1. Generate Kode Unik Laporan (cth: LAP-202611-0004)
            $kode = $this->generateKodeLaporan();

            // 2. Simpan Data Laporan
            $report = Report::create([
                'kode' => $kode,
                'pelapor_id' => $user->id,
                'wilayah_id' => $wilayahId,
                'kategori_id' => $validated['kategori_id'],
                'judul' => $validated['judul'],
                'uraian' => $validated['uraian'],
                'lokasi_detail' => $validated['lokasi_detail'],
                'blok' => filled($validated['blok'] ?? null) ? trim((string) $validated['blok']) : null,
                'dampak' => $validated['dampak'],
                'urgensi' => $validated['urgensi'],
                'status_penerimaan' => 'baru',
            ]);

            // 3. Simpan Foto Lampiran (jika diunggah)
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $storageKey = $file->store('lampirans/laporan', 'public');

                Lampiran::create([
                    'pengunggah_id' => $user->id,
                    'laporan_id' => $report->id,
                    'storage_key' => $storageKey,
                    'nama_file' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType() ?: 'image/jpeg',
                    'ukuran_byte' => $file->getSize(),
                    'created_at' => now(),
                ]);
            }

            // 4. Buat Notifikasi Konfirmasi untuk Pelapor (Warga)
            Notifikasi::create([
                'penerima_id' => $user->id,
                'laporan_id' => $report->id,
                'judul' => 'Laporan Berhasil Terkirim',
                'pesan' => "Laporan {$report->kode} ('{$report->judul}') berhasil dikirim dan menunggu verifikasi admin RT.",
                'created_at' => now(),
            ]);

            // 5. Buat Notifikasi ke Admin RT setempat
            $adminRts = User::where('peran', 'admin_rt')
                ->where('wilayah_id', $wilayahId)
                ->get();

            foreach ($adminRts as $admin) {
                Notifikasi::create([
                    'penerima_id' => $admin->id,
                    'laporan_id' => $report->id,
                    'judul' => 'Laporan Warga Baru Masuk',
                    'pesan' => "Warga {$user->nama} mengirimkan laporan: '{$report->judul}' di {$report->lokasi_detail}.",
                    'created_at' => now(),
                ]);
            }

            return $report;
        });

        return redirect()
            ->route('warga.dashboard')
            ->with('success', "Laporan {$report->kode} berhasil dikirim! Pengurus RT akan segera meninjau dan menindaklanjutinya.");
    }

    /**
     * Generate kode urut unik laporan: LAP-YYYYMM-XXXX.
     */
    protected function generateKodeLaporan(): string
    {
        $prefix = 'LAP-'.now()->format('Ym').'-';

        $lastReport = Report::where('kode', 'like', "{$prefix}%")
            ->orderByDesc('kode')
            ->first();

        $nextNumber = 1;
        if ($lastReport) {
            $lastSequence = (int) substr($lastReport->kode, -4);
            $nextNumber = $lastSequence + 1;
        }

        return sprintf('%s%04d', $prefix, $nextNumber);
    }

    /**
     * Pemetaan nama kategori database ke label singkat, ikon, urutan tampil, dan asisten form kontekstual.
     *
     * @return array{
     *     short_name: string,
     *     icon_type: string,
     *     sort_order: int,
     *     tips: string,
     *     placeholder_judul: string,
     *     placeholder_uraian: string,
     *     contoh_judul: list<string>,
     *     suggested_dampak: string,
     *     suggested_urgensi: string
     * }
     */
    protected function resolveKategoriMeta(string $namaKategori): array
    {
        $namaLower = strtolower($namaKategori);

        if (str_contains($namaLower, 'sampah') || str_contains($namaLower, 'kebersihan')) {
            return [
                'short_name' => 'Sampah',
                'icon_type' => 'sampah',
                'sort_order' => 1,
                'tips' => 'Sebutkan jenis & perkiraan volume sampah, serta apakah menimbulkan bau atau menghalangi akses.',
                'placeholder_judul' => 'Contoh: Tumpukan sampah menumpuk dekat pos ronda',
                'placeholder_uraian' => 'Jelaskan perkiraan volume tumpukan sampah, sejak kapan belum terangkut, dan apakah menimbulkan bau menyengat atau mengundang lalat/tikus...',
                'contoh_judul' => [
                    'Tumpukan sampah rumah tangga belum terangkut',
                    'Pembuangan sampah liar di bahu jalan',
                    'Bak sampah umum penuh dan meluber',
                ],
                'suggested_dampak' => 'sedang',
                'suggested_urgensi' => 'sedang',
            ];
        }

        if (str_contains($namaLower, 'drainase') || str_contains($namaLower, 'saluran') || str_contains($namaLower, 'air')) {
            return [
                'short_name' => 'Drainase',
                'icon_type' => 'drainase',
                'sort_order' => 2,
                'tips' => 'Informasikan penyebab sumbatan (lumpur/sampah) dan ketinggian genangan saat turun hujan.',
                'placeholder_judul' => 'Contoh: Saluran air tersumbat di Gang 2',
                'placeholder_uraian' => 'Jelaskan kondisi selokan atau gorong-gorong yang mampet, tinggi genangan air saat hujan, serta rumah atau jalan yang terdampak luapan...',
                'contoh_judul' => [
                    'Saluran air mampet menyebabkan genangan',
                    'Selokan penuh endapan lumpur dan dedaunan',
                    'Penutup gorong-gorong patah/bergeser',
                ],
                'suggested_dampak' => 'sedang',
                'suggested_urgensi' => 'tinggi',
            ];
        }

        // Cek penerangan/lampu SEBELUM jalan agar "Penerangan Jalan Umum" tidak tertukar dengan "Jalan"
        if (str_contains($namaLower, 'penerangan') || str_contains($namaLower, 'lampu') || str_contains($namaLower, 'pju')) {
            return [
                'short_name' => 'Penerangan',
                'icon_type' => 'penerangan',
                'sort_order' => 5,
                'tips' => 'Catat nomor tiang lampu jika terlihat atau patokan rumah terdekat beserta berapa malam lampu padam.',
                'placeholder_judul' => 'Contoh: Lampu PJU mati total di tikungan Mawar Barat',
                'placeholder_uraian' => 'Jelaskan kondisi lampu (mati total, berkedip, atau kabel kendor), sudah berapa malam padam, dan apakah area tersebut gelap/rawan saat malam...',
                'contoh_judul' => [
                    'Lampu penerangan jalan umum mati total',
                    'Lampu PJU berkedip-kedip hampir padam',
                    'Kabel lampu gang kendor/membahayakan',
                ],
                'suggested_dampak' => 'sedang',
                'suggested_urgensi' => 'tinggi',
            ];
        }

        if (str_contains($namaLower, 'jalan') || str_contains($namaLower, 'trotoar')) {
            return [
                'short_name' => 'Jalan',
                'icon_type' => 'jalan',
                'sort_order' => 3,
                'tips' => 'Tuliskan perkiraan lebar atau kedalaman lubang dan patokan terdekat agar bisa segera diberi rambu.',
                'placeholder_judul' => 'Contoh: Jalan berlubang cukup dalam di depan Blok B',
                'placeholder_uraian' => 'Jelaskan jenis kerusakan jalan (berlubang, aspal terkelupas, paving amblas), perkiraan ukurannya, dan risikonya bagi pengendara/pejalan kaki...',
                'contoh_judul' => [
                    'Aspal jalan berlubang membahayakan pengendara',
                    'Paving block gang amblas dan licin',
                    'Trotoar retak/ambles di depan area warga',
                ],
                'suggested_dampak' => 'tinggi',
                'suggested_urgensi' => 'tinggi',
            ];
        }

        return [
            'short_name' => 'Fasilitas Umum',
            'icon_type' => 'fasilitas',
            'sort_order' => 4,
            'tips' => 'Sebutkan nama fasilitas umum (pos ronda, taman, balai warga) dan bagian spesifik yang rusak.',
            'placeholder_judul' => 'Contoh: Atap pos kamling RT bocor dan rapuh',
            'placeholder_uraian' => 'Jelaskan nama fasilitas umum, bagian yang mengalami kerusakan, sejak kapan terjadi, dan dampaknya terhadap kenyamanan/keamanan warga...',
            'contoh_judul' => [
                'Atap pos ronda bocor saat hujan',
                'Bangku taman warga rusak/patah',
                'Cermin cembung tikungan pecah/buram',
            ],
            'suggested_dampak' => 'rendah',
            'suggested_urgensi' => 'sedang',
        ];
    }

    /**
     * Resolusi identitas warga, inisial avatar, dan hierarki wilayah (RT/RW).
     *
     * @return array{
     *     namaPanggilan: string,
     *     initials: string,
     *     rtNomor: string,
     *     rwNomor: string,
     *     kelurahan: string,
     *     tanggalHariIni: string
     * }
     */
    protected function resolveProfilWarga(?User $user): array
    {
        $rtNomor = '02';
        $rwNomor = '05';
        $kelurahan = 'Sukamaju';

        if ($user && $user->wilayah) {
            if ($user->wilayah->tingkat === 'rt') {
                $rtNomor = $user->wilayah->nomor ?? '02';
                $rwNomor = $user->wilayah->parent?->nomor ?? '05';
                $kelurahan = $user->wilayah->parent?->kelurahan ?? $user->wilayah->kelurahan ?? 'Sukamaju';
            } else {
                $rwNomor = $user->wilayah->nomor ?? '05';
                $kelurahan = $user->wilayah->kelurahan ?? 'Sukamaju';
            }
        }

        $namaLengkap = $user->nama ?? 'Siti Aminah';
        $parts = explode(' ', trim($namaLengkap));
        $namaPanggilan = $parts[0];

        $initials = count($parts) >= 2
            ? strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1))
            : strtoupper(substr($namaLengkap, 0, 2));

        return [
            'namaPanggilan' => $namaPanggilan,
            'initials' => $initials,
            'rtNomor' => $rtNomor,
            'rwNomor' => $rwNomor,
            'kelurahan' => $kelurahan,
            'tanggalHariIni' => now()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ];
    }

    /**
     * Resolusi ID wilayah tingkat RT milik pengguna (dengan fallback ke RT pertama).
     */
    protected function resolveRtWilayahId(?User $user): ?string
    {
        if ($user && $user->wilayah) {
            if ($user->wilayah->tingkat === 'rt') {
                return $user->wilayah->id;
            }

            $childRtId = Wilayah::where('parent_id', $user->wilayah->id)
                ->where('tingkat', 'rt')
                ->value('id');

            if ($childRtId) {
                return $childRtId;
            }
        }

        return Wilayah::where('tingkat', 'rt')->value('id');
    }

    /**
     * Resolusi daftar area lingkungan (jalan/gang/blok/patokan) secara dinamis per RT:
     * menggabungkan data master `area_lingkungans` dengan area historis dari laporan RT terkait.
     *
     * @return list<array{nama: string, label: string, is_master: bool}>
     */
    protected function resolveAreaLingkungan(?string $wilayahId): array
    {
        if (! $wilayahId) {
            return [];
        }

        $jenisLabels = [
            'jalan' => 'Jalan',
            'gang' => 'Gang',
            'blok' => 'Blok',
            'area' => 'Area RT',
        ];

        $masterAreas = AreaLingkungan::where('wilayah_id', $wilayahId)
            ->where('aktif', true)
            ->orderBy('created_at')
            ->get(['nama', 'jenis'])
            ->map(fn (AreaLingkungan $area): array => [
                'nama' => trim($area->nama),
                'label' => $jenisLabels[$area->jenis] ?? 'Area RT',
                'is_master' => true,
            ]);

        $reportedAreas = Report::where('wilayah_id', $wilayahId)
            ->whereNotNull('blok')
            ->where('blok', '!=', '')
            ->latest('created_at')
            ->pluck('blok')
            ->map(fn (string $nama): array => [
                'nama' => trim($nama),
                'label' => 'Riwayat',
                'is_master' => false,
            ]);

        return $masterAreas
            ->merge($reportedAreas)
            ->filter(fn (array $item): bool => $item['nama'] !== '')
            ->unique(fn (array $item): string => strtolower($item['nama']))
            ->values()
            ->all();
    }

    /**
     * Ambil daftar lokasi detail unik dari laporan terbaru RT untuk autocomplete lokasi.
     *
     * @return list<string>
     */
    protected function resolveRiwayatLokasi(?string $wilayahId): array
    {
        if (! $wilayahId) {
            return [];
        }

        return Report::where('wilayah_id', $wilayahId)
            ->whereNotNull('lokasi_detail')
            ->where('lokasi_detail', '!=', '')
            ->latest('created_at')
            ->pluck('lokasi_detail')
            ->map(fn (string $lokasi): string => trim($lokasi))
            ->filter()
            ->unique(fn (string $lokasi): string => strtolower($lokasi))
            ->take(10)
            ->values()
            ->all();
    }

    /**
     * Pengambilan notifikasi untuk dropdown topbar.
     *
     * @return array{list: Collection<int, Notifikasi>, unread_count: int}
     */
    protected function getNotifikasiWarga(?User $user, int $limit = 8): array
    {
        if (! $user) {
            return [
                'list' => new Collection,
                'unread_count' => 0,
            ];
        }

        $list = Notifikasi::where('penerima_id', $user->id)
            ->latest('created_at')
            ->take($limit)
            ->get();

        $unreadCount = Notifikasi::where('penerima_id', $user->id)
            ->whereNull('dibaca_at')
            ->count();

        return [
            'list' => $list,
            'unread_count' => $unreadCount,
        ];
    }
}
