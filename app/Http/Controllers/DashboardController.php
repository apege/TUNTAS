<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk peran Warga (Dinamis dari Database).
     *
     * Logika dashboard disusun modular agar mudah dipresentasikan:
     * 1. Resolusi Profil & Hierarki Wilayah Warga (RT & RW induk)
     * 2. Agregasi 4 Indikator Statistik Laporan Pribadi
     * 3. Pengambilan Riwayat Laporan Terkini dengan Filter Pencarian
     * 4. Pengambilan Pengumuman Resmi Multi-Tier (RT lokal + RW induk)
     * 5. Sinkronisasi Notifikasi Interaktif Real-Time
     */
    public function warga(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['wilayah.parent']);
        }

        $search = trim((string) $request->query('q', ''));

        // 1. Informasi Wilayah & Profil Warga
        $profil = $this->resolveProfilWarga($user);

        // 2. Statistik 4 Indikator Laporan Pribadi
        $stats = $this->resolveStatistikLaporan($user);

        // 3. Daftar Laporan Warga (Mendukung scroll & pencarian kata kunci)
        $laporanTerbaru = $this->getLaporanTerbaru($user, $search, limit: 10);

        // 4. Daftar Pengumuman Wilayah (Tingkat RT dan RW)
        $pengumumanTerbaru = $this->getPengumumanWilayah($user, $search, limit: 10);

        // 5. Notifikasi Interaktif Pengguna
        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.warga', array_merge($profil, [
            'user' => $user,
            'search' => $search,
            'stats' => $stats,
            'laporanTerbaru' => $laporanTerbaru,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'notifikasis' => $notifikasiData['list'],
            'unreadNotifikasiCount' => $notifikasiData['unread_count'],
        ]));
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

        $namaPanggilan = $user ? explode(' ', trim($user->nama))[0] : 'Siti';

        $initials = 'SA';
        if ($user && ! empty($user->nama)) {
            $words = explode(' ', trim($user->nama));
            $initials = strtoupper(substr($words[0], 0, 1).(isset($words[1]) ? substr($words[1], 0, 1) : ''));
        }

        $tanggalHariIni = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');

        return [
            'namaPanggilan' => $namaPanggilan,
            'initials' => $initials,
            'rtNomor' => $rtNomor,
            'rwNomor' => $rwNomor,
            'kelurahan' => $kelurahan,
            'tanggalHariIni' => $tanggalHariIni,
        ];
    }

    /**
     * Menghitung statistik 4 kartu status laporan warga secara real-time.
     *
     * @return array{
     *     total: int,
     *     sedang_ditangani: int,
     *     menunggu_verifikasi: int,
     *     selesai: int
     * }
     */
    protected function resolveStatistikLaporan(?User $user): array
    {
        $baseQuery = Report::query()
            ->when($user, fn ($q) => $q->where('pelapor_id', $user->id));

        $totalReports = (clone $baseQuery)->count();

        // Status 1: Menunggu Verifikasi (laporan baru dari warga belum diverifikasi pengurus)
        $menungguVerifikasiCount = (clone $baseQuery)
            ->where('status_penerimaan', 'baru')
            ->count();

        // Status 2: Sedang Ditangani (sudah diverifikasi dan kasus sedang dalam penanganan)
        $sedangDitanganiCount = (clone $baseQuery)
            ->where('status_penerimaan', 'diverifikasi')
            ->where(function ($query) {
                $query->whereDoesntHave('kasus')
                    ->orWhereHas('kasus', function ($q) {
                        $q->whereIn('status', ['menunggu', 'diproses', 'menunggu_konfirmasi', 'dibuka_kembali']);
                    });
            })
            ->count();

        // Status 3: Selesai (laporan diverifikasi & kasus telah rampung)
        $selesaiCount = (clone $baseQuery)
            ->where('status_penerimaan', 'diverifikasi')
            ->whereHas('kasus', function ($q) {
                $q->where('status', 'selesai');
            })
            ->count();

        return [
            'total' => $totalReports,
            'sedang_ditangani' => $sedangDitanganiCount,
            'menunggu_verifikasi' => $menungguVerifikasiCount,
            'selesai' => $selesaiCount,
        ];
    }

    /**
     * Mengambil daftar laporan terbaru milik warga dengan relasi kategori & kasus.
     */
    protected function getLaporanTerbaru(?User $user, string $search, int $limit = 10): Collection
    {
        return Report::query()
            ->when($user, fn ($q) => $q->where('pelapor_id', $user->id))
            ->with(['kategori', 'kasus'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('uraian', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Mengambil pengumuman relevan untuk warga berdasarkan wilayahnya (RT setempat & RW induk).
     */
    protected function getPengumumanWilayah(?User $user, string $search, int $limit = 10): Collection
    {
        $targetWilayahIds = array_filter([
            $user?->wilayah_id,
            $user?->wilayah?->parent_id,
        ]);

        return Pengumuman::query()
            ->where('status', 'diterbitkan')
            ->when(! empty($targetWilayahIds), function ($query) use ($targetWilayahIds) {
                $query->whereIn('wilayah_id', $targetWilayahIds);
            })
            ->with(['pembuat', 'wilayah'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->latest('terbit_at')
            ->take($limit)
            ->get();
    }

    /**
     * Mengambil daftar notifikasi pengguna dan menghitung jumlah notifikasi yang belum dibaca.
     *
     * @return array{list: Collection, unread_count: int}
     */
    protected function getNotifikasiWarga(?User $user, int $limit = 8): array
    {
        $list = Notifikasi::query()
            ->when($user, fn ($q) => $q->where('penerima_id', $user->id))
            ->with(['laporan', 'pengumuman'])
            ->latest('created_at')
            ->take($limit)
            ->get();

        $unreadCount = $user
            ? Notifikasi::where('penerima_id', $user->id)->whereNull('dibaca_at')->count()
            : 0;

        return [
            'list' => $list,
            'unread_count' => $unreadCount,
        ];
    }

    /**
     * Dashboard untuk peran Admin RT.
     */
    public function adminRt(): View
    {
        return view('dashboard.admin-rt', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk peran Admin RW.
     */
    public function adminRw(): View
    {
        return view('dashboard.admin-rw', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk peran Petugas.
     */
    public function petugas(): View
    {
        return view('dashboard.petugas', [
            'user' => Auth::user(),
        ]);
    }
}
