@extends('layouts.warga')

@section('header-title', 'Peta Lingkungan')
@section('search-action', route('warga.peta.index'))

@section('warga-content')
@php
    $selectedKatObj = $activeKategori !== 'semua' ? $kategoris->firstWhere('id', $activeKategori) : null;
    $activeKategoriLabel = $selectedKatObj?->short_name ?? 'Semua Kategori';

    $statusLabels = [
        'semua' => 'Semua Status',
        'menunggu' => 'Menunggu Verifikasi',
        'diproses' => 'Sedang Ditangani',
        'konfirmasi' => 'Menunggu Konfirmasi',
        'selesai' => 'Selesai',
    ];
    $activeStatusLabel = $statusLabels[$activeStatus] ?? 'Semua Status';

    // Label zona skematis dinamis dari database AreaLingkungan RT (dengan fallback aman)
    $zoneNames = [
        0 => $areaNames[0] ?? 'Area Barat / Jalan Utama',
        1 => $areaNames[1] ?? 'Gang 1 / Area Tengah',
        2 => $areaNames[2] ?? ('Taman RT ' . ($rtNomor ?? '02')),
        3 => $areaNames[3] ?? 'Gang 2 / Sisi Timur',
        4 => $areaNames[4] ?? ('Sekitar Pos Ronda RT ' . ($rtNomor ?? '02')),
    ];

    $kategoriCounts = collect($mapMarkers)->countBy('kategori_id');
@endphp

