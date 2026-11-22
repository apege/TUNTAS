@extends('layouts.app')

@section('content')
<div class="min-h-screen clay-bg">
    <!-- Topbar -->
    <header class="border-b border-[#E3DFD4] bg-[#FCFBF8]/80 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="clay-badge-leaf w-9 h-9 flex items-center justify-center text-[#1B4332]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-base text-[#122A1E]">TUNTAS</span>
                    <span class="text-xs text-[#52645B] block -mt-1">Portal Warga</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <span class="block text-xs font-bold text-[#142D21]">{{ $user->nama }}</span>
                    <span class="block text-[11px] text-[#60776C]">Warga RT 01 / RW 05</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="clay-pill px-3.5 py-1.5 text-xs font-semibold text-[#8B2626] hover:text-red-700">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF2EC] text-[#245640] text-xs font-bold mb-2">
                    <span>Peran: Warga</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#142D21]">Halo, {{ $user->nama }}</h1>
                <p class="text-sm text-[#5B6D63] mt-1">Sampaikan keluhan lingkungan sekitar atau pantau perkembangan laporan Anda.</p>
            </div>
            <div>
                <button class="clay-btn-green px-5 py-3 text-white text-sm font-bold flex items-center gap-2 shadow-md">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Buat Laporan Baru</span>
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="clay-card p-5">
                <span class="text-xs font-semibold text-[#5B6D63] uppercase tracking-wider block mb-1">Laporan Saya</span>
                <span class="text-3xl font-extrabold text-[#1B4332]">2</span>
                <span class="text-xs text-[#708579] block mt-1">Total dikirimkan</span>
            </div>
            <div class="clay-card p-5">
                <span class="text-xs font-semibold text-[#5B6D63] uppercase tracking-wider block mb-1">Sedang Diproses</span>
                <span class="text-3xl font-extrabold text-[#D97706]">1</span>
                <span class="text-xs text-[#708579] block mt-1">Petugas sedang menangani</span>
            </div>
            <div class="clay-card p-5">
                <span class="text-xs font-semibold text-[#5B6D63] uppercase tracking-wider block mb-1">Sudah Selesai</span>
                <span class="text-3xl font-extrabold text-[#15803D]">1</span>
                <span class="text-xs text-[#708579] block mt-1">Menunggu konfirmasi Anda</span>
            </div>
        </div>

        <div class="clay-card p-6">
            <h2 class="text-lg font-bold text-[#142D21] mb-2">Alur Kerja Warga Berdasarkan PRD</h2>
            <p class="text-sm text-[#5B6D63] leading-relaxed">
                Anda berhasil masuk menggunakan akun <strong>Warga</strong>. Menurut PRD TUNTAS, warga dapat mengirimkan laporan masalah lingkungan beserta foto pendukung dan lokasi, memantau riwayat progres penanganan secara transparan, serta mengonfirmasi hasil pekerjaan sebelum kasus dinyatakan tuntas.
            </p>
        </div>
    </main>
</div>
@endsection
