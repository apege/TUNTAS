<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login TUNTAS.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->peran);
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey);

            // Sesuai PRD: Login gagal menampilkan pesan tanpa mengungkap apakah email atau password salah
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'login_failed' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $user = Auth::user();

        // Validasi akun aktif sesuai PRD 01
        if (! $user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withInput($request->only('email'))->withErrors([
                'login_failed' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi pengurus RT/RW Anda.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user->peran);
    }

    /**
     * Proses keluar (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar.');
    }

    /**
     * Arahkan pengguna ke dashboard sesuai perannya.
     */
    protected function redirectBasedOnRole(string $peran): RedirectResponse
    {
        return match ($peran) {
            'admin_rt' => redirect()->intended(route('admin-rt.dashboard')),
            'admin_rw' => redirect()->intended(route('admin-rw.dashboard')),
            'petugas' => redirect()->intended(route('petugas.dashboard')),
            default => redirect()->intended(route('warga.dashboard')),
        };
    }
}