<div class="space-y-5">

    <!-- ================= TOP BAR: SUBTITLE + SCOPE & FILTERS + CTA ================= -->
    <section class="flex flex-col xl:flex-row xl:items-center justify-between gap-3.5">
        <p class="text-xs sm:text-sm text-[#4E6457] leading-relaxed">
            Kenali sebaran masalah dan area yang membutuhkan perhatian di lingkungan Anda.
        </p>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Scope Switcher: RT Saya vs RW Induk -->
            <div class="clay-sunken-note p-1 inline-flex items-center gap-1 rounded-2xl">
                <a href="{{ route('warga.peta.index', array_filter(['scope' => 'rt', 'kategori' => $activeKategori !== 'semua' ? $activeKategori : null, 'status' => $activeStatus !== 'semua' ? $activeStatus : null, 'q' => $search !== '' ? $search : null])) }}"
                   class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all {{ $scope === 'rt' ? 'clay-nav-active shadow-xs' : 'text-[#44594D] hover:text-[#122A1E]' }}">
                    RT {{ $rtNomor ?? '02' }}
                </a>
                <a href="{{ route('warga.peta.index', array_filter(['scope' => 'rw', 'kategori' => $activeKategori !== 'semua' ? $activeKategori : null, 'status' => $activeStatus !== 'semua' ? $activeStatus : null, 'q' => $search !== '' ? $search : null])) }}"
                   class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all {{ $scope === 'rw' ? 'clay-nav-active shadow-xs' : 'text-[#44594D] hover:text-[#122A1E]' }}">
                    Satu RW {{ $rwNomor ?? '05' }}
                </a>
            </div>

            <!-- Custom Clay Category Dropdown -->
            <div class="relative" id="peta-filter-kategori-wrap">
                <button type="button"
                        onclick="togglePetaFilter('kategori', event)"
                        class="clay-select-trigger px-3.5 py-2 flex items-center gap-2 text-xs font-bold text-[#142D21]">
                    <span>{{ $activeKategoriLabel }}</span>
                    <svg id="peta-filter-kategori-chevron" class="w-3.5 h-3.5 text-[#52665B] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="peta-filter-kategori-menu"
                     class="clay-dropdown-panel hidden absolute right-0 sm:left-0 mt-2 w-52 p-2 z-40 space-y-1">
                    <a href="{{ route('warga.peta.index', array_filter(['scope' => $scope !== 'rt' ? $scope : null, 'status' => $activeStatus !== 'semua' ? $activeStatus : null, 'q' => $search !== '' ? $search : null])) }}"
                       class="clay-dropdown-option px-3 py-2 flex items-center justify-between text-xs font-bold text-[#142D21] {{ $activeKategori === 'semua' ? 'is-selected' : '' }}">
                        <span>Semua Kategori</span>
                        @if ($activeKategori === 'semua')
                            <span class="text-[#1B4332]">✓</span>
                        @endif
                    </a>
                    @foreach ($kategoris as $kat)
                        <a href="{{ route('warga.peta.index', array_filter(['scope' => $scope !== 'rt' ? $scope : null, 'kategori' => $kat->id, 'status' => $activeStatus !== 'semua' ? $activeStatus : null, 'q' => $search !== '' ? $search : null])) }}"
                           class="clay-dropdown-option px-3 py-2 flex items-center justify-between gap-2 text-xs font-semibold text-[#142D21] {{ $activeKategori === $kat->id ? 'is-selected' : '' }}">
                            <span class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $kat->warna_peta ?? '#2D6A4F' }}"></span>
                                <span class="truncate">{{ $kat->short_name }}</span>
                            </span>
                            @if ($activeKategori === $kat->id)
                                <span class="text-[#1B4332] shrink-0">✓</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Custom Clay Status Dropdown -->
            <div class="relative" id="peta-filter-status-wrap">
                <button type="button"
                        onclick="togglePetaFilter('status', event)"
                        class="clay-select-trigger px-3.5 py-2 flex items-center gap-2 text-xs font-bold text-[#142D21]">
                    <span>{{ $activeStatusLabel }}</span>
                    <svg id="peta-filter-status-chevron" class="w-3.5 h-3.5 text-[#52665B] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="peta-filter-status-menu"
                     class="clay-dropdown-panel hidden absolute right-0 mt-2 w-52 p-2 z-40 space-y-1">
                    @foreach ($statusLabels as $sKey => $sLabel)
                        <a href="{{ route('warga.peta.index', array_filter(['scope' => $scope !== 'rt' ? $scope : null, 'kategori' => $activeKategori !== 'semua' ? $activeKategori : null, 'status' => $sKey !== 'semua' ? $sKey : null, 'q' => $search !== '' ? $search : null])) }}"
                           class="clay-dropdown-option px-3 py-2 flex items-center justify-between text-xs font-semibold text-[#142D21] {{ $activeStatus === $sKey ? 'is-selected' : '' }}">
                            <span>{{ $sLabel }}</span>
                            @if ($activeStatus === $sKey)
                                <span class="text-[#1B4332]">✓</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- CTA Laporkan -->
            <a href="{{ route('warga.laporan.create') }}"
               class="clay-btn-green inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold text-xs">
                <span>Laporkan</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- ================= MAIN MAP & SIDE LIST CLAY PANEL ================= -->
    <section class="clay-card-content p-5 sm:p-6 space-y-4">
        
        <!-- Card Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3.5 border-b border-[#E3DDD1]">
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    KELURAHAN {{ strtoupper($kelurahan ?? 'Sukamaju') }}
                </span>
                <div class="flex items-center gap-2.5 mt-0.5">
                    <h3 class="text-base sm:text-lg font-black text-[#122A1E]">
                        Sebaran Laporan {{ $scope === 'rw' ? 'RW ' . ($rwNomor ?? '05') : 'RT ' . ($rtNomor ?? '02') }}
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-[#E8F1EC] text-[#1B4332] text-[11px] font-extrabold">
                        {{ count($mapMarkers) }} titik
                    </span>
                </div>
            </div>

            @if ($activeKategori !== 'semua' || $activeStatus !== 'semua' || $search !== '')
                <a href="{{ route('warga.peta.index', $scope !== 'rt' ? ['scope' => $scope] : []) }}"
                   class="clay-pill px-3.5 py-1.5 inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1B4332] self-start sm:self-auto">
                    <span>Reset Filter</span>
                    <span>✕</span>
                </a>
            @endif
        </div>

        <!-- 2-Column Interactive Split View -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            <!-- Left Column: Interactive Neighborhood Map Canvas (7/12 Cols) -->
            <div class="lg:col-span-7 xl:col-span-8 flex flex-col">
                <div id="map-viewport"
                     class="relative w-full h-[420px] sm:h-[480px] xl:h-[520px] rounded-3xl bg-[#F5F2EA] border-2 border-white/90 shadow-inner overflow-hidden select-none cursor-grab">
                    
                    <!-- Transformable Canvas (Pan & Zoom) -->
                    <div id="map-canvas"
                         class="absolute inset-0 origin-center transition-transform duration-150 ease-out">
                        
                        <!-- Schematic Neighborhood Streets, Parks & Drainage SVG -->
                        <svg class="w-full h-full" viewBox="0 0 800 540" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                            <!-- Soft Base Ground -->
                            <rect width="800" height="540" fill="#F2EFE6" />

                            <!-- Dashed RT Perimeter Border -->
                            <rect x="34" y="28" width="732" height="484" rx="22" fill="none" stroke="#B7C8BC" stroke-width="1.5" stroke-dasharray="6 6" />

                            <!-- Neighborhood Blocks / Clusters (Soft Sage Plots) -->
                            <g fill="#E3EAE1">
                                <rect x="64" y="54" width="96" height="84" rx="14" />
                                <rect x="208" y="54" width="110" height="84" rx="14" />
                                <rect x="364" y="54" width="110" height="84" rx="14" />
                                <rect x="644" y="54" width="94" height="84" rx="14" />

                                <rect x="64" y="196" width="96" height="92" rx="14" />
                                <rect x="208" y="196" width="110" height="92" rx="14" />
                                <rect x="364" y="196" width="110" height="92" rx="14" />
                                <rect x="644" y="196" width="94" height="92" rx="14" />

                                <rect x="64" y="346" width="96" height="92" rx="14" />
                                <rect x="208" y="346" width="110" height="92" rx="14" />
                                <rect x="364" y="346" width="110" height="92" rx="14" />
                                <rect x="644" y="346" width="94" height="92" rx="14" />
                            </g>

                            <!-- Main Horizontal Streets -->
                            <rect x="0" y="154" width="800" height="28" fill="#FAF8F4" />
                            <rect x="0" y="304" width="800" height="28" fill="#FAF8F4" />
                            <!-- Vertical Streets & Alleys -->
                            <rect x="172" y="0" width="24" height="540" fill="#FAF8F4" />
                            <rect x="330" y="0" width="22" height="540" fill="#FAF8F4" />
                            <rect x="490" y="0" width="22" height="540" fill="#FAF8F4" />
                            <rect x="608" y="0" width="24" height="540" fill="#FAF8F4" />

                            <!-- Central Park / Taman RT -->
                            <rect x="518" y="102" width="84" height="106" rx="18" fill="#C4DBC8" stroke="#FFFFFF" stroke-width="2" />

                            <!-- Drainage Canal / River Curve (Bottom) -->
                            <path d="M 0 436 Q 210 398, 420 452 T 800 442" fill="none" stroke="#B6D3E3" stroke-width="18" stroke-linecap="round" opacity="0.85" />

                            <!-- Dynamic Area Labels from Database (AreaLingkungan) -->
                            <g font-family="Plus Jakarta Sans, sans-serif" font-size="11" font-weight="700" fill="#63796E">
                                <text x="112" y="100" text-anchor="middle">{{ \Illuminate\Support\Str::limit($zoneNames[0], 20) }}</text>
                                <text x="419" y="172" text-anchor="middle" fill="#4E6457">{{ \Illuminate\Support\Str::limit($zoneNames[1], 24) }}</text>
                                <text x="560" y="159" text-anchor="middle" fill="#1B4332" font-weight="800">{{ \Illuminate\Support\Str::limit($zoneNames[2], 18) }}</text>
                                <text x="263" y="322" text-anchor="middle" fill="#4E6457">{{ \Illuminate\Support\Str::limit($zoneNames[3], 24) }}</text>
                                <text x="419" y="246" text-anchor="middle">{{ \Illuminate\Support\Str::limit($zoneNames[4], 20) }}</text>
                            </g>
                        </svg>

                        <!-- Interactive Plot Marker Pins -->
                        @foreach ($mapMarkers as $marker)
                            <button type="button"
                                    data-map-marker
                                    data-marker-id="{{ $marker['id'] }}"
                                    data-kategori-id="{{ $marker['kategori_id'] }}"
                                    onclick="selectMapMarker('{{ $marker['id'] }}', event)"
                                    style="left: {{ $marker['x'] }}%; top: {{ $marker['y'] }}%;"
                                    class="group absolute -translate-x-1/2 -translate-y-1/2 z-10 focus:outline-none cursor-pointer"
                                    aria-label="Titik laporan {{ $marker['kode'] }}: {{ $marker['judul'] }}">
                                
                                @if ($marker['is_priority'])
                                    <span class="absolute inset-0 -m-1.5 rounded-full animate-ping opacity-35" style="background-color: {{ $marker['warna'] }}"></span>
                                @endif

                                <!-- Pin Body -->
                                <span data-pin-body
                                      class="relative w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 border-white shadow-md flex items-center justify-center transition-transform duration-200 group-hover:scale-125"
                                      style="background-color: {{ $marker['warna'] }}">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                </span>

                                <!-- Hover Tooltip -->
                                <span class="pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-150 absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2.5 py-1 rounded-xl bg-[#122A1E] text-white text-[10px] font-bold whitespace-nowrap shadow-lg z-30">
                                    {{ $marker['kode'] }} • {{ \Illuminate\Support\Str::limit($marker['judul'], 28) }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Top-Left Compass Badge -->
                    <div class="absolute top-3 left-3 z-20 clay-pill w-8 h-8 flex items-center justify-center text-[11px] font-black text-[#1B4332]" title="Utara">
                        U
                    </div>

                    <!-- Floating Selected Marker Card (Shown on Marker/List Click) -->
                    <div id="map-selected-card"
                         class="hidden absolute top-3 left-14 right-3 sm:right-auto sm:w-80 z-30 clay-dropdown-panel p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span id="selected-pin-kode" class="text-[10px] font-black font-mono text-[#1E3A2B] bg-[#EFECE5] px-2 py-0.5 rounded-md"></span>
                                <span id="selected-pin-status" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"></span>
                            </div>
                            <button type="button"
                                    onclick="clearSelectedMarker()"
                                    class="text-xs font-bold text-[#6A7F73] hover:text-[#122A1E] px-1 cursor-pointer"
                                    aria-label="Tutup kartu titik">
                                ✕
                            </button>
                        </div>

                        <h4 id="selected-pin-judul" class="text-xs sm:text-sm font-extrabold text-[#122A1E] leading-snug line-clamp-2"></h4>

                        <p id="selected-pin-lokasi" class="text-[11px] text-[#52645B] truncate"></p>

                        <div class="pt-1.5 border-t border-[#E3DDD1] flex items-center justify-between gap-2">
                            <span id="selected-pin-waktu" class="text-[10px] font-medium text-[#6A7F73]"></span>
                            <button type="button"
                                    id="selected-pin-detail-btn"
                                    class="clay-btn-green px-3 py-1 text-[11px] font-bold text-white cursor-pointer">
                                Detail &amp; Linimasa →
                            </button>
                        </div>
                    </div>

                    <!-- Bottom-Left Disclaimer Pill -->
                    <div class="absolute bottom-3 left-3 z-20 px-3 py-1 rounded-full bg-white/80 backdrop-blur-xs border border-white text-[10px] font-semibold text-[#60776C] shadow-xs pointer-events-none">
                        Denah skematis lingkungan • Klik titik untuk detail
                    </div>

                    <!-- Bottom-Right Zoom & Reset Controls -->
                    <div class="absolute bottom-3 right-3 z-20 flex flex-col gap-1.5">
                        <button type="button"
                                onclick="zoomMap(0.2)"
                                class="clay-pill w-8 h-8 flex items-center justify-center text-sm font-black text-[#1B4332] cursor-pointer"
                                title="Perbesar peta"
                                aria-label="Perbesar peta">
                            +
                        </button>
                        <button type="button"
                                onclick="zoomMap(-0.2)"
                                class="clay-pill w-8 h-8 flex items-center justify-center text-sm font-black text-[#1B4332] cursor-pointer"
                                title="Perkecil peta"
                                aria-label="Perkecil peta">
                            −
                        </button>
                        <button type="button"
                                onclick="resetMapView()"
                                class="clay-pill w-8 h-8 flex items-center justify-center text-xs font-black text-[#1B4332] cursor-pointer"
                                title="Reset posisi peta"
                                aria-label="Reset posisi peta">
                            ↻
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Synchronized Scrollable Area Report List (5/12 Cols) -->
            <div class="lg:col-span-5 xl:col-span-4 flex flex-col h-[420px] sm:h-[480px] xl:h-[520px] bg-[#FAF8F4]/70 rounded-3xl p-4 border border-white/80 shadow-inner">
                <div class="pb-3 border-b border-[#E3DDD1] shrink-0">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                        LAPORAN DI WILAYAH ANDA
                    </span>
                    <h4 class="text-sm sm:text-base font-black text-[#122A1E] mt-0.5">
                        {{ count($mapMarkers) }} laporan ditemukan
                    </h4>
                </div>

                @if (count($mapMarkers) > 0)
                    <div class="flex-1 overflow-y-auto py-3 space-y-2.5 pr-1 custom-scrollbar min-h-0" id="peta-side-list">
                        @foreach ($mapMarkers as $marker)
                            <div data-side-card
                                 data-marker-id="{{ $marker['id'] }}"
                                 data-kategori-id="{{ $marker['kategori_id'] }}"
                                 onclick="selectMapMarker('{{ $marker['id'] }}', event)"
                                 class="clay-item-card p-3 cursor-pointer transition-all">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $marker['warna'] }}"></span>
                                        <span class="text-[10px] font-black font-mono text-[#1E3A2B] truncate">{{ $marker['kode'] }}</span>
                                        @if ($scope === 'rw')
                                            <span class="px-1.5 py-0.5 rounded bg-[#E8F1EC] text-[#1B4332] text-[9px] font-bold shrink-0">RT {{ $marker['rt_nomor'] }}</span>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold {{ $marker['status_badge']['bg'] }} {{ $marker['status_badge']['text'] }} shrink-0">
                                        <span class="w-1 h-1 rounded-full {{ $marker['status_badge']['dot'] }}"></span>
                                        <span>{{ $marker['status_label'] }}</span>
                                    </span>
                                </div>

                                <h5 class="text-xs font-extrabold text-[#122A1E] leading-snug line-clamp-1">
                                    {{ $marker['judul'] }}
                                </h5>

                                <div class="flex items-center justify-between gap-2 mt-1.5 text-[10px] text-[#60776C]">
                                    <span class="truncate">{{ $marker['lokasi_detail'] }}</span>
                                    <span class="shrink-0 font-medium">{{ $marker['waktu'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center px-3">
                        <div class="clay-icon-mint w-12 h-12 flex items-center justify-center text-[#558269] mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <h5 class="text-xs sm:text-sm font-extrabold text-[#153425]">
                            Tidak ada data yang sesuai
                        </h5>
                        <p class="text-[11px] text-[#6B8074] mt-1 max-w-xs">
                            Coba ubah cakupan wilayah ke RW {{ $rwNomor ?? '05' }}, atau sesuaikan filter kategori dan status.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bottom Interactive Category Legend -->
        <div class="pt-3 border-t border-[#E3DDD1] flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($kategoris as $kat)
                    @php
                        $katCount = $kategoriCounts->get($kat->id, 0);
                        $isLegendActive = $activeKategori === $kat->id;
                    @endphp
                    <a href="{{ route('warga.peta.index', array_filter(['scope' => $scope !== 'rt' ? $scope : null, 'kategori' => $isLegendActive ? null : $kat->id, 'status' => $activeStatus !== 'semua' ? $activeStatus : null, 'q' => $search !== '' ? $search : null])) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all {{ $isLegendActive ? 'bg-[#1B4332] text-white shadow-xs' : 'bg-[#F5F2EA] text-[#30483C] hover:bg-[#EAE4D8]' }}">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $kat->warna_peta ?? '#2D6A4F' }}"></span>
                        <span>{{ $kat->short_name }}</span>
                        <span class="opacity-70">({{ $katCount }})</span>
                    </a>
                @endforeach
            </div>
            <span class="text-[10px] text-[#6A7F73] font-medium">
                Klik kategori pada legenda untuk memfilter titik di peta
            </span>
        </div>
    </section>
</div>

<!-- =========================================================================
     DETAIL & TIMELINE MODAL (Shared Interactive Experience)
     ========================================================================= -->
<div id="report-detail-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-6 bg-[#122A1E]/45 backdrop-blur-xs"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-report-title"
     onclick="closeReportDetail(event)">
    <div class="clay-card-content w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden"
         onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 pb-4 border-b border-[#E3DDD1] flex items-start justify-between gap-4 shrink-0">
            <div class="min-w-0 space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="modal-report-kode" class="text-[11px] font-black font-mono text-[#1E3A2B] bg-[#EFECE5] px-2.5 py-0.5 rounded-lg"></span>
                    <span id="modal-report-kategori" class="text-[11px] font-bold text-[#30483C] bg-white px-2.5 py-0.5 rounded-full border border-[#E3DDD1]"></span>
                    <span id="modal-report-status" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold"></span>
                </div>
                <h3 id="modal-report-title" class="text-base sm:text-xl font-black text-[#122A1E] leading-snug"></h3>
                <p id="modal-report-date" class="text-[11px] text-[#60776C] font-medium"></p>
            </div>

            <button type="button"
                    onclick="closeReportDetail()"
                    class="clay-pill w-9 h-9 flex items-center justify-center text-[#52665B] hover:text-[#122A1E] shrink-0 cursor-pointer"
                    aria-label="Tutup detail laporan">
                ✕
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-5 flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="clay-sunken-note p-3">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">Lokasi</span>
                    <span id="modal-report-lokasi" class="block text-xs font-bold text-[#142D21] mt-0.5"></span>
                    <span id="modal-report-blok" class="hidden inline-block mt-1 px-2 py-0.5 rounded-full bg-[#D8ECE0] text-[#1B4332] text-[10px] font-bold"></span>
                </div>
                <div class="clay-sunken-note p-3">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">Dampak</span>
                    <span id="modal-report-dampak" class="block text-xs font-bold text-[#142D21] mt-0.5"></span>
                </div>
                <div class="clay-sunken-note p-3">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">Urgensi</span>
                    <span id="modal-report-urgensi" class="block text-xs font-bold text-[#142D21] mt-0.5"></span>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-1.5">Uraian Permasalahan</h4>
                <p id="modal-report-uraian" class="text-xs sm:text-sm text-[#30483C] leading-relaxed whitespace-pre-line"></p>
            </div>

            <div id="modal-photos-wrap" class="hidden">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-2">Foto Pendukung</h4>
                <div id="modal-photos-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
            </div>

            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-3">Linimasa Penanganan</h4>
                <div id="modal-timeline-list" class="space-y-3 border-l-2 border-[#D4DFD7] pl-4 ml-1.5"></div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     VANILLA JS FOR INTERACTIVE MAP (ZOOM, PAN, MARKERS, FILTER, MODAL)
     ========================================================================= -->
<script>
    (function () {
        const markersList = @json($mapMarkers ?? []);
        const reportDetails = @json($reportDetailMap ?? []);

        const markerMap = {};
        markersList.forEach(function (m) {
            markerMap[m.id] = m;
        });

        // 1. Filter Dropdowns
        window.togglePetaFilter = function (kind, event) {
            if (event) event.stopPropagation();
            const other = kind === 'kategori' ? 'status' : 'kategori';
            const otherMenu = document.getElementById('peta-filter-' + other + '-menu');
            const otherChevron = document.getElementById('peta-filter-' + other + '-chevron');
            if (otherMenu) otherMenu.classList.add('hidden');
            if (otherChevron) otherChevron.classList.remove('rotate-180');

            const menu = document.getElementById('peta-filter-' + kind + '-menu');
            const chevron = document.getElementById('peta-filter-' + kind + '-chevron');
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180', isHidden);
        };

        document.addEventListener('click', function (e) {
            ['kategori', 'status'].forEach(function (kind) {
                const wrap = document.getElementById('peta-filter-' + kind + '-wrap');
                const menu = document.getElementById('peta-filter-' + kind + '-menu');
                const chevron = document.getElementById('peta-filter-' + kind + '-chevron');
                if (menu && !menu.classList.contains('hidden') && (!wrap || !wrap.contains(e.target))) {
                    menu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            });
        });

        // 2. Map Pan & Zoom Engine
        const viewport = document.getElementById('map-viewport');
        const canvas = document.getElementById('map-canvas');
        let scale = 1;
        let panX = 0;
        let panY = 0;
        let isDragging = false;
        let startX = 0;
        let startY = 0;

        function applyMapTransform() {
            if (!canvas) return;
            canvas.style.transform = 'translate(' + panX + 'px, ' + panY + 'px) scale(' + scale + ')';
        }

        window.zoomMap = function (delta) {
            scale = Math.max(1, Math.min(2.2, Number((scale + delta).toFixed(2))));
            if (scale === 1) {
                panX = 0;
                panY = 0;
            }
            applyMapTransform();
        };

        window.resetMapView = function () {
            scale = 1;
            panX = 0;
            panY = 0;
            applyMapTransform();
            window.clearSelectedMarker();
        };

        if (viewport) {
            viewport.addEventListener('mousedown', function (e) {
                if (e.target.closest('button')) return;
                isDragging = true;
                startX = e.clientX - panX;
                startY = e.clientY - panY;
                viewport.classList.add('cursor-grabbing');
                viewport.classList.remove('cursor-grab');
            });

            window.addEventListener('mousemove', function (e) {
                if (!isDragging) return;
                const maxPan = (scale - 1) * 200 + 40;
                panX = Math.max(-maxPan, Math.min(maxPan, e.clientX - startX));
                panY = Math.max(-maxPan, Math.min(maxPan, e.clientY - startY));
                applyMapTransform();
            });

            window.addEventListener('mouseup', function () {
                if (!isDragging) return;
                isDragging = false;
                viewport.classList.remove('cursor-grabbing');
                viewport.classList.add('cursor-grab');
            });
        }

        // 3. Select Map Marker & Highlight Synchronized Side Card
        window.selectMapMarker = function (markerId, event) {
            if (event) event.stopPropagation();
            const marker = markerMap[markerId];
            if (!marker) return;

            // Highlight Pin
            document.querySelectorAll('[data-map-marker]').forEach(function (el) {
                const body = el.querySelector('[data-pin-body]');
                const isTarget = el.dataset.markerId === String(markerId);
                if (body) {
                    body.classList.toggle('scale-125', isTarget);
                    body.classList.toggle('ring-4', isTarget);
                    body.classList.toggle('ring-[#1B4332]/40', isTarget);
                }
                el.style.zIndex = isTarget ? '25' : '10';
            });

            // Highlight Side Card
            document.querySelectorAll('[data-side-card]').forEach(function (card) {
                const isTarget = card.dataset.markerId === String(markerId);
                card.classList.toggle('ring-2', isTarget);
                card.classList.toggle('ring-[#2D6A4F]', isTarget);
                if (isTarget) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            // Populate Floating Map Card
            const card = document.getElementById('map-selected-card');
            if (card) {
                document.getElementById('selected-pin-kode').textContent = marker.kode || '';
                document.getElementById('selected-pin-judul').textContent = marker.judul || '';
                document.getElementById('selected-pin-lokasi').textContent = marker.lokasi_detail + (marker.blok ? ' • ' + marker.blok : '');
                document.getElementById('selected-pin-waktu').textContent = marker.waktu || '';

                const statusEl = document.getElementById('selected-pin-status');
                if (statusEl && marker.status_badge) {
                    statusEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ' + marker.status_badge.bg + ' ' + marker.status_badge.text;
                    statusEl.innerHTML = '';
                    const dot = document.createElement('span');
                    dot.className = 'w-1.5 h-1.5 rounded-full ' + marker.status_badge.dot;
                    const txt = document.createElement('span');
                    txt.textContent = marker.status_label || '';
                    statusEl.appendChild(dot);
                    statusEl.appendChild(txt);
                }

                const detailBtn = document.getElementById('selected-pin-detail-btn');
                if (detailBtn) {
                    detailBtn.onclick = function () {
                        window.openReportDetail(marker.id);
                    };
                }

                card.classList.remove('hidden');
            }
        };

        window.clearSelectedMarker = function () {
            const card = document.getElementById('map-selected-card');
            if (card) card.classList.add('hidden');

            document.querySelectorAll('[data-map-marker]').forEach(function (el) {
                const body = el.querySelector('[data-pin-body]');
                if (body) {
                    body.classList.remove('scale-125', 'ring-4', 'ring-[#1B4332]/40');
                }
                el.style.zIndex = '10';
            });

            document.querySelectorAll('[data-side-card]').forEach(function (el) {
                el.classList.remove('ring-2', 'ring-[#2D6A4F]');
            });
        };

        // 4. Detail Modal
        window.openReportDetail = function (reportId) {
            const data = reportDetails[reportId];
            if (!data) return;

            const modal = document.getElementById('report-detail-modal');
            if (!modal) return;

            document.getElementById('modal-report-kode').textContent = data.kode || '-';
            document.getElementById('modal-report-kategori').textContent = data.kategori_full || data.kategori || 'Umum';
            document.getElementById('modal-report-title').textContent = data.judul || '-';
            document.getElementById('modal-report-date').textContent = data.tanggal || '';
            document.getElementById('modal-report-lokasi').textContent = data.lokasi_detail || '-';
            document.getElementById('modal-report-dampak').textContent = data.dampak || '-';
            document.getElementById('modal-report-urgensi').textContent = data.urgensi || '-';
            document.getElementById('modal-report-uraian').textContent = data.uraian || '-';

            const blokEl = document.getElementById('modal-report-blok');
            if (blokEl) {
                if (data.blok) {
                    blokEl.textContent = data.blok;
                    blokEl.classList.remove('hidden');
                } else {
                    blokEl.classList.add('hidden');
                }
            }

            const statusEl = document.getElementById('modal-report-status');
            if (statusEl && data.status_badge) {
                statusEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold ' + data.status_badge.bg + ' ' + data.status_badge.text;
                statusEl.innerHTML = '';
                const dot = document.createElement('span');
                dot.className = 'w-1.5 h-1.5 rounded-full ' + data.status_badge.dot;
                const txt = document.createElement('span');
                txt.textContent = data.status_label || '';
                statusEl.appendChild(dot);
                statusEl.appendChild(txt);
            }

            const photosWrap = document.getElementById('modal-photos-wrap');
            const photosGrid = document.getElementById('modal-photos-grid');
            if (photosWrap && photosGrid) {
                photosGrid.innerHTML = '';
                if (Array.isArray(data.photos) && data.photos.length > 0) {
                    data.photos.forEach(function (photo) {
                        const link = document.createElement('a');
                        link.href = photo.url;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        link.className = 'block rounded-2xl overflow-hidden border-2 border-white shadow-sm';
                        const img = document.createElement('img');
                        img.src = photo.url;
                        img.alt = photo.name || 'Foto lampiran';
                        img.className = 'w-full h-44 object-cover';
                        link.appendChild(img);
                        photosGrid.appendChild(link);
                    });
                    photosWrap.classList.remove('hidden');
                } else {
                    photosWrap.classList.add('hidden');
                }
            }

            const timelineList = document.getElementById('modal-timeline-list');
            if (timelineList) {
                timelineList.innerHTML = '';
                (data.timeline || []).forEach(function (item) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    const dotColor = item.tone === 'amber' ? 'bg-[#D97706]' : (item.tone === 'blue' ? 'bg-[#0284C7]' : 'bg-[#2D6A4F]');
                    const dot = document.createElement('span');
                    dot.className = 'absolute -left-[22px] top-1.5 w-3 h-3 rounded-full border-2 border-white shadow-2xs ' + dotColor;

                    const header = document.createElement('div');
                    header.className = 'flex flex-wrap items-center justify-between gap-2';
                    const title = document.createElement('span');
                    title.className = 'text-xs font-extrabold text-[#122A1E]';
                    title.textContent = item.title || '';
                    const time = document.createElement('span');
                    time.className = 'text-[10px] font-medium text-[#6A7F73]';
                    time.textContent = item.time_label || '';
                    header.appendChild(title);
                    header.appendChild(time);

                    const actor = document.createElement('span');
                    actor.className = 'block text-[11px] font-semibold text-[#2D6A4F] mt-0.5';
                    actor.textContent = item.actor || '';

                    const note = document.createElement('p');
                    note.className = 'text-xs text-[#4E6457] mt-0.5 leading-relaxed';
                    note.textContent = item.note || '';

                    wrapper.appendChild(dot);
                    wrapper.appendChild(header);
                    wrapper.appendChild(actor);
                    wrapper.appendChild(note);
                    timelineList.appendChild(wrapper);
                });
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };

        window.closeReportDetail = function () {
            const modal = document.getElementById('report-detail-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        };

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.closeReportDetail();
            }
        });
    })();
</script>
@endsection
