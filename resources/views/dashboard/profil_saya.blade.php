@extends('layouts.warga')

@section('title', 'Profil Saya — TUNTAS')
@section('header-title', 'Profil Saya')
@section('search-action', route('warga.laporan.index'))

@section('warga-content')
@php
    $joinedLabel = $user->created_at
        ? $user->created_at->locale('id')->isoFormat('MMMM Y')
        : 'Baru bergabung';
    $isNotifChecked = (bool) old('notifikasi_aktif', $user->notifikasi_aktif);
@endphp

<div class="space-y-5">

    <!-- ================= TOP BAR: SUBTITLE + ACCOUNT STATUS ================= -->
    <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-xs sm:text-sm text-[#4E6457] leading-relaxed">
            Sesuaikan informasi profil, detail kontak, kata sandi, dan preferensi pemberitahuan akun Anda.
        </p>

        <div class="clay-pill px-3.5 py-1.5 inline-flex items-center gap-2 text-[11px] font-bold text-[#1B4332] shrink-0 self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full {{ $user->aktif ? 'bg-[#16A34A]' : 'bg-[#DC2626]' }}"></span>
            <span>{{ $user->aktif ? 'Akun Aktif' : 'Nonaktif' }}</span>
            <span class="text-[#9AB0A3]">•</span>
            <span class="text-[#52645B]">Sejak {{ $joinedLabel }}</span>
        </div>
    </section>

    <!-- ================= PROFILE & NOTIFICATION FORM GRID ================= -->
    <form id="profil-form" action="{{ route('warga.profil.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

            <!-- LEFT COLUMN: PROFIL & KONTAK -->
            <section class="xl:col-span-8 clay-card-content p-5 sm:p-6">
                <!-- Card Header -->
                <div class="pb-4 border-b border-[#E3DDD1] mb-5">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                        INFORMASI AKUN
                    </span>
                    <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
                        Profil &amp; Kontak
                    </h3>
                </div>

                <!-- Live Profile Preview Strip -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-5 border-b border-[#E3DDD1]/80">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div id="preview-initials" class="clay-icon-mint w-14 h-14 flex items-center justify-center text-base font-black text-[#1B4332] shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <h4 id="preview-nama" class="text-base font-black text-[#122A1E] truncate">
                                {{ old('nama', $user->nama) }}
                            </h4>
                            <p class="text-xs text-[#52645B] mt-0.5 truncate">
                                Warga RT {{ $rtNomor }} / RW {{ $rwNomor }} • Kelurahan {{ $kelurahan }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span class="neu-pill-tag px-3 py-1 text-[11px] font-extrabold text-[#153425]">
                            {{ $totalLaporanSaya }} Laporan
                        </span>
                        <span class="neu-pill-tag px-3 py-1 text-[11px] font-extrabold text-[#166534]">
                            {{ $laporanSelesaiCount }} Selesai
                        </span>
                    </div>
                </div>

                <!-- Form Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="input-nama" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Nama lengkap <span class="text-[#B91C1C]">*</span>
                        </label>
                        <input
                            type="text"
                            id="input-nama"
                            name="nama"
                            value="{{ old('nama', $user->nama) }}"
                            required
                            maxlength="150"
                            placeholder="Masukkan nama lengkap Anda"
                            class="clay-input w-full px-4 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83]"
                        >
                        @error('nama')
                            <p class="text-[11px] font-bold text-[#B91C1C] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label for="input-telepon" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Nomor telepon <span class="text-[#63796E] font-semibold">(opsional)</span>
                        </label>
                        <input
                            type="tel"
                            id="input-telepon"
                            name="telepon"
                            value="{{ old('telepon', $user->telepon) }}"
                            maxlength="20"
                            placeholder="Contoh: 081234567890"
                            class="clay-input w-full px-4 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83]"
                        >
                        @error('telepon')
                            <p class="text-[11px] font-bold text-[#B91C1C] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Akun -->
                    <div>
                        <label for="input-email" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Email akun <span class="text-[#B91C1C]">*</span>
                        </label>
                        <input
                            type="email"
                            id="input-email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            maxlength="150"
                            placeholder="nama@email.com"
                            class="clay-input w-full px-4 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83]"
                        >
                        @error('email')
                            <p class="text-[11px] font-bold text-[#B91C1C] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Wilayah / Penugasan (Read-Only) -->
                    <div>
                        <span class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Wilayah domisili
                        </span>
                        <div class="clay-sunken-note px-4 py-3 flex items-center justify-between gap-2 text-sm font-bold text-[#30483C]">
                            <span class="truncate">Warga RT {{ $rtNomor }} / RW {{ $rwNomor }} ({{ $kelurahan }})</span>
                            <svg class="w-4 h-4 text-[#63796E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <p class="text-[10px] text-[#63796E] mt-1">
                            Diatur sesuai pencatatan wilayah pengurus RT/RW.
                        </p>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="md:col-span-2">
                        <label for="input-alamat" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Alamat lengkap <span class="text-[#63796E] font-semibold">(opsional)</span>
                        </label>
                        <textarea
                            id="input-alamat"
                            name="alamat"
                            rows="3"
                            maxlength="500"
                            placeholder="Contoh: Jl. Mawar Blok B-3 No. 12, RT {{ $rtNomor }} / RW {{ $rwNomor }}"
                            class="clay-input w-full px-4 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83] resize-none"
                        >{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-[11px] font-bold text-[#B91C1C] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="mt-6 pt-4 border-t border-[#E3DDD1] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p id="profil-dirty-status" class="text-xs font-semibold text-[#52645B]">
                        Data profil Anda sudah tersimpan.
                    </p>

                    <div class="flex items-center gap-2.5 self-end sm:self-auto">
                        <button
                            type="button"
                            id="btn-reset-profil"
                            class="clay-btn-cancel px-4 py-2.5 text-xs font-bold cursor-pointer"
                        >
                            Batalkan
                        </button>
                        <button
                            type="submit"
                            class="clay-btn-green px-5 py-2.5 text-xs font-bold text-white inline-flex items-center gap-2 cursor-pointer"
                        >
                            <span>Simpan Perubahan</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </section>

            <!-- RIGHT COLUMN: PREFERENSI & SYNC INFO -->
            <div class="xl:col-span-4 space-y-5">
                <!-- Preferensi Pemberitahuan -->
                <section class="clay-card-content p-5 sm:p-6">
                    <div class="pb-3.5 border-b border-[#E3DDD1] mb-4">
                        <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                            PREFERENSI
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
                            Pemberitahuan
                        </h3>
                    </div>

                    <input type="hidden" name="notifikasi_aktif" value="0">

                    <label for="input-notifikasi-aktif" class="flex items-start justify-between gap-4 cursor-pointer select-none">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs sm:text-sm font-extrabold text-[#122A1E]">
                                    Notifikasi aktivitas
                                </span>
                                <span id="notifikasi-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $isNotifChecked ? 'bg-[#D8F3DC] text-[#1B4332]' : 'bg-[#EAE4D8] text-[#52645B]' }}">
                                    {{ $isNotifChecked ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <p class="text-[11px] sm:text-xs text-[#52645B] mt-1 leading-relaxed">
                                Tampilkan ringkasan pembaruan status laporan dan pengumuman RT/RW di lonceng notifikasi header.
                            </p>
                        </div>

                        <div class="relative inline-flex items-center shrink-0 mt-0.5">
                            <input
                                type="checkbox"
                                id="input-notifikasi-aktif"
                                name="notifikasi_aktif"
                                value="1"
                                {{ $isNotifChecked ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-12 h-7 rounded-full bg-[#D8D1C4] shadow-inner transition-colors duration-200 peer-checked:bg-[#1B4332] peer-focus-visible:ring-2 peer-focus-visible:ring-[#2D6A4F]/40 after:content-[''] after:absolute after:top-1 after:left-1 after:w-5 after:h-5 after:rounded-full after:bg-white after:shadow-md after:transition-transform after:duration-200 peer-checked:after:translate-x-5"></div>
                        </div>
                    </label>
                </section>

                <!-- Sinkronisasi Akun -->
                <section class="clay-helper-card p-5 space-y-2.5">
                    <div class="flex items-center gap-3">
                        <div class="clay-icon-mint w-10 h-10 flex items-center justify-center text-[#1B4332] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-black text-[#122A1E]">Tersimpan di Akun TUNTAS</h4>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#4E6457] leading-relaxed">
                        Setiap pembaruan kontak dan preferensi disimpan permanen ke basis data akun Anda agar pengurus RT/RW dapat menghubungi Anda saat tindak lanjut laporan.
                    </p>
                </section>
            </div>

        </div>
    </form>

    <!-- ================= SECURITY (PASSWORD) & QUICK SHORTCUTS ================= -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

        <!-- LEFT: UBAH KATA SANDI -->
        <section class="xl:col-span-8 clay-card-content p-5 sm:p-6">
            <div class="pb-4 border-b border-[#E3DDD1] mb-5">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    KEAMANAN AKUN
                </span>
                <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
                    Ubah Kata Sandi
                </h3>
            </div>

            <form action="{{ route('warga.profil.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Kata sandi saat ini -->
                <div>
                    <label for="input-current-password" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                        Kata sandi saat ini <span class="text-[#B91C1C]">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="input-current-password"
                            name="current_password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi saat ini"
                            class="clay-input w-full pl-4 pr-12 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83]"
                        >
                        <button
                            type="button"
                            data-toggle-password="input-current-password"
                            class="clay-btn-toggle absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-[#52645B] hover:text-[#122A1E] cursor-pointer"
                            aria-label="Tampilkan kata sandi saat ini"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @if ($errors->passwordUpdate->has('current_password'))
                        <p class="text-[11px] font-bold text-[#B91C1C] mt-1.5">{{ $errors->passwordUpdate->first('current_password') }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kata sandi baru -->
                    <div>
                        <label for="input-new-password" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Kata sandi baru <span class="text-[#B91C1C]">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="input-new-password"
                                name="password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="clay-input w-full pl-4 pr-12 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83]"
                            >
                            <button
                                type="button"
                                data-toggle-password="input-new-password"
                                class="clay-btn-toggle absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-[#52645B] hover:text-[#122A1E] cursor-pointer"
                                aria-label="Tampilkan kata sandi baru"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @if ($errors->passwordUpdate->has('password'))
                            <p class="text-[11px] font-bold text-[#B91C1C] mt-1.5">{{ $errors->passwordUpdate->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Konfirmasi kata sandi baru -->
                    <div>
                        <label for="input-password-confirmation" class="block text-xs font-extrabold text-[#142D21] mb-1.5">
                            Konfirmasi kata sandi baru <span class="text-[#B91C1C]">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="input-password-confirmation"
                                name="password_confirmation"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Ulangi kata sandi baru"
                                class="clay-input w-full pl-4 pr-12 py-3 text-sm font-medium text-[#122A1E] placeholder-[#7C8E83]"
                            >
                            <button
                                type="button"
                                data-toggle-password="input-password-confirmation"
                                class="clay-btn-toggle absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-[#52645B] hover:text-[#122A1E] cursor-pointer"
                                aria-label="Tampilkan konfirmasi kata sandi"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-[#E3DDD1] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-[11px] sm:text-xs text-[#60776C]">
                        Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.
                    </p>
                    <button
                        type="submit"
                        class="clay-btn-green px-5 py-2.5 text-xs font-bold text-white inline-flex items-center justify-center gap-2 shrink-0 cursor-pointer"
                    >
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- RIGHT: AKSES CEPAT & SESI -->
        <section class="xl:col-span-4 clay-card-content p-5 sm:p-6 space-y-4">
            <div class="pb-3 border-b border-[#E3DDD1]">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    AKSES CEPAT
                </span>
                <h3 class="text-base font-black text-[#122A1E] mt-0.5">
                    Navigasi &amp; Sesi
                </h3>
            </div>

            <div class="space-y-2.5">
                <a href="{{ route('warga.laporan.index') }}" class="clay-item-card p-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="clay-icon-mint w-9 h-9 flex items-center justify-center text-[#1B4332] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-extrabold text-[#122A1E]">Laporan Saya</span>
                            <span class="block text-[11px] text-[#60776C]">{{ $totalLaporanSaya }} riwayat pengaduan</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-[#2D6A4F]">→</span>
                </a>

                <a href="{{ route('warga.pengumuman.index') }}" class="clay-item-card p-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="clay-icon-amber w-9 h-9 flex items-center justify-center text-[#B45309] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-extrabold text-[#122A1E]">Pengumuman Warga</span>
                            <span class="block text-[11px] text-[#60776C]">Info resmi RT {{ $rtNomor }} &amp; RW {{ $rwNomor }}</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-[#2D6A4F]">→</span>
                </a>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="pt-2 border-t border-[#E3DDD1]">
                @csrf
                <button type="submit" class="clay-pill w-full px-4 py-2.5 text-xs font-bold text-[#9E2A2B] inline-flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('profil-form');
    const namaInput = document.getElementById('input-nama');
    const emailInput = document.getElementById('input-email');
    const teleponInput = document.getElementById('input-telepon');
    const alamatInput = document.getElementById('input-alamat');
    const notifToggle = document.getElementById('input-notifikasi-aktif');
    const notifBadge = document.getElementById('notifikasi-status-badge');
    const previewNama = document.getElementById('preview-nama');
    const previewInitials = document.getElementById('preview-initials');
    const dirtyStatus = document.getElementById('profil-dirty-status');
    const resetBtn = document.getElementById('btn-reset-profil');

    if (form && namaInput && emailInput && teleponInput && alamatInput && notifToggle) {
        const initialSnapshot = {
            nama: namaInput.value,
            email: emailInput.value,
            telepon: teleponInput.value,
            alamat: alamatInput.value,
            notif: notifToggle.checked,
        };

        const computeInitials = (fullName) => {
            const parts = String(fullName || '').trim().split(/\s+/).filter(Boolean);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            if (parts.length === 1) {
                return parts[0].slice(0, 2).toUpperCase();
            }
            return 'WG';
        };

        const refreshUiState = () => {
            if (previewNama) {
                previewNama.textContent = namaInput.value.trim() || initialSnapshot.nama;
            }
            if (previewInitials) {
                previewInitials.textContent = computeInitials(namaInput.value);
            }

            if (notifBadge) {
                notifBadge.textContent = notifToggle.checked ? 'Aktif' : 'Nonaktif';
                notifBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold ' + (
                    notifToggle.checked
                        ? 'bg-[#D8F3DC] text-[#1B4332]'
                        : 'bg-[#EAE4D8] text-[#52645B]'
                );
            }

            const isDirty =
                namaInput.value !== initialSnapshot.nama ||
                emailInput.value !== initialSnapshot.email ||
                teleponInput.value !== initialSnapshot.telepon ||
                alamatInput.value !== initialSnapshot.alamat ||
                notifToggle.checked !== initialSnapshot.notif;

            if (dirtyStatus) {
                dirtyStatus.textContent = isDirty
                    ? 'Ada perubahan yang belum disimpan. Klik Simpan Perubahan.'
                    : 'Data profil Anda sudah tersimpan.';
                dirtyStatus.className = isDirty
                    ? 'text-xs font-bold text-[#9A3412]'
                    : 'text-xs font-semibold text-[#52645B]';
            }
        };

        [namaInput, emailInput, teleponInput, alamatInput].forEach((field) => {
            field.addEventListener('input', refreshUiState);
        });
        notifToggle.addEventListener('change', refreshUiState);

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                namaInput.value = initialSnapshot.nama;
                emailInput.value = initialSnapshot.email;
                teleponInput.value = initialSnapshot.telepon;
                alamatInput.value = initialSnapshot.alamat;
                notifToggle.checked = initialSnapshot.notif;
                refreshUiState();
            });
        }
    }

    document.addEventListener('click', (event) => {
        const toggleBtn = event.target.closest('[data-toggle-password]');
        if (!toggleBtn) {
            return;
        }

        const targetId = toggleBtn.getAttribute('data-toggle-password');
        const inputEl = targetId ? document.getElementById(targetId) : null;
        if (!inputEl) {
            return;
        }

        inputEl.type = inputEl.type === 'password' ? 'text' : 'password';
    });
});
</script>
@endpush
