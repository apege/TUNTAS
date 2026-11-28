@extends('layouts.warga')

@section('header-title', 'Dashboard Warga')

@section('warga-content')
    <!-- ================= HERO BANNER ================= -->
    <section class="clay-hero-banner p-4 sm:p-5 lg:p-6 mb-5 xl:mb-6 relative overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-12 items-center gap-4 sm:gap-6 relative z-10">
            
            <!-- Left Hero Content -->
            <div class="md:col-span-7 space-y-3 sm:space-y-3.5">
                <!-- Date & Location Badge (Clean, no AI slop pulse dot) -->
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/40 backdrop-blur-sm text-[10px] sm:text-[11px] font-bold text-[#143525] border border-white/50 shadow-2xs">
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
                    <a href="{{ route('warga.laporan.create') }}" 
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
        
        <!-- Left Panel: Perkembangan Laporan Saya (Balanced Scrollable Height) -->
        <div class="clay-card-content p-5 sm:p-6 h-[480px] xl:h-[510px] flex flex-col justify-between">
            <!-- Header (Fixed at Top) -->
            <div class="flex items-center justify-between pb-3.5 border-b border-[#E3DDD1] shrink-0">
                <div>
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                        Setiap Laporan Berarti
                    </span>
                    <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
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
                <!-- Dynamic Report List with Smooth Internal Scroll -->
                <div class="flex-1 overflow-y-auto py-3 space-y-3 pr-1.5 custom-scrollbar min-h-0">
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
                <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
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

            <!-- Bottom Helper Note (Fixed at Bottom) -->
            <div class="clay-sunken-note p-3 mt-3 flex items-start gap-2.5 text-xs text-[#52685C] shrink-0">
                <svg class="w-4 h-4 text-[#2D6A4F] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                    <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                </svg>
                <p class="leading-relaxed text-[11px] sm:text-xs">
                    Laporan yang jelas membantu pengurus menangani masalah lebih cepat. Sertakan lokasi dan kondisi yang Anda temukan.
                </p>
            </div>
        </div>

        <!-- Right Panel: Pengumuman Warga (Balanced Scrollable Height) -->
        <div class="clay-card-content p-5 sm:p-6 h-[480px] xl:h-[510px] flex flex-col justify-between">
            <!-- Header (Fixed at Top) -->
            <div class="flex items-center justify-between pb-3.5 border-b border-[#E3DDD1] shrink-0">
                <div>
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                        Kabar Dari Lingkungan
                    </span>
                    <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
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
                <!-- Dynamic Announcement List with Smooth Internal Scroll -->
                <div class="flex-1 overflow-y-auto py-3 space-y-3 pr-1.5 custom-scrollbar min-h-0">
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
                <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
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

            <!-- Bottom Helper Note (Fixed at Bottom - Perfectly Balanced with Left Panel) -->
            <div class="clay-sunken-note p-3 mt-3 flex items-start gap-2.5 text-xs text-[#52685C] shrink-0">
                <svg class="w-4 h-4 text-[#2D6A4F] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="leading-relaxed text-[11px] sm:text-xs">
                    Pengumuman diterbitkan langsung oleh Ketua RT & RW untuk keterbukaan informasi dan koordinasi seluruh warga.
                </p>
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
@endsection
