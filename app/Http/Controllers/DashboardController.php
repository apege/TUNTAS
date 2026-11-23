<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk peran Warga (Dinamis dari Database).
     */
    public function warga(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['wilayah.parent']);
        }

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

        // Inisial untuk avatar, cth: "Siti Aminah" -> "SA"
        $initials = 'SA';
        if ($user && ! empty($user->nama)) {
            $words = explode(' ', trim($user->nama));
            $initials = strtoupper(substr($words[0], 0, 1).(isset($words[1]) ? substr($words[1], 0, 1) : ''));
        }

        $tanggalHariIni = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');

        // Parameter pencarian opsional dari input header
        $search = trim((string) $request->query('q', ''));

        // Query Dinamis Statistik Laporan Pribadi Warga
        $userReportsQuery = Report::query()
            ->when($user, fn ($q) => $q->where('pelapor_id', $user->id));

        $totalReports = (clone $userReportsQuery)->count();

        // 1. Menunggu Verifikasi: status_penerimaan = 'baru'
        $menungguVerifikasiCount = (clone $userReportsQuery)
            ->where('status_penerimaan', 'baru')
            ->count();

        // 2. Sedang Ditangani: status_penerimaan = 'diverifikasi' AND (belum masuk kasus atau kasus sedang berjalan)
        $sedangDitanganiCount = (clone $userReportsQuery)
            ->where('status_penerimaan', 'diverifikasi')
            ->where(function ($query) {
                $query->whereDoesntHave('kasus')
                    ->orWhereHas('kasus', function ($q) {
                        $q->whereIn('status', ['menunggu', 'diproses', 'menunggu_konfirmasi', 'dibuka_kembali']);
                    });
            })
            ->count();

        // 3. Selesai: status_penerimaan = 'diverifikasi' AND kasus.status = 'selesai'
        $selesaiCount = (clone $userReportsQuery)
            ->where('status_penerimaan', 'diverifikasi')
            ->whereHas('kasus', function ($q) {
                $q->where('status', 'selesai');
            })
            ->count();

        // Query Dinamis Laporan Terbaru Milik Warga
        $laporanTerbaru = (clone $userReportsQuery)
            ->with(['kategori', 'kasus'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('uraian', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->take(5)
            ->get();

        // Query Dinamis Pengumuman Wilayah (RT setempat & RW induk)
        $targetWilayahIds = array_filter([
            $user?->wilayah_id,
            $user?->wilayah?->parent_id,
        ]);

        $pengumumanTerbaru = Pengumuman::query()
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
            ->take(5)
            ->get();

        // Query Dinamis Notifikasi Pengguna
        $notifikasis = Notifikasi::query()
            ->when($user, fn ($q) => $q->where('penerima_id', $user->id))
            ->with(['laporan', 'pengumuman'])
            ->latest('created_at')
            ->take(8)
            ->get();

        $unreadNotifikasiCount = $user
            ? Notifikasi::where('penerima_id', $user->id)->whereNull('dibaca_at')->count()
            : 0;

        return view('dashboard.warga', [
            'user' => $user,
            'namaPanggilan' => $namaPanggilan,
            'initials' => $initials,
            'rtNomor' => $rtNomor,
            'rwNomor' => $rwNomor,
            'kelurahan' => $kelurahan,
            'tanggalHariIni' => $tanggalHariIni,
            'search' => $search,
            'stats' => [
                'total' => $totalReports,
                'sedang_ditangani' => $sedangDitanganiCount,
                'menunggu_verifikasi' => $menungguVerifikasiCount,
                'selesai' => $selesaiCount,
            ],
            'laporanTerbaru' => $laporanTerbaru,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'notifikasis' => $notifikasis,
            'unreadNotifikasiCount' => $unreadNotifikasiCount,
        ]);
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
