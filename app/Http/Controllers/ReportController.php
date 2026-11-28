<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\AreaLingkungan;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Notifikasi;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman daftar & pelacakan laporan milik Warga (Laporan Saya).
     *
     * Logika modular untuk presentasi:
     * 1. Hitung ringkasan jumlah per status (Semua, Menunggu, Diproses, Konfirmasi, Selesai, Ditolak)
     * 2. Terapkan filter status, kategori, kata kunci pencarian, dan urutan
     * 3. Muat relasi kasus, penugasan, progres petugas, lampiran foto, dan riwayat review
     * 4. Susun timeline kronologis per laporan agar siap ditampilkan di modal detail
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['wilayah.parent']);
        }

        $profil = $this->resolveProfilWarga($user);
        $kategoris = $this->getFormattedKategoris();

        $activeStatus = (string) $request->query('status', 'semua');
        $allowedStatuses = ['semua', 'menunggu', 'diproses', 'konfirmasi', 'selesai', 'ditolak'];
        if (! in_array($activeStatus, $allowedStatuses, true)) {
            $activeStatus = 'semua';
        }

        $activeKategori = (string) $request->query('kategori', 'semua');
        $search = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', 'terbaru');

        // 1. Hitung agregasi jumlah per tab filter (langsung dari database)
        $tabCounts = [
            'semua' => Report::milikPelapor($user->id)->count(),
            'menunggu' => Report::milikPelapor($user->id)->filterStatus('menunggu')->count(),
            'diproses' => Report::milikPelapor($user->id)->filterStatus('diproses')->count(),
            'konfirmasi' => Report::milikPelapor($user->id)->filterStatus('konfirmasi')->count(),
            'selesai' => Report::milikPelapor($user->id)->filterStatus('selesai')->count(),
            'ditolak' => Report::milikPelapor($user->id)->filterStatus('ditolak')->count(),
        ];

        // 2. Query daftar laporan dengan filter dinamis & eager loading lengkap
        $query = Report::milikPelapor($user->id)
            ->with([
                'kategori',
                'lampirans',
                'wilayah.parent',
                'reviews.pemeriksa',
                'kasus.koordinator',
                'kasus.penugasans.petugas',
                'kasus.penugasans.progres.pencatat',
                'kasus.penugasans.progres.lampirans',
                'kasus.reviews.pemeriksa',
            ])
            ->filterStatus($activeStatus)
            ->when($activeKategori !== 'semua' && Str::isUuid($activeKategori), function (Builder $q) use ($activeKategori): void {
                $q->where('kategori_id', $activeKategori);
            })
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('lokasi_detail', 'like', "%{$search}%")
                        ->orWhere('blok', 'like', "%{$search}%")
                        ->orWhere('uraian', 'like', "%{$search}%");
                });
            });

        if ($sort === 'terlama') {
            $query->oldest('created_at');
        } elseif ($sort === 'urgensi') {
            $query->orderByRaw("CASE urgensi WHEN 'mendesak' THEN 1 WHEN 'tinggi' THEN 2 WHEN 'sedang' THEN 3 ELSE 4 END")
                ->latest('created_at');
        } else {
            $query->latest('created_at');
        }

        $laporans = $query->paginate(8)->withQueryString();

        // 3. Susun payload detail & timeline untuk modal interaktif
        $reportDetailMap = [];
        foreach ($laporans as $lap) {
            $reportDetailMap[$lap->id] = $this->buildReportDetailPayload($lap, $kategoris);
        }

        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.laporan_saya', array_merge($profil, [
            'user' => $user,
            'laporans' => $laporans,
            'kategoris' => $kategoris,
            'tabCounts' => $tabCounts,
            'activeStatus' => $activeStatus,
            'activeKategori' => $activeKategori,
            'search' => $search,
            'sort' => $sort,
            'totalLaporanSaya' => $tabCounts['semua'],
            'reportDetailMap' => $reportDetailMap,
            'notifikasis' => $notifikasiData['list'],
            'unreadNotifikasiCount' => $notifikasiData['unread_count'],
        ]));
    }

    /**
     * Konfirmasi hasil penanganan oleh Warga (Tuntas atau Belum Tuntas / Buka Kembali).
     */
    public function confirmResolution(Request $request, Report $report): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || $report->pelapor_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengonfirmasi laporan ini.');
        }

        $report->loadMissing('kasus');
        if (! $report->kasus || $report->kasus->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Laporan ini belum berada pada tahap menunggu konfirmasi warga.');
        }

        $validated = $request->validate([
            'keputusan' => ['required', 'string', 'in:tuntas,belum_tuntas'],
            'catatan' => ['required_if:keputusan,belum_tuntas', 'nullable', 'string', 'max:500'],
        ], [
            'keputusan.required' => 'Pilih keputusan konfirmasi Anda.',
            'keputusan.in' => 'Pilihan konfirmasi tidak valid.',
            'catatan.required_if' => 'Mohon jelaskan bagian yang masih belum tuntas agar petugas dapat menindaklanjuti kembali.',
            'catatan.max' => 'Catatan konfirmasi maksimal :max karakter.',
        ]);

        $keputusan = $validated['keputusan'];
        $catatan = filled($validated['catatan'] ?? null) ? trim((string) $validated['catatan']) : null;

        DB::transaction(function () use ($user, $report, $keputusan, $catatan): void {
            $kasus = $report->kasus;

            Review::create([
                'jenis' => 'konfirmasi_warga',
                'laporan_id' => $report->id,
                'kasus_id' => $kasus->id,
                'pemeriksa_id' => $user->id,
                'keputusan' => $keputusan,
                'catatan' => $catatan,
            ]);

            if ($keputusan === 'tuntas') {
                $kasus->update([
                    'status' => 'selesai',
                    'selesai_at' => now(),
                ]);
            } else {
                $kasus->update([
                    'status' => 'dibuka_kembali',
                    'selesai_at' => null,
                ]);
            }

            if ($kasus->koordinator_id) {
                Notifikasi::create([
                    'penerima_id' => $kasus->koordinator_id,
                    'laporan_id' => $report->id,
                    'kasus_id' => $kasus->id,
                    'judul' => $keputusan === 'tuntas'
                        ? 'Warga Mengonfirmasi Masalah Tuntas'
                        : 'Warga Meminta Penanganan Ulang',
                    'pesan' => $keputusan === 'tuntas'
                        ? "{$user->nama} mengonfirmasi laporan {$report->kode} telah selesai ditangani dengan baik."
                        : "{$user->nama} melaporkan kasus {$kasus->kode} masih belum tuntas: ".($catatan ?? 'Perlu pengecekan kembali.'),
                    'dikirim_at' => now(),
                ]);
            }
        });

        $flashMessage = $keputusan === 'tuntas'
            ? "Terima kasih! Laporan {$report->kode} telah resmi dinyatakan Tuntas."
            : "Permintaan penanganan ulang untuk laporan {$report->kode} telah dikirim ke pengurus.";

        return redirect()
            ->route('warga.laporan.index')
            ->with('success', $flashMessage);
    }

    /**
     * Tampilkan Peta Sebaran Masalah Lingkungan (Dinamis per RT atau satu RW).
     *
     * Logika modular:
     * 1. Resolusi wilayah target berdasarkan lingkup (RT warga atau seluruh RW)
     * 2. Pengambilan daftar area lingkungan (jalan/gang/patokan) sebagai label zona peta
     * 3. Query laporan aktif (tidak ditolak) dengan filter kategori, status, dan pencarian
     * 4. Pemetaan titik koordinat peta (GPS asli jika ada, atau zona area deterministik)
     */
    public function peta(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['wilayah.parent']);
        }

        $profil = $this->resolveProfilWarga($user);
        $kategoris = $this->getFormattedKategoris();

        $scope = (string) $request->query('scope', 'rt');
        if (! in_array($scope, ['rt', 'rw'], true)) {
            $scope = 'rt';
        }

        $activeKategori = (string) $request->query('kategori', 'semua');
        $activeStatus = (string) $request->query('status', 'semua');
        if (! in_array($activeStatus, ['semua', 'menunggu', 'diproses', 'konfirmasi', 'selesai'], true)) {
            $activeStatus = 'semua';
        }
        $search = trim((string) $request->query('q', ''));

        $rtWilayahId = $this->resolveRtWilayahId($user);
        $rwWilayahId = $user?->wilayah?->tingkat === 'rw'
            ? $user->wilayah->id
            : ($user?->wilayah?->parent_id ?? Wilayah::where('tingkat', 'rw')->value('id'));

        $targetWilayahIds = [];
        if ($scope === 'rw' && $rwWilayahId) {
            $targetWilayahIds = Wilayah::where('parent_id', $rwWilayahId)
                ->where('tingkat', 'rt')
                ->pluck('id')
                ->all();
        } elseif ($rtWilayahId) {
            $targetWilayahIds = [$rtWilayahId];
        }

        // Ambil area lingkungan dinamis dari database untuk label zona di denah peta
        $areaNames = ! empty($targetWilayahIds)
            ? AreaLingkungan::whereIn('wilayah_id', $targetWilayahIds)
                ->where('aktif', true)
                ->orderBy('created_at')
                ->pluck('nama')
                ->unique()
                ->values()
                ->all()
            : [];

        // Query laporan di wilayah target (kecuali yang ditolak)
        $laporans = Report::query()
            ->when(! empty($targetWilayahIds), fn (Builder $q): Builder => $q->whereIn('wilayah_id', $targetWilayahIds))
            ->where('status_penerimaan', '!=', 'ditolak')
            ->with([
                'kategori',
                'wilayah',
                'lampirans',
                'pelapor',
                'reviews.pemeriksa',
                'kasus.koordinator',
                'kasus.penugasans.petugas',
                'kasus.penugasans.progres.pencatat',
                'kasus.reviews.pemeriksa',
            ])
            ->when($activeStatus !== 'semua', fn (Builder $q): Builder => $q->filterStatus($activeStatus))
            ->when($activeKategori !== 'semua' && Str::isUuid($activeKategori), function (Builder $q) use ($activeKategori): void {
                $q->where('kategori_id', $activeKategori);
            })
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('lokasi_detail', 'like', "%{$search}%")
                        ->orWhere('blok', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->take(40)
            ->get();

        // Hitung koordinat relatif & payload modal detail untuk setiap titik
        $mapMarkers = [];
        $reportDetailMap = [];
        $gpsBounds = $this->computeGpsBounds($laporans);

        foreach ($laporans as $lap) {
            $coords = $this->resolveMapCoordinates($lap, $gpsBounds);
            $katMeta = $kategoris->firstWhere('id', $lap->kategori_id);

            $mapMarkers[] = [
                'id' => $lap->id,
                'kode' => $lap->kode,
                'judul' => $lap->judul,
                'lokasi_detail' => $lap->lokasi_detail,
                'blok' => $lap->blok,
                'rt_nomor' => $lap->wilayah?->nomor ?? $profil['rtNomor'],
                'kategori_id' => $lap->kategori_id,
                'kategori_nama' => $katMeta?->short_name ?? ($lap->kategori?->nama ?? 'Umum'),
                'warna' => $lap->kategori?->warna_peta ?? '#2D6A4F',
                'status_label' => $lap->status_label,
                'status_badge' => $lap->status_badge,
                'urgensi' => $lap->urgensi,
                'is_priority' => in_array($lap->urgensi, ['tinggi', 'mendesak'], true),
                'waktu' => $lap->created_at->diffForHumans(),
                'x' => $coords['x'],
                'y' => $coords['y'],
            ];

            $reportDetailMap[$lap->id] = $this->buildReportDetailPayload($lap, $kategoris);
        }

        $totalLaporanSaya = $user ? Report::where('pelapor_id', $user->id)->count() : 0;
        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.peta_lingkungan', array_merge($profil, [
            'user' => $user,
            'laporans' => $laporans,
            'kategoris' => $kategoris,
            'scope' => $scope,
            'activeKategori' => $activeKategori,
            'activeStatus' => $activeStatus,
            'search' => $search,
            'areaNames' => $areaNames,
            'mapMarkers' => $mapMarkers,
            'reportDetailMap' => $reportDetailMap,
            'totalLaporanSaya' => $totalLaporanSaya,
            'notifikasis' => $notifikasiData['list'],
            'unreadNotifikasiCount' => $notifikasiData['unread_count'],
        ]));
    }

    /**
     * Tampilkan formulir pembuatan laporan baru untuk Warga.
     *
     * Data dimuat secara dinamis dari database:
     * 1. Profil dan hierarki wilayah warga (RT & RW)
     * 2. Daftar kategori aktif yang dipetakan ke ikon UI
     * 3. Pilihan area lingkungan & riwayat lokasi per RT
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
        $kategoris = $this->getFormattedKategoris();

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
     * Ambil daftar kategori aktif lengkap dengan metadata visual & urutan standar UI.
     */
    protected function getFormattedKategoris(): Collection
    {
        return Kategori::where('aktif', true)
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
    }

    /**
     * Susun payload lengkap detail laporan & timeline penanganan kronologis untuk modal.
     *
     * @return array<string, mixed>
     */
    protected function buildReportDetailPayload(Report $report, Collection $kategoris): array
    {
        $kategoriMeta = $kategoris->firstWhere('id', $report->kategori_id);
        $dampakLabels = ['rendah' => 'Ringan', 'sedang' => 'Sedang', 'tinggi' => 'Berat'];
        $urgensiLabels = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'mendesak' => 'Darurat'];

        $photos = $report->lampirans->map(function (Lampiran $lampiran): array {
            return [
                'id' => $lampiran->id,
                'name' => $lampiran->nama_file,
                'url' => Storage::disk('public')->exists($lampiran->storage_key)
                    ? asset('storage/'.$lampiran->storage_key)
                    : null,
            ];
        })->filter(fn (array $p): bool => $p['url'] !== null)->values()->all();

        $timeline = [
            [
                'timestamp' => $report->created_at->timestamp,
                'time_label' => $report->created_at->locale('id')->isoFormat('D MMM Y • HH:mm'),
                'title' => 'Laporan Dikirim',
                'actor' => $report->pelapor?->nama ?? 'Warga Pelapor',
                'note' => 'Laporan masuk ke antrean verifikasi pengurus RT.',
                'tone' => 'emerald',
            ],
        ];

        foreach ($report->reviews as $review) {
            $keputusanText = match ($review->keputusan) {
                'diterima' => 'Laporan Diverifikasi & Diterima',
                'ditolak' => 'Laporan Ditolak',
                'perlu_perbaikan' => 'Perlu Perbaikan Data',
                'tuntas' => 'Warga Mengonfirmasi Tuntas',
                'belum_tuntas' => 'Warga Meminta Penanganan Ulang',
                default => 'Pemeriksaan Pengurus',
            };

            $timeline[] = [
                'timestamp' => $review->created_at?->timestamp ?? $report->created_at->timestamp,
                'time_label' => $review->created_at?->locale('id')->isoFormat('D MMM Y • HH:mm') ?? '-',
                'title' => $keputusanText,
                'actor' => $review->pemeriksa?->nama ?? 'Pengurus Lingkungan',
                'note' => $review->catatan ?: 'Pemeriksaan selesai dicatat.',
                'tone' => $review->keputusan === 'ditolak' || $review->keputusan === 'belum_tuntas' ? 'amber' : 'blue',
            ];
        }

        if ($report->kasus) {
            $kasus = $report->kasus;

            $timeline[] = [
                'timestamp' => $kasus->created_at->timestamp,
                'time_label' => $kasus->created_at->locale('id')->isoFormat('D MMM Y • HH:mm'),
                'title' => "Ditindaklanjuti dalam Kasus {$kasus->kode}",
                'actor' => $kasus->koordinator?->nama ?? 'Koordinator Lingkungan',
                'note' => $kasus->judul,
                'tone' => 'blue',
            ];

            foreach ($kasus->penugasans as $penugasan) {
                $timeline[] = [
                    'timestamp' => ($penugasan->ditugaskan_at ?? $penugasan->created_at)->timestamp,
                    'time_label' => ($penugasan->ditugaskan_at ?? $penugasan->created_at)->locale('id')->isoFormat('D MMM Y • HH:mm'),
                    'title' => 'Petugas Lapangan Ditugaskan',
                    'actor' => $penugasan->petugas?->nama ?? 'Petugas Lapangan',
                    'note' => $penugasan->instruksi ?: 'Petugas ditugaskan menuju lokasi.',
                    'tone' => 'blue',
                ];

                foreach ($penugasan->progres as $prog) {
                    $progTitle = match ($prog->jenis) {
                        'penyelesaian' => 'Penyelesaian Lapangan Dicatat',
                        'kendala' => 'Kendala Lapangan Dilaporkan',
                        default => 'Pembaruan Progres Lapangan',
                    };
                    $timeline[] = [
                        'timestamp' => $prog->created_at?->timestamp ?? now()->timestamp,
                        'time_label' => $prog->created_at?->locale('id')->isoFormat('D MMM Y • HH:mm') ?? '-',
                        'title' => $progTitle,
                        'actor' => $prog->pencatat?->nama ?? 'Petugas',
                        'note' => $prog->catatan,
                        'tone' => $prog->jenis === 'kendala' ? 'amber' : 'emerald',
                    ];
                }
            }

            foreach ($kasus->reviews as $kasusReview) {
                if ($kasusReview->laporan_id === $report->id) {
                    continue;
                }
                $timeline[] = [
                    'timestamp' => $kasusReview->created_at?->timestamp ?? now()->timestamp,
                    'time_label' => $kasusReview->created_at?->locale('id')->isoFormat('D MMM Y • HH:mm') ?? '-',
                    'title' => $kasusReview->keputusan === 'tuntas' ? 'Konfirmasi Selesai' : 'Catatan Penanganan Kasus',
                    'actor' => $kasusReview->pemeriksa?->nama ?? 'Pengurus / Warga',
                    'note' => $kasusReview->catatan ?: '-',
                    'tone' => $kasusReview->keputusan === 'belum_tuntas' ? 'amber' : 'emerald',
                ];
            }
        }

        usort($timeline, fn (array $a, array $b): int => $a['timestamp'] <=> $b['timestamp']);

        return [
            'id' => $report->id,
            'kode' => $report->kode,
            'judul' => $report->judul,
            'uraian' => $report->uraian,
            'kategori' => $kategoriMeta?->short_name ?? ($report->kategori?->nama ?? 'Umum'),
            'kategori_full' => $report->kategori?->nama ?? 'Umum',
            'kategori_warna' => $report->kategori?->warna_peta ?? '#2D6A4F',
            'lokasi_detail' => $report->lokasi_detail,
            'blok' => $report->blok,
            'dampak' => $dampakLabels[$report->dampak] ?? ucfirst($report->dampak),
            'urgensi' => $urgensiLabels[$report->urgensi] ?? ucfirst($report->urgensi),
            'status_label' => $report->status_label,
            'status_badge' => $report->status_badge,
            'progress_step' => $report->progress_step,
            'tanggal' => $report->created_at->locale('id')->isoFormat('dddd, D MMMM Y • HH:mm'),
            'kasus_kode' => $report->kasus?->kode,
            'kasus_judul' => $report->kasus?->judul,
            'koordinator' => $report->kasus?->koordinator?->nama,
            'needs_confirmation' => $report->status_penerimaan === 'diverifikasi'
                && $report->kasus?->status === 'menunggu_konfirmasi',
            'confirm_url' => route('warga.laporan.confirm', $report),
            'photos' => $photos,
            'timeline' => $timeline,
        ];
    }

    /**
     * Hitung batas minimum/maksimum koordinat GPS jika ada laporan yang memiliki lat/lng.
     *
     * @return array{minLat: float, maxLat: float, minLng: float, maxLng: float}|null
     */
    protected function computeGpsBounds(Collection $reports): ?array
    {
        $withCoords = $reports->filter(fn (Report $r): bool => $r->latitude !== null && $r->longitude !== null);
        if ($withCoords->isEmpty()) {
            return null;
        }

        return [
            'minLat' => (float) $withCoords->min('latitude'),
            'maxLat' => (float) $withCoords->max('latitude'),
            'minLng' => (float) $withCoords->min('longitude'),
            'maxLng' => (float) $withCoords->max('longitude'),
        ];
    }

    /**
     * Tentukan posisi persentase (x%, y%) pin laporan di atas denah peta lingkungan.
     *
     * @param  array{minLat: float, maxLat: float, minLng: float, maxLng: float}|null  $gpsBounds
     * @return array{x: float, y: float}
     */
    protected function resolveMapCoordinates(Report $report, ?array $gpsBounds): array
    {
        if ($report->latitude !== null && $report->longitude !== null && $gpsBounds !== null) {
            $latSpan = $gpsBounds['maxLat'] - $gpsBounds['minLat'];
            $lngSpan = $gpsBounds['maxLng'] - $gpsBounds['minLng'];

            $normX = $lngSpan > 0.0001
                ? (((float) $report->longitude - $gpsBounds['minLng']) / $lngSpan)
                : 0.5;
            $normY = $latSpan > 0.0001
                ? (1 - (((float) $report->latitude - $gpsBounds['minLat']) / $latSpan))
                : 0.5;

            return [
                'x' => round(16 + ($normX * 68), 2),
                'y' => round(18 + ($normY * 62), 2),
            ];
        }

        // Zona skematis lingkungan (Barat-Utara, Tengah-Utara, Taman, Barat-Selatan, Pos Ronda, Kali/Selatan)
        $zones = [
            0 => ['x' => 23, 'y' => 24],
            1 => ['x' => 48, 'y' => 23],
            2 => ['x' => 72, 'y' => 27],
            3 => ['x' => 24, 'y' => 58],
            4 => ['x' => 49, 'y' => 55],
            5 => ['x' => 74, 'y' => 63],
        ];

        $text = strtolower(trim(($report->blok ?? '').' '.$report->lokasi_detail));
        $zoneIndex = abs(crc32($report->id)) % count($zones);

        if (str_contains($text, 'blok a') || str_contains($text, 'utama') || str_contains($text, 'mawar')) {
            $zoneIndex = 0;
        } elseif (str_contains($text, 'gang 1') || str_contains($text, 'musala') || str_contains($text, 'kenanga')) {
            $zoneIndex = 1;
        } elseif (str_contains($text, 'taman') || str_contains($text, 'tps') || str_contains($text, 'lapangan')) {
            $zoneIndex = 2;
        } elseif (str_contains($text, 'gang 2') || str_contains($text, 'timur') || str_contains($text, 'barat')) {
            $zoneIndex = 3;
        } elseif (str_contains($text, 'pos') || str_contains($text, 'blok b') || str_contains($text, 'melati')) {
            $zoneIndex = 4;
        } elseif (str_contains($text, 'kali') || str_contains($text, 'jembatan') || str_contains($text, 'blok c') || str_contains($text, 'saluran')) {
            $zoneIndex = 5;
        }

        $hash = abs(crc32($report->id.$report->kode));
        $jitterX = (($hash % 13) - 6);
        $jitterY = ((($hash >> 4) % 11) - 5);

        return [
            'x' => max(14, min(84, $zones[$zoneIndex]['x'] + $jitterX)),
            'y' => max(16, min(78, $zones[$zoneIndex]['y'] + $jitterY)),
        ];
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
