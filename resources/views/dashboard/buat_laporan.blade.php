@extends('layouts.app')

@section('content')
<div class="min-h-screen dashboard-canvas flex flex-col lg:flex-row antialiased relative selection:bg-[#245640] selection:text-white"
     x-data="{ 
         mobileMenuOpen: false, 
         profileDropdownOpen: false,
         selectedKategori: '{{ old('kategori_id', $kategoris->first()->id ?? '') }}',
         charCount: {{ strlen(old('uraian', '')) }},
         photoPreview: null,
         photoName: '',
         photoSize: '',
         handlePhotoSelect(event) {
             const file = event.target.files[0];
             if (!file) return;
             if (file.size > 5 * 1024 * 1024) {
                 alert('Ukuran foto melebihi batas 5 MB. Silakan pilih foto dengan ukuran lebih kecil.');
                 event.target.value = '';
                 return;
             }
             this.photoName = file.name;
             this.photoSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
             const reader = new FileReader();
             reader.onload = (e) => {
                 this.photoPreview = e.target.result;
             };
             reader.readAsDataURL(file);
         },
         clearPhoto() {
             this.photoPreview = null;
             this.photoName = '';
             this.photoSize = '';
             const input = document.getElementById('foto-input');
             if (input) input.value = '';
         }
     }">

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
                
                <a href="{{ route('warga.dashboard') }}" class="neu-nav-item px-4 py-3 flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('warga.laporan.create') }}" class="clay-nav-active px-4 py-3 flex items-center gap-3 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <a href="{{ route('warga.dashboard') }}#laporan-saya" class="neu-nav-item px-4 py-3 flex items-center justify-between font-semibold text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Saya</span>
                    </div>
                    <span class="neu-pill-tag px-2 py-0.5 text-xs font-bold text-[#142D21]">{{ $totalLaporanSaya ?? 0 }}</span>
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

        <!-- Sidebar Bottom Helper Card -->
        <div class="mt-6">
            <div class="clay-helper-card p-3.5 relative overflow-hidden">
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
         DESKTOP SIDEBAR (Visible on lg+)
         ========================================================================= -->
    <aside class="hidden lg:flex w-64 xl:w-72 shrink-0 p-4 xl:p-6 flex-col justify-between neu-sidebar sticky top-0 h-screen overflow-y-auto no-scrollbar">
        <div class="space-y-4 xl:space-y-5">
            <!-- Brand Logo & Name -->
            <div class="flex items-center gap-3 px-1 py-1">
                <div class="clay-badge-leaf w-10 h-10 xl:w-11 xl:h-11 flex items-center justify-center text-[#1B4332] shrink-0">
                    <svg class="w-5 h-5 xl:w-6 xl:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-black text-lg xl:text-xl text-[#122A1E] tracking-tight leading-none">TUNTAS</h1>
                    <span class="text-[10px] xl:text-[11px] text-[#55695D] font-medium">Lingkungan Lebih Baik</span>
                </div>
            </div>

            <!-- Wilayah Anda Card -->
            <div class="clay-territory-card p-3 xl:p-4">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E] mb-1.5 xl:mb-2">Wilayah Anda</span>
                <div class="flex items-center gap-3">
                    <div class="clay-icon-mint w-9 h-9 xl:w-10 xl:h-10 text-[#1B4332] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block font-black text-xs xl:text-sm text-[#142D21]">RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}</span>
                        <span class="block text-[10px] xl:text-xs text-[#60776C]">Kelurahan {{ $kelurahan ?? 'Sukamaju' }}</span>
                    </div>
                </div>
            </div>

            <!-- Ruang Warga Navigation -->
            <nav class="space-y-1">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E] px-2 mb-1.5 xl:mb-2">Ruang Warga</span>
                
                <!-- Beranda -->
                <a href="{{ route('warga.dashboard') }}" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-semibold text-xs xl:text-sm">
                    <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span>Beranda</span>
                </a>

                <!-- Buat Laporan (ACTIVE) -->
                <a href="{{ route('warga.laporan.create') }}" class="clay-nav-active px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center gap-3 font-bold text-xs xl:text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <!-- Laporan Saya -->
                <a href="{{ route('warga.dashboard') }}#laporan-saya" class="neu-nav-item px-3.5 py-2 xl:px-4 xl:py-2.5 flex items-center justify-between font-semibold text-xs xl:text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-[#52665B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Saya</span>
                    </div>
                    <span class="neu-pill-tag px-2 py-0.5 text-[10px] xl:text-[11px] font-bold text-[#142D21]">{{ $totalLaporanSaya ?? 0 }}</span>
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
                                Buat Laporan
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
                    <!-- Search Form (Quick Jump to Dashboard Search) -->
                    <form action="{{ route('warga.dashboard') }}" method="GET" class="relative w-44 sm:w-56 md:w-64 lg:w-48 xl:w-64 m-0">
                        <div class="neu-search-input flex items-center px-3 py-1.5 sm:px-3.5 sm:py-2">
                            <svg class="w-3.5 h-3.5 text-[#73877C] shrink-0 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" 
                                   name="q"
                                   id="dashboard-search" 
                                   placeholder="Cari laporan..." 
                                   class="w-full bg-transparent border-0 p-0 text-xs text-[#122A1E] placeholder-[#798D82] focus:outline-none focus:ring-0">
                            <span class="hidden sm:inline-block px-1.5 py-0.5 rounded bg-white/70 text-[9px] font-mono text-[#6A7F73] border border-black/5 shadow-2xs">⌘K</span>
                        </div>
                    </form>

                    <!-- Notification Button -->
                    <div class="relative" id="notification-dropdown-container">
                        <button type="button" 
                                id="notif-toggle-btn"
                                onclick="toggleNotificationDropdown(event)" 
                                class="clay-pill w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center text-[#1E3A2B] relative transition-transform active:scale-95"
                                aria-label="Notifikasi Pengguna"
                                aria-expanded="false">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if (($unreadNotifikasiCount ?? 0) > 0)
                                <span id="notif-badge-indicator" class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#1B4332] text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white">
                                    {{ $unreadNotifikasiCount }}
                                </span>
                            @endif
                        </button>
                    </div>

                    <!-- User Profile Dropdown Toggle -->
                    <div class="relative" id="profile-dropdown-container">
                        <button type="button" 
                                id="profile-toggle-btn"
                                onclick="toggleProfileDropdown(event)" 
                                class="clay-pill pl-1 pr-2.5 sm:pr-3 py-1 flex items-center gap-2 text-left group shrink-0 transition-transform active:scale-95"
                                aria-label="Menu Pengguna"
                                aria-expanded="false">
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

            <!-- ================= SUB-HEADER / SECTION TITLE ================= -->
            <section class="mb-5 xl:mb-6">
                <span class="block text-[11px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    TUNTAS • RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}
                </span>
                <h1 class="text-2xl sm:text-3xl xl:text-4xl font-black text-[#122A1E] tracking-tight mt-0.5">
                    Buat Laporan
                </h1>
                <p class="text-xs sm:text-sm text-[#55695D] mt-1 max-w-2xl leading-relaxed">
                    Ceritakan masalah di sekitar Anda. Pengurus RT akan membantu menindaklanjutinya.
                </p>
            </section>

            <!-- ================= MAIN TWO COLUMNS GRID ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- ================= LEFT COLUMN: FORM CARD (8 Cols) ================= -->
                <div class="lg:col-span-8">
                    <div class="clay-card-content p-5 sm:p-7 xl:p-8">
                        
                        <!-- Card Header -->
                        <div class="pb-5 border-b border-[#E3DDD1] mb-6">
                            <span class="block text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-[#63796E]">
                                LANGKAH KECIL, DAMPAK BESAR
                            </span>
                            <h2 class="text-lg sm:text-xl font-black text-[#122A1E] mt-0.5">
                                Informasi Permasalahan
                            </h2>
                        </div>

                        <!-- Validation Errors Alert (if any) -->
                        @if ($errors->any())
                            <div class="mb-6 p-4 rounded-2xl bg-red-50/90 border border-red-200 text-red-800 shadow-sm">
                                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm mb-1.5">
                                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    <span>Mohon lengkapi formulir dengan benar:</span>
                                </div>
                                <ul class="list-disc list-inside text-xs space-y-0.5 text-red-700 pl-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('warga.laporan.store') }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              id="report-form"
                              class="space-y-6">
                            @csrf

                            <!-- ================= FIELD 1: KATEGORI MASALAH ================= -->
                            <div>
                                <label class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2.5">
                                    Kategori masalah <span class="text-red-500">*</span>
                                </label>

                                <input type="hidden" 
                                       name="kategori_id" 
                                       id="kategori_id" 
                                       :value="selectedKategori" 
                                       required>

                                <!-- 5 Selectable Category Cards Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 sm:gap-3">
                                    @foreach ($kategoris as $kat)
                                        <button type="button"
                                                @click="selectedKategori = '{{ $kat->id }}'"
                                                :class="{ 'is-selected ring-2 ring-[#2D6A4F]': selectedKategori === '{{ $kat->id }}' }"
                                                class="clay-category-card p-3 sm:p-3.5 flex flex-col items-center justify-center text-center group focus:outline-none"
                                                aria-label="Pilih Kategori {{ $kat->short_name }}">
                                            
                                            <!-- Category Icon -->
                                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-105"
                                                 :class="selectedKategori === '{{ $kat->id }}' ? 'bg-[#2D6A4F] text-white shadow-sm' : 'bg-[#EAE4D8]/80 text-[#30483C]'">
                                                
                                                @if ($kat->icon_type === 'sampah')
                                                    <!-- Trash Can Icon -->
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                @elseif ($kat->icon_type === 'drainase')
                                                    <!-- Drain / Water Crosshair Icon -->
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4m10-10h-4M6 12H2"/>
                                                    </svg>
                                                @elseif ($kat->icon_type === 'jalan')
                                                    <!-- Map / Road Icon -->
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                    </svg>
                                                @elseif ($kat->icon_type === 'penerangan')
                                                    <!-- Streetlight / Bell Icon -->
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                @else
                                                    <!-- Public Facilities / Bench Icon -->
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <rect x="3" y="7" width="18" height="13" rx="2"></rect>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                    </svg>
                                                @endif
                                            </div>

                                            <span class="text-xs sm:text-[13px] font-bold text-[#142D21] tracking-tight leading-tight">
                                                {{ $kat->short_name }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- ================= FIELD 2: JUDUL LAPORAN ================= -->
                            <div>
                                <label for="judul" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                    Judul laporan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="judul" 
                                       id="judul" 
                                       value="{{ old('judul') }}"
                                       placeholder="Contoh: Saluran air tersumbat di Gang 2"
                                       required
                                       class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0">
                            </div>

                            <!-- ================= FIELD 3: LOKASI & AREA LINGKUNGAN (2 COLS) ================= -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="lokasi_detail" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                        Lokasi masalah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="lokasi_detail" 
                                           id="lokasi_detail" 
                                           value="{{ old('lokasi_detail') }}"
                                           placeholder="Nama jalan, gang, atau patokan"
                                           required
                                           class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0">
                                </div>

                                <div>
                                    <label for="blok" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                        Area lingkungan
                                    </label>
                                    <div class="relative">
                                        <select name="blok" 
                                                id="blok" 
                                                class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] appearance-none cursor-pointer focus:ring-0">
                                            @foreach ($daftarBlok as $blokOption)
                                                <option value="{{ $blokOption }}" {{ old('blok', 'Blok A') === $blokOption ? 'selected' : '' }}>
                                                    {{ $blokOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#52665B]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= FIELD 4: DAMPAK & URGENSI (2 COLS) ================= -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="dampak" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                        Dampak bagi warga <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="dampak" 
                                                id="dampak" 
                                                required
                                                class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] appearance-none cursor-pointer focus:ring-0">
                                            <option value="rendah" {{ old('dampak') === 'rendah' ? 'selected' : '' }}>
                                                Ringan — tidak mengganggu aktivitas utama
                                            </option>
                                            <option value="sedang" {{ old('dampak', 'sedang') === 'sedang' ? 'selected' : '' }}>
                                                Sedang — akses / kenyamanan terganggu
                                            </option>
                                            <option value="tinggi" {{ old('dampak') === 'tinggi' ? 'selected' : '' }}>
                                                Berat — membahayakan keselamatan / menghentikan aktivitas
                                            </option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#52665B]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="urgensi" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                        Urgensi penanganan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="urgensi" 
                                                id="urgensi" 
                                                required
                                                class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] appearance-none cursor-pointer focus:ring-0">
                                            <option value="rendah" {{ old('urgensi') === 'rendah' ? 'selected' : '' }}>
                                                Rendah — penanganan berkala / fleksibel
                                            </option>
                                            <option value="sedang" {{ old('urgensi', 'sedang') === 'sedang' ? 'selected' : '' }}>
                                                Sedang — perlu segera ditangani
                                            </option>
                                            <option value="tinggi" {{ old('urgensi') === 'tinggi' ? 'selected' : '' }}>
                                                Tinggi — butuh penanganan cepat
                                            </option>
                                            <option value="mendesak" {{ old('urgensi') === 'mendesak' ? 'selected' : '' }}>
                                                Darurat — butuh tindakan langsung
                                            </option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#52665B]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= FIELD 5: URAIAN MASALAH ================= -->
                            <div>
                                <label for="uraian" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                    Uraian masalah <span class="text-red-500">*</span>
                                </label>
                                <textarea name="uraian" 
                                          id="uraian" 
                                          rows="5"
                                          @input="charCount = $event.target.value.length"
                                          maxlength="1500"
                                          placeholder="Jelaskan kondisi, sejak kapan terjadi, dan dampaknya bagi warga..."
                                          required
                                          class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0 leading-relaxed">{{ old('uraian') }}</textarea>
                                
                                <div class="flex items-center justify-between text-[11px] text-[#697D72] mt-1.5 px-1 font-medium">
                                    <span>
                                        <span x-text="charCount">0</span>/1500 karakter • minimal 20 karakter
                                    </span>
                                    <span :class="charCount >= 20 ? 'text-[#1B4332] font-bold' : 'text-[#A07040]'">
                                        <span x-show="charCount < 20">Butuh <span x-text="20 - charCount"></span> karakter lagi</span>
                                        <span x-show="charCount >= 20">✓ Panjang memadai</span>
                                    </span>
                                </div>
                            </div>

                            <!-- ================= FIELD 6: FOTO PENDUKUNG ================= -->
                            <div>
                                <label class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                    Foto pendukung <span class="text-[11px] font-normal text-[#6B8074]">(opsional)</span>
                                </label>

                                <input type="file" 
                                       name="foto" 
                                       id="foto-input" 
                                       accept="image/jpeg,image/png,image/webp"
                                       @change="handlePhotoSelect($event)"
                                       class="hidden">

                                <!-- Upload Dropzone Container -->
                                <div class="clay-upload-dropzone p-6 sm:p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                                     onclick="document.getElementById('foto-input').click()">
                                    
                                    <!-- State 1: No Photo Selected -->
                                    <div x-show="!photoPreview" class="flex flex-col items-center">
                                        <div class="clay-pill w-12 h-12 flex items-center justify-center text-[#2D6A4F] mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-extrabold text-xs sm:text-sm text-[#142D21]">
                                            Pilih foto kondisi lingkungan
                                        </h4>
                                        <p class="text-[11px] text-[#63796E] mt-1 font-medium">
                                            JPG, PNG, atau WebP • maksimal 5 MB
                                        </p>
                                    </div>

                                    <!-- State 2: Photo Selected & Preview Visible -->
                                    <div x-show="photoPreview" class="w-full flex flex-col items-center space-y-3" style="display: none;">
                                        <div class="relative max-w-xs rounded-2xl overflow-hidden border-2 border-white shadow-md">
                                            <img :src="photoPreview" alt="Pratinjau Foto" class="w-full h-44 object-cover">
                                            <button type="button" 
                                                    @click.stop="clearPhoto()" 
                                                    class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center text-xs font-bold shadow-md hover:bg-red-700 transition-colors"
                                                    title="Hapus foto">
                                                ✕
                                            </button>
                                        </div>
                                        <div class="text-center">
                                            <span class="block text-xs font-bold text-[#142D21]" x-text="photoName"></span>
                                            <span class="block text-[10px] text-[#63796E]" x-text="photoSize"></span>
                                            <span class="inline-block text-[11px] font-bold text-[#2D6A4F] mt-1 hover:underline">
                                                Klik untuk mengganti foto
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[10px] sm:text-[11px] text-[#718579] mt-2 px-1">
                                    Foto hanya dibaca sebagai pratinjau lokal selama sesi, disimpan aman untuk verifikasi pengurus RT.
                                </p>
                            </div>

                            <!-- ================= FOOTER ACTIONS ================= -->
                            <div class="pt-5 border-t border-[#E3DDD1] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <span class="text-[11px] font-semibold text-[#6A7F73]">
                                    * Wajib diisi • Dikirim ke RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}
                                </span>

                                <div class="flex items-center gap-3 self-end sm:self-auto">
                                    <a href="{{ route('warga.dashboard') }}" 
                                       class="clay-btn-cancel px-5 py-2.5 text-xs sm:text-sm font-bold">
                                        Batalkan
                                    </a>

                                    <button type="submit" 
                                            class="clay-btn-green inline-flex items-center gap-2 px-6 py-2.5 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all">
                                        <span>Kirim laporan</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ================= RIGHT COLUMN: GUIDANCE CARDS (4 Cols) ================= -->
                <div class="lg:col-span-4 space-y-5">
                    
                    <!-- Card 1: Laporan yang Baik, Penanganan Lebih Tepat -->
                    <div class="clay-card-content p-5 sm:p-6">
                        <!-- Mint Leaf Badge Icon -->
                        <div class="clay-icon-mint w-11 h-11 text-[#1B4332] flex items-center justify-center shrink-0 mb-4">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                                <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                            </svg>
                        </div>

                        <h3 class="text-base sm:text-lg font-black text-[#122A1E] leading-snug">
                            Laporan yang baik,<br>penanganan lebih tepat.
                        </h3>
                        
                        <p class="text-xs text-[#52645B] mt-1.5 leading-relaxed">
                            Bantu kami memahami masalah yang Anda temukan.
                        </p>

                        <!-- 4 Step Guide -->
                        <div class="mt-5 space-y-3">
                            <!-- Step 1 -->
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60">
                                    1
                                </div>
                                <p class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                                    Pilih kategori yang paling sesuai
                                </p>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60">
                                    2
                                </div>
                                <p class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                                    Tulis lokasi dan patokan dengan jelas
                                </p>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60">
                                    3
                                </div>
                                <p class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                                    Jelaskan kondisi secara singkat dan objektif
                                </p>
                            </div>

                            <!-- Step 4 -->
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60">
                                    4
                                </div>
                                <p class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                                    Hindari data pribadi pada foto atau uraian
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Setelah Laporan Dikirim -->
                    <div class="clay-helper-card p-5">
                        <span class="block text-[10px] font-black uppercase tracking-wider text-[#4E6659] mb-1.5">
                            SETELAH LAPORAN DIKIRIM
                        </span>
                        <p class="text-xs text-[#203D2E] leading-relaxed font-medium">
                            Admin RT akan memeriksa laporan. Anda dapat memantau perkembangan dan memberikan konfirmasi hasil melalui <span class="font-bold text-[#142D21]">Laporan Saya</span>.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <!-- ================= PAGE FOOTER ================= -->
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
        <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-0.5">
            <svg class="w-5 h-5 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
            </svg>
            <span class="text-[10px]">Beranda</span>
        </a>

        <!-- Laporan -->
        <a href="{{ route('warga.dashboard') }}#laporan-saya" class="flex flex-col items-center gap-0.5">
            <div class="relative">
                <svg class="w-5 h-5 text-[#52665B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-[10px]">Laporan</span>
        </a>

        <!-- Center Action Floating Clay Button (Buat Laporan - ACTIVE STATE) -->
        <a href="{{ route('warga.laporan.create') }}" class="-mt-5 flex flex-col items-center group" aria-label="Buat Laporan Baru">
            <div class="clay-btn-fab w-13 h-13 rounded-full flex items-center justify-center text-white ring-4 ring-[#EAE5DB] transition-transform active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <span class="text-[10px] font-black text-[#1B4332] mt-0.5">Lapor</span>
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
     JAVASCRIPT INTERACTIVITY (Mobile Drawer & Dropdowns)
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

    function toggleProfileDropdown(event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('profile-dropdown-menu');
        const notifMenu = document.getElementById('notification-dropdown-menu');
        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
        }
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function toggleNotificationDropdown(event) {
        if (event) event.stopPropagation();
        const profileMenu = document.getElementById('profile-dropdown-menu');
        if (profileMenu && !profileMenu.classList.contains('hidden')) {
            profileMenu.classList.add('hidden');
        }
        // Redirect to dashboard notifications if clicked
        window.location.href = "{{ route('warga.dashboard') }}";
    }

    document.addEventListener('click', function(event) {
        const profileContainer = document.getElementById('profile-dropdown-container');
        const profileMenu = document.getElementById('profile-dropdown-menu');
        if (profileContainer && profileMenu && !profileContainer.contains(event.target)) {
            profileMenu.classList.add('hidden');
        }
    });
</script>
@endsection
