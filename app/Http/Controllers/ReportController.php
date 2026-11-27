<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
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

        // 2. Kategori Aktif dari Database
        $kategoris = Kategori::where('aktif', true)
            ->orderBy('created_at')
            ->get()
            ->map(function (Kategori $kategori): Kategori {
                $meta = $this->resolveKategoriMeta($kategori->nama);
                $kategori->short_name = $meta['short_name'];
                $kategori->icon_type = $meta['icon_type'];

                return $kategori;
            });

        // 3. Pilihan Area Lingkungan / Blok
        $daftarBlok = [
            'Blok A',
            'Blok B',
            'Blok C',
            'Blok D',
            'Lainnya / Luar Blok',
        ];

        // 4. Jumlah Laporan Pengguna (untuk badge sidebar)
        $totalLaporanSaya = $user ? Report::where('pelapor_id', $user->id)->count() : 0;

        // 5. Notifikasi Pengguna
        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.buat_laporan', array_merge($profil, [
            'user' => $user,
            'kategoris' => $kategoris,
            'daftarBlok' => $daftarBlok,
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
                'blok' => $validated['blok'] ?? null,
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
     * Pemetaan nama kategori database ke label singkat dan tipe ikon visual UI.
     *
     * @return array{short_name: string, icon_type: string}
     */
    protected function resolveKategoriMeta(string $namaKategori): array
    {
        $namaLower = strtolower($namaKategori);

        if (str_contains($namaLower, 'sampah')) {
            return ['short_name' => 'Sampah', 'icon_type' => 'sampah'];
        }

        if (str_contains($namaLower, 'drainase') || str_contains($namaLower, 'saluran') || str_contains($namaLower, 'air')) {
            return ['short_name' => 'Drainase', 'icon_type' => 'drainase'];
        }

        if (str_contains($namaLower, 'jalan') || str_contains($namaLower, 'trotoar')) {
            return ['short_name' => 'Jalan', 'icon_type' => 'jalan'];
        }

        if (str_contains($namaLower, 'penerangan') || str_contains($namaLower, 'lampu') || str_contains($namaLower, 'pju')) {
            return ['short_name' => 'Penerangan', 'icon_type' => 'penerangan'];
        }

        return ['short_name' => 'Fasilitas Umum', 'icon_type' => 'fasilitas'];
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
