@extends('layouts.app')

@section('content')
<div class="min-h-screen dashboard-canvas flex flex-col lg:flex-row antialiased relative selection:bg-[#245640] selection:text-white"
     x-data="{ mobileMenuOpen: false, profileDropdownOpen: false }">

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

        <!-- Right Side: Territory Pill & Notification & Profile -->
        <div class="flex items-center gap-2">
            <!-- RT / RW Pill Tag -->
            <div class="neu-pill-tag px-2.5 py-1 flex items-center gap-1.5 text-[11px] font-extrabold text-[#1B4332]">
                <span>RT {{ $rtNomor ?? '02' }}</span>
                <span class="text-[#A2B3A8]">•</span>
                <span>RW {{ $rwNomor ?? '05' }}</span>
            </div>

            <!-- Profile Avatar (Mobile) -->
            <button type="button" 
                    onclick="toggleProfileDropdown()" 
                    class="clay-pill w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-[#1B4332] bg-[#E5F2E9] border border-white/80"
                    aria-label="Profil Warga">
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
                
                <a href="{{ route('warga.dashboard') }}" class="clay-nav-active px-4 py-3 flex items-center gap-3 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>

                <a href="#buat-laporan" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <a href="#laporan-saya" class="neu-nav-item px-4 py-3 flex items-center justify-between font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Saya</span>
                    </div>
                    <span class="neu-pill-tag px-2 py-0.5 text-[11px] font-bold text-[#142D21]">{{ $stats['total'] ?? 0 }}</span>
                </a>

                <a href="#peta" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Peta Lingkungan</span>
                </a>

                <a href="#pengumuman" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Pengumuman</span>
                </a>

                <a href="#profil" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
         DESKTOP SIDEBAR (Visible on screens >= lg)
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
                
                <!-- Beranda (Active) -->
                <a href="{{ route('warga.dashboard') }}" class="clay-nav-active px-3.5 py-2.5 xl:px-4 xl:py-2.5 flex items-center gap-3 font-bold text-xs xl:text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>

                <!-- Buat Laporan -->
                <a href="#buat-laporan" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <!-- Laporan Saya -->
                <a href="#laporan-saya" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center justify-between font-semibold text-xs xl:text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Saya</span>
                    </div>
                    <span class="neu-pill-tag px-2 py-0.5 text-[10px] xl:text-[11px] font-bold text-[#142D21]">{{ $stats['total'] ?? 0 }}</span>
                </a>

                <!-- Peta Lingkungan -->
                <a href="#peta" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Peta Lingkungan</span>
                </a>

                <!-- Pengumuman -->
                <a href="#pengumuman" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Pengumuman</span>
                </a>

                <!-- Profil Saya -->
                <a href="#profil" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
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
                        <a href="#panduan" class="inline-flex items-center gap-1 text-[10px] font-bold text-[#1B4332] mt-1.5 hover:underline">
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
         MAIN VIEWPORT
         ========================================================================= -->
    <main class="flex-1 min-w-0 p-4 sm:p-5 lg:p-6 xl:p-8 pb-24 lg:pb-8 flex flex-col justify-between">
        
        <div>
            <!-- ================= TOP HEADER BAR ================= -->
            <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4 mb-5 xl:mb-6">
                <!-- Title & Breadcrumb & Territory Badge -->
                <div class="flex items-center gap-3">
                    <div>
                        <span class="block text-[11px] sm:text-xs font-semibold text-[#667B70] tracking-wide">
                            Warga • Lingkungan {{ $kelurahan ?? 'Sukamaju' }}
                        </span>
                        <div class="flex items-center gap-2.5 mt-0.5">
                            <h2 class="text-xl sm:text-2xl xl:text-3xl font-black text-[#122A1E] tracking-tight">
                                Dashboard Warga
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

                <!-- Right Header Actions (Search, Notification, Profile) in a single clean row -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Search Form -->
                    <form action="{{ route('warga.dashboard') }}" method="GET" class="relative w-44 sm:w-56 md:w-64 lg:w-48 xl:w-64 m-0">
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
                                <a href="{{ route('warga.dashboard') }}" class="text-xs text-[#798D82] hover:text-[#122A1E] mr-1.5" title="Hapus pencarian">✕</a>
                            @endif
                            <!-- Keyboard Shortcut Chip -->
                            <kbd class="hidden xl:inline-flex items-center px-1.5 py-0.5 text-[9px] font-semibold text-[#6A7F73] bg-[#DFDAD0] rounded border border-white/60 shadow-inner">
                                ⌘K
                            </kbd>
                        </div>
                    </form>

                    <!-- Notification Dropdown Container -->
                    <div class="relative" id="notification-dropdown-container">
                        <!-- Notification Bell Button -->
                        <button type="button" 
                                id="notification-toggle-btn"
                                onclick="toggleNotificationDropdown()" 
                                class="clay-pill w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center text-[#245640] relative shrink-0 transition-transform active:scale-95" 
                                title="Pemberitahuan"
                                aria-label="Pemberitahuan"
                                aria-expanded="false">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Unread Dot Indicator (Visible only if unread > 0) -->
                            <span id="notification-unread-dot" class="{{ ($unreadNotifikasiCount ?? 0) > 0 ? '' : 'hidden' }} absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#1B4332] ring-2 ring-white animate-pulse"></span>
                        </button>

                        <!-- Notification Dropdown Menu (Claymorphic + Neumorphic) -->
                        <div id="notification-dropdown-menu" 
                             class="hidden absolute right-0 mt-2 w-80 sm:w-96 clay-card-content p-3 sm:p-4 z-50 shadow-2xl border border-white/95 text-left">
                            
                            <!-- Header -->
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

                            <!-- Notification List -->
                            <div id="notification-list" class="my-2 max-h-72 sm:max-h-80 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                @forelse ($notifikasis ?? [] as $notif)
                                    <div id="notif-item-{{ $notif->id }}" 
                                         onclick="markNotificationAsRead('{{ $notif->id }}')"
                                         class="clay-item-card p-2.5 sm:p-3 flex items-start gap-2.5 cursor-pointer transition-all hover:-translate-y-0.5 {{ $notif->isUnread() ? 'bg-[#FCFAF5] border-l-4 border-l-[#1B4332]' : 'opacity-85' }}">
                                        
                                        <!-- Icon Badge by Type -->
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

                                        <!-- Text -->
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

                            <!-- Footer -->
                            <div class="pt-2 border-t border-[#E3DDD1] flex items-center justify-between text-[10px] text-[#6A7F73]">
                                <span>Lingkungan Sukamaju</span>
                                <span class="font-bold text-[#245640]">RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown Toggle -->
                    <div class="relative">
                        <button type="button" 
                                onclick="toggleProfileDropdown()" 
                                class="clay-pill pl-1 pr-2.5 sm:pr-3 py-1 flex items-center gap-2 text-left group shrink-0">
                            <!-- Avatar Squircle -->
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

                        <!-- Profile Dropdown Menu -->
                        <div id="profile-dropdown-menu" 
                             class="hidden absolute right-0 mt-2 w-52 bg-[#FAF8F4] clay-card-stat p-2 z-50 shadow-xl border border-white/90">
                            <div class="px-3 py-2 border-b border-[#E3DDD1]">
                                <span class="block text-xs font-bold text-[#142D21] truncate">{{ $user->nama ?? 'Siti Aminah' }}</span>
                                <span class="block text-[11px] text-[#60776C] truncate">{{ $user->email ?? 'warga@tuntas.id' }}</span>
                            </div>
                            <div class="py-1">
                                <a href="#profil" class="block px-3 py-2 text-xs font-semibold text-[#30483C] hover:bg-[#EAE5DC] rounded-xl transition-colors">
                                    Pengaturan Profil
                                </a>
                                <a href="#panduan" class="block px-3 py-2 text-xs font-semibold text-[#30483C] hover:bg-[#EAE5DC] rounded-xl transition-colors">
                                    Panduan Penggunaan
                                </a>
                            </div>
                            <div class="pt-1 border-t border-[#E3DDD1]">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs font-bold text-[#9E2A2B] hover:bg-red-50 rounded-xl transition-colors">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ================= HERO BANNER ================= -->
            <section class="clay-hero-banner p-4 sm:p-5 lg:p-6 mb-5 xl:mb-6 relative overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-12 items-center gap-4 sm:gap-6 relative z-10">
                    
                    <!-- Left Hero Content -->
                    <div class="md:col-span-7 space-y-3 sm:space-y-3.5">
                        <!-- Date & Location Badge -->
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-white/50 backdrop-blur-sm text-[10px] sm:text-[11px] font-bold text-[#204E3A] border border-white/70 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#2D6A4F] animate-pulse"></span>
                            <span>{{ $tanggalHariIni ?? 'Senin, 23 November 2026' }} • RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
                        </div>

                        <!-- Main Greeting -->
                        <div class="space-y-1">
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-[#122A1E] tracking-tight leading-tight">
                                Selamat datang, {{ $namaPanggilan ?? 'Siti' }}
                            </h3>
                            <p class="text-xs sm:text-sm text-[#385244] leading-relaxed max-w-lg font-normal">
                                Lingkungan yang nyaman dimulai dari kepedulian kita bersama.
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-0.5">
                            <a href="#buat-laporan" 
                               class="clay-btn-green inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all">
                                <span>Laporkan Masalah</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right Hero Graphic: High-Fidelity Claymorphic Village Illustration -->
                    <div class="md:col-span-5 flex items-center justify-center relative">
                        <div class="w-full max-w-[240px] sm:max-w-[280px] xl:max-w-[320px] h-32 sm:h-36 xl:h-40 relative flex items-center justify-center">
                            
                            <!-- Clay SVG Village Artwork -->
                            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 380 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <!-- Sun Glow & Shadow -->
                                    <radialGradient id="sunGlow" cx="50%" cy="50%" r="50%">
                                        <stop offset="0%" stop-color="#FDE68A" />
                                        <stop offset="65%" stop-color="#F59E0B" />
                                        <stop offset="100%" stop-color="#D97706" />
                                    </radialGradient>
                                    <!-- Cloud Gradient -->
                                    <linearGradient id="cloudGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#FFFFFF" />
                                        <stop offset="100%" stop-color="#E2EDE6" />
                                    </linearGradient>
                                    <!-- House 1 Roof Gradient -->
                                    <linearGradient id="roofGrad1" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#3D7D5E" />
                                        <stop offset="100%" stop-color="#1B4332" />
                                    </linearGradient>
                                    <!-- House Wall Gradient -->
                                    <linearGradient id="wallGrad1" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#FFFFFF" />
                                        <stop offset="100%" stop-color="#EAE5DC" />
                                    </linearGradient>
                                    <!-- Tree Foliage Gradient -->
                                    <linearGradient id="treeGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#84C7A3" />
                                        <stop offset="100%" stop-color="#468866" />
                                    </linearGradient>
                                    <linearGradient id="treeGrad2" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#99D9B6" />
                                        <stop offset="100%" stop-color="#559976" />
                                    </linearGradient>
                                    <!-- Ground Hill Gradient -->
                                    <linearGradient id="groundGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#CFE4D6" />
                                        <stop offset="100%" stop-color="#B8D6C2" />
                                    </linearGradient>
                                    <filter id="claySoftShadow" x="-10%" y="-10%" width="125%" height="125%">
                                        <feDropShadow dx="2" dy="4" stdDeviation="3" flood-color="#1B4332" flood-opacity="0.15"/>
                                    </filter>
                                </defs>

                                <!-- Sun (Clay Sphere) -->
                                <circle cx="340" cy="55" r="22" fill="url(#sunGlow)" filter="url(#claySoftShadow)" />

                                <!-- Floating Soft Clouds -->
                                <g opacity="0.9" filter="url(#claySoftShadow)">
                                    <path d="M280 65 C280 57, 288 52, 296 54 C300 48, 308 48, 314 52 C320 50, 328 54, 328 62 C332 64, 332 70, 328 72 C326 74, 282 74, 280 65 Z" fill="url(#cloudGrad)" />
                                </g>

                                <!-- Floating Leaf Badge (Top Right) -->
                                <g transform="translate(305, 30)" filter="url(#claySoftShadow)">
                                    <rect width="36" height="36" rx="12" fill="#E8F4EC" stroke="#FFFFFF" stroke-width="1.5" />
                                    <path d="M17 26A6.5 6.5 0 0 1 15.8 13.1C21 12 22.5 11.5 24 9.5c.8 1.8 1.8 3.7 1.8 7 0 4.8-4.2 9.5-8.8 9.5Z" fill="none" stroke="#2D6A4F" stroke-width="1.8" />
                                    <path d="M9 27c0-2.5 1.5-4.5 4-5" fill="none" stroke="#2D6A4F" stroke-width="1.8" stroke-linecap="round"/>
                                </g>

                                <!-- Rolling Base Ground (Clay Mound) -->
                                <ellipse cx="230" cy="225" rx="200" ry="60" fill="url(#groundGrad)" />

                                <!-- Far Left Tree -->
                                <g filter="url(#claySoftShadow)">
                                    <!-- Trunk -->
                                    <rect x="18" y="160" width="8" height="28" rx="4" fill="#9C826A" />
                                    <!-- Foliage -->
                                    <circle cx="22" cy="148" r="18" fill="url(#treeGrad2)" />
                                </g>

                                <!-- Mid-Left Tree -->
                                <g filter="url(#claySoftShadow)">
                                    <rect x="58" y="152" width="9" height="36" rx="4" fill="#8C735D" />
                                    <ellipse cx="62" cy="135" rx="20" ry="24" fill="url(#treeGrad1)" />
                                </g>

                                <!-- House 1 (Main Big House) -->
                                <g filter="url(#claySoftShadow)">
                                    <!-- Chimney -->
                                    <rect x="110" y="98" width="10" height="20" rx="2" fill="#BA6F52" />
                                    <ellipse cx="115" cy="98" rx="6" ry="2" fill="#DFA38D" />
                                    
                                    <!-- Gable Roof -->
                                    <polygon points="120,86 195,130 45,130" fill="url(#roofGrad1)" />
                                    <polygon points="120,86 195,130 185,130 120,92" fill="#2D6A4F" opacity="0.4" />
                                    
                                    <!-- Main Body Walls -->
                                    <rect x="55" y="128" width="130" height="66" rx="6" fill="url(#wallGrad1)" stroke="#FFFFFF" stroke-width="1" />
                                    
                                    <!-- Windows -->
                                    <rect x="70" y="138" width="22" height="22" rx="4" fill="#C5E3D2" stroke="#FFFFFF" stroke-width="1.5" />
                                    <line x1="81" y1="138" x2="81" y2="160" stroke="#FFFFFF" stroke-width="1.2" />
                                    <line x1="70" y1="149" x2="92" y2="149" stroke="#FFFFFF" stroke-width="1.2" />

                                    <rect x="148" y="138" width="22" height="22" rx="4" fill="#C5E3D2" stroke="#FFFFFF" stroke-width="1.5" />
                                    <line x1="159" y1="138" x2="159" y2="160" stroke="#FFFFFF" stroke-width="1.2" />
                                    <line x1="148" y1="149" x2="170" y2="149" stroke="#FFFFFF" stroke-width="1.2" />

                                    <!-- Front Door -->
                                    <rect x="108" y="146" width="24" height="48" rx="4" fill="#D39E82" stroke="#FFFFFF" stroke-width="1" />
                                    <circle cx="127" cy="170" r="2" fill="#6B432C" />
                                </g>

                                <!-- House 2 (Adjacent Neighbor House) -->
                                <g filter="url(#claySoftShadow)">
                                    <!-- Roof -->
                                    <polygon points="230,98 285,134 175,134" fill="url(#roofGrad1)" />
                                    <!-- Walls -->
                                    <rect x="185" y="132" width="90" height="62" rx="6" fill="url(#wallGrad1)" stroke="#FFFFFF" stroke-width="1" />
                                    <!-- Window -->
                                    <rect x="198" y="142" width="20" height="20" rx="4" fill="#C5E3D2" stroke="#FFFFFF" stroke-width="1.5" />
                                    <line x1="208" y1="142" x2="208" y2="162" stroke="#FFFFFF" stroke-width="1.2" />
                                    <!-- Door -->
                                    <rect x="236" y="148" width="22" height="46" rx="4" fill="#D39E82" stroke="#FFFFFF" stroke-width="1" />
                                    <circle cx="251" cy="170" r="2" fill="#6B432C" />
                                </g>

                                <!-- Right Tree (Clay Sphere) -->
                                <g filter="url(#claySoftShadow)">
                                    <rect x="306" y="148" width="10" height="40" rx="5" fill="#8C735D" />
                                    <circle cx="311" cy="132" r="22" fill="url(#treeGrad1)" />
                                </g>

                                <!-- Far Right Bush -->
                                <g filter="url(#claySoftShadow)">
                                    <circle cx="348" cy="166" r="16" fill="url(#treeGrad2)" />
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ================= 4 STAT CARDS ================= -->
            <section class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 xl:gap-5 mb-5 xl:mb-7" aria-label="Ringkasan Laporan">
                
                <!-- Card 1: Laporan Saya -->
                <div class="clay-card-stat p-3.5 sm:p-4 xl:p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="clay-icon-mint w-10 h-10 xl:w-11 xl:h-11 text-[#1B4332] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="block text-2xl sm:text-3xl xl:text-4xl font-black text-[#142D21]">
                            {{ $stats['total'] ?? 0 }}
                        </span>
                        <h4 class="font-bold text-xs sm:text-sm text-[#183627] mt-1 truncate">Laporan Saya</h4>
                        <p class="text-[11px] sm:text-xs text-[#6A7F73] mt-0.5 line-clamp-1">Kepedulian Anda untuk lingkungan</p>
                    </div>
                </div>

                <!-- Card 2: Sedang Ditangani -->
                <div class="clay-card-stat p-3.5 sm:p-4 xl:p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="clay-icon-blue w-10 h-10 xl:w-11 xl:h-11 text-[#1D4ED8] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="block text-2xl sm:text-3xl xl:text-4xl font-black text-[#142D21]">
                            {{ $stats['sedang_ditangani'] ?? 0 }}
                        </span>
                        <h4 class="font-bold text-xs sm:text-sm text-[#183627] mt-1 truncate">Sedang Ditangani</h4>
                        <p class="text-[11px] sm:text-xs text-[#6A7F73] mt-0.5 line-clamp-1">Sedang ditindaklanjuti pengurus</p>
                    </div>
                </div>

                <!-- Card 3: Menunggu Verifikasi -->
                <div class="clay-card-stat p-3.5 sm:p-4 xl:p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="clay-icon-amber w-10 h-10 xl:w-11 xl:h-11 text-[#B45309] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="block text-2xl sm:text-3xl xl:text-4xl font-black text-[#142D21]">
                            {{ $stats['menunggu_verifikasi'] ?? 0 }}
                        </span>
                        <h4 class="font-bold text-xs sm:text-sm text-[#183627] mt-1 truncate">Menunggu Verifikasi</h4>
                        <p class="text-[11px] sm:text-xs text-[#6A7F73] mt-0.5 line-clamp-1">Akan diperiksa oleh admin RT</p>
                    </div>
                </div>

                <!-- Card 4: Laporan Selesai -->
                <div class="clay-card-stat p-3.5 sm:p-4 xl:p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="clay-icon-emerald w-10 h-10 xl:w-11 xl:h-11 text-[#15803D] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="block text-2xl sm:text-3xl xl:text-4xl font-black text-[#142D21]">
                            {{ $stats['selesai'] ?? 0 }}
                        </span>
                        <h4 class="font-bold text-xs sm:text-sm text-[#183627] mt-1 truncate">Laporan Selesai</h4>
                        <p class="text-[11px] sm:text-xs text-[#6A7F73] mt-0.5 line-clamp-1">Terima kasih atas partisipasi Anda</p>
                    </div>
                </div>
            </section>

            <!-- ================= TWO LARGE CONTENT PANELS ================= -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-7">
                
                <!-- Left Panel: Perkembangan Laporan Saya -->
                <div class="clay-card-content p-6 sm:p-7 flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-[#E3DDD1]">
                            <div>
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                                    Setiap Laporan Berarti
                                </span>
                                <h3 class="text-lg font-black text-[#122A1E] mt-0.5">
                                    Perkembangan Laporan Saya
                                </h3>
                            </div>
                            <a href="#semua-laporan" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#245640] hover:text-[#142D21] transition-colors">
                                <span>Semua laporan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>

                        @if ($laporanTerbaru->isNotEmpty())
                            <!-- Dynamic Report List -->
                            <div class="py-3 space-y-3">
                                @foreach ($laporanTerbaru as $lap)
                                    <div class="clay-item-card p-3.5 sm:p-4">
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-[11px] font-black text-[#1E3A2B] font-mono tracking-tight shrink-0">{{ $lap->kode }}</span>
                                                <span class="w-1 h-1 rounded-full bg-[#A2B3A8] shrink-0"></span>
                                                <span class="text-[11px] font-medium text-[#52645B] truncate">{{ $lap->kategori?->nama ?? 'Umum' }}</span>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $lap->status_badge['bg'] }} {{ $lap->status_badge['text'] }} shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $lap->status_badge['dot'] }}"></span>
                                                <span>{{ $lap->status_label }}</span>
                                            </span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-extrabold text-[#122A1E] leading-snug">{{ $lap->judul }}</h4>
                                        <p class="text-xs text-[#52645B] mt-1 line-clamp-2 leading-relaxed">{{ $lap->uraian }}</p>
                                        <div class="flex items-center justify-between gap-2 mt-2.5 pt-2 border-t border-[#E3DDD1] text-[10px] sm:text-[11px] text-[#6A7F73]">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <svg class="w-3.5 h-3.5 text-[#2D6A4F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate">{{ $lap->lokasi_detail }}</span>
                                            </div>
                                            <span class="shrink-0 font-medium">{{ $lap->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State View -->
                            <div class="py-12 sm:py-16 text-center flex flex-col items-center justify-center">
                                <!-- Delicate Outline Leaf Icon -->
                                <div class="w-14 h-14 rounded-2xl bg-[#E8F1EB]/60 flex items-center justify-center text-[#558269] mb-4 shadow-sm border border-white">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                                    </svg>
                                </div>
                                <h4 class="font-extrabold text-sm sm:text-base text-[#153425]">
                                    Belum ada laporan pribadi.
                                </h4>
                                <p class="text-xs text-[#6B8074] mt-1 max-w-xs">
                                    Periksa kembali nanti atau sesuaikan pencarian dan filter.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Helper Note Inside Card: Sunken Debossed Clay Pill -->
                    <div class="clay-sunken-note p-3 sm:p-3.5 mt-4 flex items-start gap-2.5 text-xs text-[#52685C]">
                        <svg class="w-4 h-4 text-[#2D6A4F] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                        </svg>
                        <p class="leading-relaxed text-[11px] sm:text-xs">
                            Laporan yang jelas membantu pengurus menangani masalah lebih cepat. Sertakan lokasi dan kondisi yang Anda temukan.
                        </p>
                    </div>
                </div>

                <!-- Right Panel: Pengumuman Warga -->
                <div class="clay-card-content p-6 sm:p-7 flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-[#E3DDD1]">
                            <div>
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                                    Kabar Dari Lingkungan
                                </span>
                                <h3 class="text-lg font-black text-[#122A1E] mt-0.5">
                                    Pengumuman Warga
                                </h3>
                            </div>
                            <a href="#semua-pengumuman" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#245640] hover:text-[#142D21] transition-colors">
                                <span>Semua</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>

                        @if ($pengumumanTerbaru->isNotEmpty())
                            <!-- Dynamic Announcement List -->
                            <div class="py-3 space-y-3">
                                @foreach ($pengumumanTerbaru as $pengumuman)
                                    <div class="clay-item-card p-3.5 sm:p-4">
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $pengumuman->jenis_badge['bg'] }} {{ $pengumuman->jenis_badge['text'] }}">
                                                    {{ $pengumuman->jenis_badge['label'] }}
                                                </span>
                                                <span class="text-[11px] font-bold text-[#2D6A4F]">
                                                    {{ $pengumuman->wilayah?->tingkat === 'rw' ? 'RW ' . $pengumuman->wilayah->nomor : 'RT ' . $pengumuman->wilayah?->nomor }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] sm:text-[11px] text-[#6A7F73] font-medium shrink-0">
                                                {{ $pengumuman->terbit_at ? $pengumuman->terbit_at->diffForHumans() : $pengumuman->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-extrabold text-[#122A1E] leading-snug">{{ $pengumuman->judul }}</h4>
                                        <p class="text-xs text-[#52645B] mt-1 line-clamp-2 leading-relaxed">{{ $pengumuman->isi }}</p>
                                        <div class="flex items-center justify-between gap-2 mt-2.5 pt-2 border-t border-[#E3DDD1] text-[10px] sm:text-[11px] text-[#6A7F73]">
                                            <span class="truncate">Oleh: <strong class="text-[#142D21] font-semibold">{{ $pengumuman->pembuat?->nama ?? 'Pengurus Lingkungan' }}</strong></span>
                                            <span class="text-[#245640] font-bold shrink-0">Pengumuman Resmi</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State View -->
                            <div class="py-12 sm:py-16 text-center flex flex-col items-center justify-center">
                                <!-- Delicate Outline Leaf Icon -->
                                <div class="w-14 h-14 rounded-2xl bg-[#E8F1EB]/60 flex items-center justify-center text-[#558269] mb-4 shadow-sm border border-white">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                                    </svg>
                                </div>
                                <h4 class="font-extrabold text-sm sm:text-base text-[#153425]">
                                    Belum ada pengumuman untuk wilayah Anda.
                                </h4>
                                <p class="text-xs text-[#6B8074] mt-1 max-w-xs">
                                    Periksa kembali nanti atau sesuaikan pencarian dan filter.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Spacing Placeholder for Equal Balance -->
                    <div class="pt-4 border-t border-[#E8E2D7] invisible flex items-center gap-2 text-xs">
                        <span>Spacer</span>
                    </div>
                </div>
            </section>

            <!-- ================= BOTTOM MAP BANNER ================= -->
            <section class="clay-bottom-banner p-5 sm:p-6 mb-8 relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="clay-icon-mint w-12 h-12 text-[#1B4332] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm sm:text-base text-[#122A1E]">
                                Kenali kondisi di sekitar Anda
                            </h4>
                            <p class="text-xs sm:text-sm text-[#4E6457] mt-0.5">
                                Lihat sebaran laporan warga melalui peta lingkungan RT {{ $rtNomor ?? '02' }}.
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <a href="#peta" class="clay-pill px-5 py-2.5 inline-flex items-center gap-2 text-xs font-bold text-[#153425] hover:text-[#245640] transition-transform">
                            <span>Lihat peta</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <!-- ================= BOTTOM FOOTER ================= -->
        <footer class="pt-6 border-t border-[#E3DDD1] flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#6F8277]">
            <span class="font-medium">Lingkungan Lebih Baik</span>
            <span class="text-[11px] font-semibold">TUNTAS v1.0 • Warga • RW {{ $rwNomor ?? '05' }}</span>
        </footer>
    </main>

    <!-- =========================================================================
         MOBILE BOTTOM NAVIGATION BAR (Visible on screens < lg)
         Ergonomic bottom dock for phones
         ========================================================================= -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 neu-bottom-bar px-4 py-2 flex items-center justify-around" aria-label="Navigasi Bawah">
        <!-- Beranda (Active) -->
        <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[#1B4332] font-black group">
            <div class="w-8 h-8 rounded-xl bg-[#D8F3DC] flex items-center justify-center shadow-inner">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                </svg>
            </div>
            <span class="text-[10px]">Beranda</span>
        </a>

        <!-- Laporan Saya -->
        <a href="#laporan-saya" class="flex flex-col items-center gap-0.5 text-[#5F7368] hover:text-[#1B4332] font-medium group">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                @if (($stats['total'] ?? 0) > 0)
                    <span class="absolute -top-0.5 -right-0.5 px-1 min-w-[14px] h-3.5 rounded-full bg-[#1B4332] text-white text-[9px] font-black flex items-center justify-center leading-none shadow-sm">{{ $stats['total'] }}</span>
                @endif
            </div>
            <span class="text-[10px]">Laporan</span>
        </a>

        <!-- Center Action Floating Clay Button (Buat Laporan) -->
        <a href="#buat-laporan" class="-mt-5 flex flex-col items-center group" aria-label="Buat Laporan Baru">
            <div class="clay-btn-fab w-13 h-13 rounded-full flex items-center justify-center text-white transition-transform active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-[#1B4332] mt-0.5">Lapor</span>
        </a>

        <!-- Pengumuman -->
        <a href="#pengumuman" class="flex flex-col items-center gap-0.5 text-[#5F7368] hover:text-[#1B4332] font-medium group">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <span class="text-[10px]">Kabar</span>
        </a>

        <!-- Profil -->
        <a href="#profil" class="flex flex-col items-center gap-0.5 text-[#5F7368] hover:text-[#1B4332] font-medium group">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="text-[10px]">Profil</span>
        </a>
    </nav>
</div>

<!-- =========================================================================
     VANILLA JAVASCRIPT INTERACTIONS
     Handles mobile drawer, profile dropdown, and Cmd+K / Ctrl+K keyboard shortcut
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

    function toggleNotificationDropdown() {
        const notifMenu = document.getElementById('notification-dropdown-menu');
        const profileMenu = document.getElementById('profile-dropdown-menu');
        const notifBtn = document.getElementById('notification-toggle-btn');
        if (!notifMenu) return;

        // Tutup menu profil jika sedang terbuka
        if (profileMenu && !profileMenu.classList.contains('hidden')) {
            profileMenu.classList.add('hidden');
        }

        const isHidden = notifMenu.classList.contains('hidden');
        notifMenu.classList.toggle('hidden');
        if (notifBtn) {
            notifBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        }
    }

    function toggleProfileDropdown() {
        const menu = document.getElementById('profile-dropdown-menu');
        const notifMenu = document.getElementById('notification-dropdown-menu');
        if (!menu) return;

        // Tutup menu notifikasi jika sedang terbuka
        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
            const notifBtn = document.getElementById('notification-toggle-btn');
            if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
        }

        menu.classList.toggle('hidden');
    }

    // Close dropdowns on click outside
    document.addEventListener('click', function(e) {
        const profileMenu = document.getElementById('profile-dropdown-menu');
        const notifMenu = document.getElementById('notification-dropdown-menu');
        const notifContainer = document.getElementById('notification-dropdown-container');
        const profileContainer = document.querySelector('[onclick="toggleProfileDropdown()"]')?.parentElement;

        if (profileMenu && !profileMenu.classList.contains('hidden')) {
            if (profileContainer && !profileContainer.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        }

        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            if (notifContainer && !notifContainer.contains(e.target)) {
                notifMenu.classList.add('hidden');
                const notifBtn = document.getElementById('notification-toggle-btn');
                if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Mark single notification as read
    function markNotificationAsRead(id) {
        const dot = document.getElementById('notif-dot-' + id);
        const item = document.getElementById('notif-item-' + id);

        // Optimistic UI update
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

    // Mark all notifications as read
    function markAllNotificationsAsRead() {
        const topDot = document.getElementById('notification-unread-dot');
        const badge = document.getElementById('notification-unread-badge');
        const btnMarkAll = document.getElementById('btn-mark-all-read');

        // Optimistic UI update
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

    // Keyboard shortcut: Cmd+K / Ctrl+K focuses the search bar
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

    // Otomatis sembunyikan bottom nav bar saat virtual keyboard terbuka di ponsel
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
@endsection
