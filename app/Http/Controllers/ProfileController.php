<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman Profil Saya untuk Warga.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing(['wilayah.parent']);

        $profil = $this->resolveProfilWarga($user);
        $totalLaporanSaya = Report::where('pelapor_id', $user->id)->count();
        $laporanSelesaiCount = Report::where('pelapor_id', $user->id)
            ->whereHas('kasus', fn ($query) => $query->where('status', 'selesai'))
            ->count();

        $notifikasiData = $this->getNotifikasiWarga($user, limit: 8);

        return view('dashboard.profil_saya', array_merge($profil, [
            'user' => $user,
            'totalLaporanSaya' => $totalLaporanSaya,
            'laporanSelesaiCount' => $laporanSelesaiCount,
            'notifikasis' => $notifikasiData['list'],
            'unreadNotifikasiCount' => $notifikasiData['unread_count'],
        ]));
    }

    /**
     * Perbarui informasi profil, kontak, dan preferensi pemberitahuan warga.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'notifikasi_aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama lengkap minimal terdiri dari 2 karakter.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan oleh akun lain.',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda +, -, atau tanda kurung.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
        ]);

        $user->update([
            'nama' => trim($validated['nama']),
            'email' => strtolower(trim($validated['email'])),
            'telepon' => isset($validated['telepon']) && trim($validated['telepon']) !== '' ? trim($validated['telepon']) : null,
            'alamat' => isset($validated['alamat']) && trim($validated['alamat']) !== '' ? trim($validated['alamat']) : null,
            'notifikasi_aktif' => $request->boolean('notifikasi_aktif'),
        ]);

        return redirect()
            ->route('warga.profil.index')
            ->with('success', 'Informasi profil dan preferensi pemberitahuan berhasil disimpan.');
    }

    /**
     * Perbarui kata sandi akun warga dengan verifikasi kata sandi lama.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('warga.profil.index')
            ->with('success', 'Kata sandi akun berhasil diperbarui.');
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
    protected function resolveProfilWarga(User $user): array
    {
        $rtNomor = '02';
        $rwNomor = '05';
        $kelurahan = 'Sukamaju';

        if ($user->wilayah) {
            if ($user->wilayah->tingkat === 'rt') {
                $rtNomor = $user->wilayah->nomor ?? '02';
                $rwNomor = $user->wilayah->parent?->nomor ?? '05';
                $kelurahan = $user->wilayah->parent?->kelurahan ?? $user->wilayah->kelurahan ?? 'Sukamaju';
            } else {
                $rwNomor = $user->wilayah->nomor ?? '05';
                $kelurahan = $user->wilayah->kelurahan ?? 'Sukamaju';
            }
        }

        $namaLengkap = $user->nama ?: 'Warga TUNTAS';
        $parts = preg_split('/\s+/', trim($namaLengkap)) ?: [];
        $namaPanggilan = $parts[0] ?? 'Warga';

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
    protected function getNotifikasiWarga(User $user, int $limit = 8): array
    {
        $list = Notifikasi::where('penerima_id', $user->id)
            ->latest('created_at')
            ->limit($limit)
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
