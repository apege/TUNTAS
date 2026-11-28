@extends('layouts.app')

@section('content')
<div class="min-h-screen dashboard-canvas flex flex-col lg:flex-row antialiased relative selection:bg-[#245640] selection:text-white">

    <!-- =========================================================================
         MOBILE TOPBAR (Visible on screens < lg)
         ========================================================================= -->
    <header class="lg:hidden sticky top-0 z-40 bg-[#FAF8F4]/90 backdrop-blur-md border-b border-[#E3DDD1] px-4 py-3 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <!-- Mobile Menu Toggle Button (Clay Squircle) -->
            <button type="button" 
                    id="mobile-menu-btn"
                    onclick="toggleMobileDrawer()" 
                    class="clay-pill w-10 h-10 flex items-center justify-center text-[#1E3A2B] hover:text-[#142D21] focus:outline-none"
                    aria-label="Buka menu navigasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Brand Logo & Name -->
            <div class="flex items-center gap-2.5">
                <div class="clay-badge-leaf w-9 h-9 flex items-center justify-center text-[#1B4332] shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                    </svg>
                </div>
                <div>
                    <span class="block font-black text-base text-[#122A1E] tracking-tight leading-none">TUNTAS</span>
                    <span class="text-[10px] text-[#60776C] font-semibold">Lingkungan Lebih Baik</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Territory Pill & Profile Avatar -->
        <div class="flex items-center gap-2">
            <!-- RT / RW Pill Tag -->
            <div class="neu-pill-tag px-2.5 py-1 flex items-center gap-1.5 text-[11px] font-extrabold text-[#1B4332]">
                <span>RT {{ $rtNomor ?? '02' }}</span>
                <span class="text-[#A2B3A8]">•</span>
                <span>RW {{ $rwNomor ?? '05' }}</span>
            </div>

            <!-- Profile Avatar (Mobile) -> opens mobile drawer -->
            <button type="button" 
                    onclick="toggleMobileDrawer()" 
                    class="clay-pill w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-[#1B4332] bg-[#E5F2E9] border border-white/80 active:scale-95 transition-transform"
                    aria-label="Buka Menu Profil Warga">
                {{ $initials ?? 'SA' }}
            </button>
        </div>
    </header>

    <!-- =========================================================================
         MOBILE DRAWER / SLIDE-OVER (Visible when opened on < lg)
         ========================================================================= -->
    <div id="mobile-drawer-backdrop" 
         onclick="toggleMobileDrawer()" 
         class="fixed inset-0 z-50 bg-[#122A1E]/40 backdrop-blur-sm hidden transition-opacity duration-300 lg:hidden">
    </div>

    <aside id="mobile-drawer" 
           class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-[#FAF8F4] neu-sidebar p-5 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden overflow-y-auto no-scrollbar">
        <div class="space-y-6">
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-3 border-b border-[#E3DDD1]">
                <div class="flex items-center gap-3">
                    <div class="clay-badge-leaf w-10 h-10 flex items-center justify-center text-[#1B4332]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-lg text-[#122A1E]">TUNTAS</h2>
                        <p class="text-xs text-[#60776C]">Portal Warga</p>
                    </div>
                </div>
                <button type="button" 
                        onclick="toggleMobileDrawer()" 
                        class="clay-pill w-8 h-8 flex items-center justify-center text-[#55695E]"
                        aria-label="Tutup menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Wilayah Anda Card -->
            <div class="clay-territory-card p-4">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E] mb-2">Wilayah Anda</span>
                <div class="flex items-center gap-3">
                    <div class="clay-icon-mint w-10 h-10 text-[#1B4332] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block font-black text-sm text-[#142D21]">RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
                        <span class="block text-xs text-[#60776C]">Kelurahan {{ $kelurahan ?? 'Sukamaju' }}</span>
                    </div>
                </div>
            </div>

            <!-- Ruang Warga Navigation -->
            <div class="space-y-1.5">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E] px-2 mb-2">Ruang Warga</span>
                
                <a href="{{ route('warga.dashboard') }}" class="{{ request()->routeIs('warga.dashboard') ? 'clay-nav-active' : 'neu-nav-item' }} px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 {{ request()->routeIs('warga.dashboard') ? '' : 'text-[#52665B]' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('warga.laporan.create') }}" class="{{ request()->routeIs('warga.laporan.create') ? 'clay-nav-active' : 'neu-nav-item' }} px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 {{ request()->routeIs('warga.laporan.create') ? '' : 'text-[#52665B]' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <a href="{{ route('warga.laporan.index') }}" class="{{ request()->routeIs('warga.laporan.index') ? 'clay-nav-active' : 'neu-nav-item' }} px-4 py-3 flex items-center justify-between font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ request()->routeIs('warga.laporan.index') ? '' : 'text-[#52665B]' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Saya</span>
                    </div>
                    <span class="{{ request()->routeIs('warga.laporan.index') ? 'bg-white/20 text-white' : 'neu-pill-tag text-[#142D21]' }} px-2 py-0.5 text-xs font-bold rounded-full">{{ $stats['total'] ?? $totalLaporanSaya ?? 0 }}</span>
                </a>

                <a href="{{ route('warga.dashboard') }}#peta" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Peta Lingkungan</span>
                </a>

                <a href="{{ route('warga.dashboard') }}#pengumuman" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Pengumuman</span>
                </a>

                <a href="{{ route('warga.dashboard') }}#profil" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Profil Saya</span>
                </a>
            </div>
        </div>

        <!-- Mobile Drawer Bottom: Logout & Info -->
        <div class="pt-6 border-t border-[#E3DDD1] space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#D8F3DC] text-[#1B4332] font-black flex items-center justify-center text-sm border border-white">
                    {{ $initials ?? 'SA' }}
                </div>
                <div class="flex-1 min-w-0">
                    <span class="block font-bold text-sm text-[#142D21] truncate">{{ $user->nama ?? 'Siti Aminah' }}</span>
                    <span class="block text-xs text-[#60776C]">Warga RT {{ $rtNomor ?? '02' }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="clay-pill w-full py-2.5 text-center text-xs font-bold text-[#9E2A2B] hover:text-red-700">
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </aside>

    <!-- =========================================================================
         DESKTOP SIDEBAR (Identical dimensions across all pages: w-60 xl:w-68 2xl:w-72)
         ========================================================================= -->
    <aside class="hidden lg:flex w-60 xl:w-68 2xl:w-72 shrink-0 neu-sidebar min-h-screen p-4 xl:p-5 flex-col justify-between sticky top-0 h-screen overflow-y-auto no-scrollbar">
        <div class="space-y-4 xl:space-y-5">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="clay-badge-leaf w-10 h-10 flex items-center justify-center text-[#1B4332] shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-black text-lg xl:text-xl text-[#122A1E] tracking-tight leading-none">TUNTAS</h1>
                    <span class="text-[11px] font-medium text-[#5F7367]">Lingkungan Lebih Baik</span>
                </div>
            </div>

            <!-- Wilayah Anda Card -->
            <div>
                <span class="block text-[10px] xl:text-[11px] font-bold uppercase tracking-wider text-[#687C71] mb-1.5 px-1">Wilayah Anda</span>
                <div class="clay-territory-card p-3 xl:p-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="clay-icon-mint w-8 h-8 xl:w-9 xl:h-9 text-[#1B4332] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block font-black text-xs xl:text-sm text-[#142D21] truncate">RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
                            <span class="block text-[10px] xl:text-xs text-[#60776C] truncate">Kelurahan {{ $kelurahan ?? 'Sukamaju' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ruang Warga Navigation -->
            <nav class="space-y-1" aria-label="Menu Ruang Warga">
                <span class="block text-[10px] xl:text-[11px] font-bold uppercase tracking-wider text-[#687C71] mb-1 px-1">Ruang Warga</span>
                
                <!-- Beranda -->
                <a href="{{ route('warga.dashboard') }}" class="{{ request()->routeIs('warga.dashboard') ? 'clay-nav-active' : 'neu-nav-item' }} px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 {{ request()->routeIs('warga.dashboard') ? '' : 'text-[#52665B]' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>

                <!-- Buat Laporan -->
                <a href="{{ route('warga.laporan.create') }}" class="{{ request()->routeIs('warga.laporan.create') ? 'clay-nav-active' : 'neu-nav-item' }} px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 {{ request()->routeIs('warga.laporan.create') ? '' : 'text-[#52665B]' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <!-- Laporan Saya -->
                <a href="{{ route('warga.laporan.index') }}" class="{{ request()->routeIs('warga.laporan.index') ? 'clay-nav-active' : 'neu-nav-item' }} px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center justify-between font-semibold text-xs xl:text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ request()->routeIs('warga.laporan.index') ? '' : 'text-[#52665B]' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Saya</span>
                    </div>
                    <span class="{{ request()->routeIs('warga.laporan.index') ? 'bg-white/20 text-white' : 'neu-pill-tag text-[#142D21]' }} px-2 py-0.5 text-[10px] xl:text-[11px] font-bold rounded-full">{{ $stats['total'] ?? $totalLaporanSaya ?? 0 }}</span>
                </a>

                <!-- Peta Lingkungan -->
                <a href="{{ route('warga.dashboard') }}#peta" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Peta Lingkungan</span>
                </a>

                <!-- Pengumuman -->
                <a href="{{ route('warga.dashboard') }}#pengumuman" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Pengumuman</span>
                </a>

                <!-- Profil Saya -->
                <a href="{{ route('warga.dashboard') }}#profil" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Profil Saya</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Helper Card -->
        <div class="mt-3 xl:mt-4">
            <div class="clay-helper-card p-3 xl:p-3.5 relative overflow-hidden">
                <div class="flex items-start gap-2.5 relative z-10">
                    <div class="clay-badge-help w-7 h-7 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        ?
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-extrabold text-xs text-[#122A1E] leading-snug">Pusat Bantuan</h3>
                        <p class="text-[10px] text-[#4A6054] mt-0.5 leading-tight">Alur pelaporan dan penanganan.</p>
                        <a href="{{ route('warga.dashboard') }}#panduan" class="inline-flex items-center gap-1 text-[10px] font-bold text-[#1B4332] mt-1.5 hover:underline">
                            <span>Pelajari panduan</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- =========================================================================
         MAIN VIEWPORT (Identical padding: p-4 sm:p-5 lg:p-6 xl:p-8)
         ========================================================================= -->
    <main class="flex-1 min-w-0 p-4 sm:p-5 lg:p-6 xl:p-8 pb-24 lg:pb-8 flex flex-col justify-between">
        
        <div>
            <!-- Flash Message Toast Notification -->
            @if (session('success'))
                <div data-flash-toast class="mb-4 sm:mb-5 p-3.5 sm:p-4 rounded-2xl bg-[#E8F5ED] border border-[#A7D7B9] text-[#164E33] shadow-sm flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="clay-icon-emerald w-8 h-8 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#15803D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-bold">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.closest('[data-flash-toast]').remove()" class="text-[#4E7660] hover:text-[#164E33] font-bold text-xs px-2 py-1 rounded-lg hover:bg-black/5" aria-label="Tutup pemberitahuan">✕</button>
                </div>
            @endif

            @if (session('error'))
                <div data-flash-toast class="mb-4 sm:mb-5 p-3.5 sm:p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 shadow-sm flex items-center justify-between gap-3">
                    <span class="text-xs sm:text-sm font-bold">{{ session('error') }}</span>
                    <button type="button" onclick="this.closest('[data-flash-toast]').remove()" class="text-red-700 hover:text-red-900 font-bold text-xs px-2 py-1 rounded-lg hover:bg-black/5" aria-label="Tutup pemberitahuan">✕</button>
                </div>
            @endif

            <!-- ================= SHARED TOP HEADER BAR (Identical across all pages) ================= -->
            <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4 mb-5 xl:mb-6">
                <!-- Title & Breadcrumb & Territory Badge -->
                <div class="flex items-center gap-3">
                    <div>
                        <span class="block text-[11px] sm:text-xs font-semibold text-[#667B70] tracking-wide">
                            Warga • Lingkungan {{ $kelurahan ?? 'Sukamaju' }}
                        </span>
                        <div class="flex items-center gap-2.5 mt-0.5">
                            <h2 class="text-xl sm:text-2xl xl:text-3xl font-black text-[#122A1E] tracking-tight">
                                @yield('header-title', 'Dashboard Warga')
                            </h2>
                            <!-- RT / RW Neumorphic Pill Badge placed neatly next to title -->
                            <div class="neu-pill-tag px-2.5 py-1 flex items-center gap-1.5 text-[11px] font-extrabold text-[#153425] shrink-0">
                                <span>RT {{ $rtNomor ?? '02' }}</span>
                                <span class="w-1 h-1 rounded-full bg-[#8EA598]"></span>
                                <span>RW {{ $rwNomor ?? '05' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Header Actions (Search, Notification, Profile) -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Search Form -->
                    <form action="@yield('search-action', route('warga.dashboard'))" method="GET" class="relative w-44 sm:w-56 md:w-64 lg:w-48 xl:w-64 m-0">
                        <div class="neu-search-input flex items-center px-3 py-1.5 sm:px-3.5 sm:py-2">
                            <svg class="w-3.5 h-3.5 text-[#73877C] shrink-0 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" 
                                   name="q"
                                   id="dashboard-search" 
                                   value="{{ $search ?? '' }}"
                                   placeholder="Cari laporan..." 
                                   class="w-full bg-transparent border-0 p-0 text-xs text-[#122A1E] placeholder-[#798D82] focus:outline-none focus:ring-0">
                            @if (!empty($search))
                                <a href="@yield('search-action', route('warga.dashboard'))" class="text-xs text-[#798D82] hover:text-[#122A1E] mr-1.5" title="Hapus pencarian">✕</a>
                            @endif
                            <kbd class="hidden xl:inline-flex items-center px-1.5 py-0.5 text-[9px] font-semibold text-[#6A7F73] bg-[#DFDAD0] rounded border border-white/60 shadow-inner">
                                ⌘K
                            </kbd>
                        </div>
                    </form>

                    <!-- Notification Dropdown Container -->
                    <div class="relative" id="notification-dropdown-container">
                        <button type="button" 
                                id="notification-toggle-btn"
                                onclick="toggleNotificationDropdown(event)" 
                                class="clay-pill w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center text-[#245640] relative shrink-0 transition-transform active:scale-95" 
                                title="Pemberitahuan"
                                aria-label="Pemberitahuan"
                                aria-expanded="false">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notification-unread-dot" class="{{ ($unreadNotifikasiCount ?? 0) > 0 ? '' : 'hidden' }} absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#1B4332] ring-2 ring-white"></span>
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div id="notification-dropdown-menu" 
                             class="hidden absolute right-0 mt-2 w-80 sm:w-96 clay-card-content p-3 sm:p-4 z-50 shadow-2xl border border-white/95 text-left">
                            <div class="flex items-center justify-between pb-3 border-b border-[#E3DDD1]">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-black text-[#122A1E]">Pemberitahuan</h3>
                                    <span id="notification-unread-badge" class="{{ ($unreadNotifikasiCount ?? 0) > 0 ? '' : 'hidden' }} px-2 py-0.5 rounded-full bg-[#1B4332] text-white text-[10px] font-black">
                                        {{ $unreadNotifikasiCount ?? 0 }} baru
                                    </span>
                                </div>
                                @if (($unreadNotifikasiCount ?? 0) > 0)
                                    <button type="button" 
                                            id="btn-mark-all-read"
                                            onclick="markAllNotificationsAsRead()"
                                            class="text-[11px] font-bold text-[#245640] hover:text-[#122A1E] transition-colors">
                                        Tandai semua dibaca
                                    </button>
                                @endif
                            </div>

                            <div id="notification-list" class="my-2 max-h-72 sm:max-h-80 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                @forelse ($notifikasis ?? [] as $notif)
                                    <div id="notif-item-{{ $notif->id }}" 
                                         onclick="markNotificationAsRead('{{ $notif->id }}')"
                                         class="clay-item-card p-2.5 sm:p-3 flex items-start gap-2.5 cursor-pointer transition-all hover:-translate-y-0.5 {{ $notif->isUnread() ? 'bg-[#FCFAF5] border-l-4 border-l-[#1B4332]' : 'opacity-85' }}">
                                        
                                        @if ($notif->pengumuman_id)
                                            <div class="clay-icon-amber w-8 h-8 flex items-center justify-center text-[#B45309] shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                            </div>
                                        @elseif ($notif->kasus_id)
                                            <div class="clay-icon-blue w-8 h-8 flex items-center justify-center text-[#1D4ED8] shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="clay-icon-mint w-8 h-8 flex items-center justify-center text-[#1B4332] shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <h4 class="text-xs font-black text-[#122A1E] truncate">{{ $notif->judul }}</h4>
                                                @if ($notif->isUnread())
                                                    <span id="notif-dot-{{ $notif->id }}" class="w-2 h-2 rounded-full bg-[#1B4332] shrink-0"></span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-[#55695D] line-clamp-2 mt-0.5 leading-snug">{{ $notif->pesan }}</p>
                                            <span class="block text-[10px] text-[#7C8F83] font-medium mt-1">
                                                {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center flex flex-col items-center justify-center text-[#6A7F73]">
                                        <div class="w-10 h-10 rounded-2xl bg-[#E8F1EB]/60 flex items-center justify-center text-[#558269] mb-2 shadow-sm border border-white">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-[#142D21]">Belum ada pemberitahuan</p>
                                        <p class="text-[11px] text-[#718579] mt-0.5">Pembaruan laporan & pengumuman akan muncul di sini</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="pt-2 border-t border-[#E3DDD1] flex items-center justify-between text-[10px] text-[#6A7F73]">
                                <span>Lingkungan Sukamaju</span>
                                <span class="font-bold text-[#245640]">RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown Toggle -->
                    <div class="relative" id="profile-dropdown-container">
                        <button type="button" 
                                id="profile-toggle-btn"
                                onclick="toggleProfileDropdown(event)" 
                                class="clay-pill pl-1 pr-2.5 sm:pr-3 py-1 flex items-center gap-2 text-left group shrink-0 transition-transform active:scale-95"
                                aria-label="Menu Pengguna"
                                aria-expanded="false">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#D8F3DC] text-[#1B4332] font-black text-xs flex items-center justify-center shadow-inner border border-white/80">
                                {{ $initials ?? 'SA' }}
                            </div>
                            <div class="hidden sm:block leading-tight max-w-[100px] xl:max-w-[130px]">
                                <span class="block text-xs font-bold text-[#142D21] group-hover:text-[#245640] transition-colors truncate">
                                    {{ $user->nama ?? 'Siti Aminah' }}
                                </span>
                                <span class="block text-[10px] text-[#697D71]">Warga</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-[#6B8074] group-hover:text-[#142D21] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="profile-dropdown-menu" 
                             class="hidden absolute right-0 mt-2 w-56 clay-card-content p-3 z-50 shadow-2xl border border-white/95 text-left">
                            <div class="px-2.5 py-2 border-b border-[#E3DDD1]">
                                <span class="block text-xs font-black text-[#142D21] truncate">{{ $user->nama ?? 'Siti Aminah' }}</span>
                                <span class="block text-[11px] text-[#60776C] truncate mt-0.5">{{ $user->email ?? 'warga@tuntas.id' }}</span>
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#E8F1EC] text-[#1B4332]">
                                    Warga RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}
                                </span>
                            </div>
                            <div class="py-1.5 space-y-0.5">
                                <a href="{{ route('warga.dashboard') }}#profil" class="block px-2.5 py-2 text-xs font-bold text-[#30483C] hover:bg-[#EAE5DC] rounded-xl transition-colors">
                                    Pengaturan Profil
                                </a>
                                <a href="{{ route('warga.dashboard') }}#panduan" class="block px-2.5 py-2 text-xs font-bold text-[#30483C] hover:bg-[#EAE5DC] rounded-xl transition-colors">
                                    Panduan Penggunaan
                                </a>
                            </div>
                            <div class="pt-1.5 border-t border-[#E3DDD1]">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-2.5 py-2 text-xs font-bold text-[#9E2A2B] hover:bg-red-50 rounded-xl transition-colors">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ================= PAGE SPECIFIC CONTENT ================= -->
            @yield('warga-content')

        </div>

        <!-- ================= SHARED FOOTER ================= -->
        <footer class="mt-8 pt-4 border-t border-[#E3DDD1] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#697D71] gap-2">
            <span>Lingkungan Lebih Baik</span>
            <span>TUNTAS v1.0 • Warga • RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
        </footer>
    </main>

    <!-- =========================================================================
         MOBILE BOTTOM NAVIGATION DOCK (Visible on < lg)
         ========================================================================= -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 neu-bottom-bar px-3 py-2 flex items-center justify-around text-[#52665B]">
        <!-- Beranda -->
        <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('warga.dashboard') ? 'text-[#1B4332] font-black' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('warga.dashboard') ? 'text-[#1B4332]' : 'text-[#52665B]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
            </svg>
            <span class="text-[10px]">Beranda</span>
        </a>

        <!-- Laporan -->
        <a href="{{ route('warga.laporan.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('warga.laporan.index') ? 'text-[#1B4332] font-black' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('warga.laporan.index') ? 'text-[#1B4332]' : 'text-[#52665B]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-[10px]">Laporan</span>
        </a>

        <!-- Center Action Floating Clay Button (Buat Laporan) -->
        <a href="{{ route('warga.laporan.create') }}" class="-mt-5 flex flex-col items-center group" aria-label="Buat Laporan Baru">
            <div class="clay-btn-fab w-13 h-13 rounded-full flex items-center justify-center text-white {{ request()->routeIs('warga.laporan.create') ? 'ring-4 ring-[#2D6A4F]/40' : '' }} transition-transform active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-[#1B4332] mt-0.5">Lapor</span>
        </a>

        <!-- Pengumuman -->
        <a href="{{ route('warga.dashboard') }}#pengumuman" class="flex flex-col items-center gap-0.5">
            <svg class="w-5 h-5 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="text-[10px]">Warta</span>
        </a>

        <!-- Profil -->
        <button type="button" onclick="toggleMobileDrawer()" class="flex flex-col items-center gap-0.5">
            <div class="w-5 h-5 rounded-full bg-[#D8F3DC] text-[#1B4332] font-black text-[9px] flex items-center justify-center">
                {{ $initials ?? 'SA' }}
            </div>
            <span class="text-[10px]">Profil</span>
        </button>
    </nav>
</div>

<!-- =========================================================================
     SHARED SCRIPTS (Interactivity, Keyboard shortcuts, Notifications)
     ========================================================================= -->
<script>
    function toggleMobileDrawer() {
        const drawer = document.getElementById('mobile-drawer');
        const backdrop = document.getElementById('mobile-drawer-backdrop');
        if (!drawer || !backdrop) return;

        const isClosed = drawer.classList.contains('-translate-x-full');
        if (isClosed) {
            drawer.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function toggleNotificationDropdown(event) {
        if (event) {
            event.stopPropagation();
        }

        const notifMenu = document.getElementById('notification-dropdown-menu');
        const profileMenu = document.getElementById('profile-dropdown-menu');
        const notifBtn = document.getElementById('notification-toggle-btn');
        const profileBtn = document.getElementById('profile-toggle-btn');
        if (!notifMenu) return;

        if (profileMenu && !profileMenu.classList.contains('hidden')) {
            profileMenu.classList.add('hidden');
            if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
        }

        const isHidden = notifMenu.classList.contains('hidden');
        notifMenu.classList.toggle('hidden');
        if (notifBtn) {
            notifBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        }
    }

    function toggleProfileDropdown(event) {
        if (event) {
            event.stopPropagation();
        }

        const menu = document.getElementById('profile-dropdown-menu');
        const notifMenu = document.getElementById('notification-dropdown-menu');
        const profileBtn = document.getElementById('profile-toggle-btn');
        const notifBtn = document.getElementById('notification-toggle-btn');
        if (!menu) return;

        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
            if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
        }

        const isHidden = menu.classList.contains('hidden');
        menu.classList.toggle('hidden');
        if (profileBtn) {
            profileBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        }
    }

    document.addEventListener('click', function(e) {
        const profileMenu = document.getElementById('profile-dropdown-menu');
        const notifMenu = document.getElementById('notification-dropdown-menu');
        const notifContainer = document.getElementById('notification-dropdown-container');
        const profileContainer = document.getElementById('profile-dropdown-container');
        const profileBtn = document.getElementById('profile-toggle-btn');
        const notifBtn = document.getElementById('notification-toggle-btn');

        if (profileMenu && !profileMenu.classList.contains('hidden')) {
            if (!profileContainer || !profileContainer.contains(e.target)) {
                profileMenu.classList.add('hidden');
                if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
            }
        }

        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            if (!notifContainer || !notifContainer.contains(e.target)) {
                notifMenu.classList.add('hidden');
                if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });

    function markNotificationAsRead(id) {
        const dot = document.getElementById('notif-dot-' + id);
        const item = document.getElementById('notif-item-' + id);

        if (dot) dot.remove();
        if (item) {
            item.classList.remove('border-l-4', 'border-l-[#1B4332]', 'bg-[#FCFAF5]');
            item.classList.add('opacity-85');
        }

        fetch('/notifikasi/' + id + '/baca', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data && data.success) {
                  const badge = document.getElementById('notification-unread-badge');
                  const topDot = document.getElementById('notification-unread-dot');
                  if (badge) {
                      let count = parseInt(badge.textContent) || 0;
                      if (count > 1) {
                          badge.textContent = (count - 1) + ' baru';
                      } else {
                          badge.classList.add('hidden');
                          if (topDot) topDot.classList.add('hidden');
                          const btnMarkAll = document.getElementById('btn-mark-all-read');
                          if (btnMarkAll) btnMarkAll.remove();
                      }
                  }
              }
          }).catch(err => console.error(err));
    }

    function markAllNotificationsAsRead() {
        const topDot = document.getElementById('notification-unread-dot');
        const badge = document.getElementById('notification-unread-badge');
        const btnMarkAll = document.getElementById('btn-mark-all-read');

        if (topDot) topDot.classList.add('hidden');
        if (badge) badge.classList.add('hidden');
        if (btnMarkAll) btnMarkAll.remove();

        document.querySelectorAll('[id^="notif-dot-"]').forEach(el => el.remove());
        document.querySelectorAll('[id^="notif-item-"]').forEach(el => {
            el.classList.remove('border-l-4', 'border-l-[#1B4332]', 'bg-[#FCFAF5]');
            el.classList.add('opacity-85');
        });

        fetch('{{ route("notifikasi.baca-semua") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).catch(err => console.error(err));
    }

    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('dashboard-search');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', function() {
            const bottomBar = document.querySelector('nav.neu-bottom-bar');
            if (!bottomBar || window.innerWidth >= 1024) return;

            const isKeyboardOpen = (window.innerHeight - window.visualViewport.height) > 120;
            if (isKeyboardOpen) {
                bottomBar.style.transform = 'translateY(120%)';
                bottomBar.style.transition = 'transform 0.2s ease-in-out';
            } else {
                bottomBar.style.transform = 'translateY(0)';
            }
        });
    }
</script>

@stack('scripts')
@endsection
