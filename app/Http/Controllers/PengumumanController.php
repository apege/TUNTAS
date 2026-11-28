<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    /**
     * Tampilkan papan pengumuman resmi wilayah RT & RW untuk Warga.
     *
     * Logika modular:
     * 1. Resolusi wilayah RT dan RW induk milik warga
     * 2. Hitung jumlah pengumuman diterbitkan per jenis (Semua, Darurat, Informasi, Kegiatan, Imbauan)
     * 3. Terapkan filter jenis, cakupan (RT & RW / RT / RW), dan pencarian kata kunci
     * 4. Susun payload detail pengumuman untuk modal baca lengkap
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing(['wilayah.parent']);
        }

        $profil = $this->resolveProfilWarga($user);

        $rtWilayahId = $user?->wilayah?->tingkat === 'rt'
            ? $user->wilayah->id
            : ($user?->wilayah?->id ? Wilayah::where('parent_id', $user->wilayah->id)->where('tingkat', 'rt')->value('id') : Wilayah::where('tingkat', 'rt')->value('id'));

        $rwWilayahId = $user?->wilayah?->tingkat === 'rw'
            ? $user->wilayah->id
            : ($user?->wilayah?->parent_id ?? Wilayah::where('tingkat', 'rw')->value('id'));

        $scope = (string) $request->query('scope', 'semua');
        if (! in_array($scope, ['semua', 'rt', 'rw'], true)) {
            $scope = 'semua';
        }

        $activeJenis = (string) $request->query('jenis', 'semua');
        if (! in_array($activeJenis, ['semua', 'darurat', 'informasi', 'kegiatan', 'imbauan'], true)) {
            $activeJenis = 'semua';
        }

        $search = trim((string) $request->query('q', ''));

        $allWilayahIds = array_values(array_filter([$rtWilayahId, $rwWilayahId]));
        $scopedWilayahIds = match ($scope) {
            'rt' => array_values(array_filter([$rtWilayahId])),
            'rw' => array_values(array_filter([$rwWilayahId])),
            default => $allWilayahIds,
        };

        // 1. Hitung jumlah per jenis pada cakupan wilayah aktif
        $countQuery = Pengumuman::query()
            ->diterbitkan()
            ->when(! empty($scopedWilayahIds), fn (Builder $q): Builder => $q->untukWilayah($scopedWilayahIds));

        $tabCounts = [
            'semua' => (clone $countQuery)->count(),
            'darurat' => (clone $countQuery)->where('jenis', 'darurat')->count(),
            'informasi' => (clone $countQuery)->where('jenis', 'informasi')->count(),
            'kegiatan' => (clone $countQuery)->where('jenis', 'kegiatan')->count(),
            'imbauan' => (clone $countQuery)->where('jenis', 'imbauan')->count(),
        ];

        // 2. Query pengumuman dengan filter jenis, wilayah, dan pencarian
        $pengumumans = Pengumuman::query()
            ->diterbitkan()
            ->when(! empty($scopedWilayahIds), fn (Builder $q): Builder => $q->untukWilayah($scopedWilayahIds))
            ->when($activeJenis !== 'semua', fn (Builder $q): Builder => $q->where('jenis', $activeJenis))
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->with(['pembuat', 'wilayah'])
            ->orderByRaw("CASE jenis WHEN 'darurat' THEN 0 ELSE 1 END")
            ->latest('terbit_at')
            ->latest('created_at')
            ->paginate(8)
            ->withQueryString();

        // 3. Susun payload detail baca lengkap untuk modal
        $pengumumanDetailMap = [];
        foreach ($pengumumans as $item) {
            $publishedAt = $item->terbit_at ?? $item->created_at;
            $wilayahLabel = $item->wilayah?->tingkat === 'rw'
                ? 'RW '.($item->wilayah->nomor ?? '05')
                : 'RT '.($item->wilayah?->nomor ?? '01');

            $pengumumanDetailMap[$item->id] = [
                'id' => $item->id,
                'judul' => $item->judul,
                'isi' => $item->isi,
                'jenis' => $item->jenis,
                'jenis_badge' => $item->jenis_badge,
                'wilayah_label' => $wilayahLabel,
                'pembuat_nama' => $item->pembuat?->nama ?? 'Pengurus Lingkungan',
                'tanggal_lengkap' => $publishedAt->locale('id')->isoFormat('dddd, D MMMM Y • HH:mm').' WIB',
                'waktu_relatif' => $publishedAt->diffForHumans(),
            ];
        }

        $totalLaporanSaya = $user ? Report::where('pelapor_id', $user->id)->count() : 0;
        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.pengumuman', array_merge($profil, [
            'user' => $user,
            'pengumumans' => $pengumumans,
            'pengumumanDetailMap' => $pengumumanDetailMap,
            'tabCounts' => $tabCounts,
            'scope' => $scope,
            'activeJenis' => $activeJenis,
            'search' => $search,
            'totalLaporanSaya' => $totalLaporanSaya,
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
