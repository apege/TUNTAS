@extends('layouts.warga')

@section('header-title', 'Peta Lingkungan')
@section('search-action', route('warga.peta.index'))

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        /* Harmonize OpenStreetMap tiles gently with TUNTAS warm clay aesthetic */
        #leaflet-map .leaflet-tile-pane {
            filter: sepia(0.08) saturate(0.94) brightness(1.01);
        }
        .tuntas-pin-wrapper {
            background: transparent;
            border: none;
        }
    </style>
@endpush

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

    $kategoriCounts = collect($mapMarkers)->countBy('kategori_id');
@endphp

<div class="space-y-5">

    <!-- ================= TOP BAR: SUBTITLE + SCOPE & FILTERS + CTA ================= -->
    <section class="flex flex-col xl:flex-row xl:items-center justify-between gap-3.5">
        <p class="text-xs sm:text-sm text-[#4E6457] leading-relaxed">
            Pantau sebaran laporan warga di peta geografis nyata untuk penanganan yang cepat dan tepat sasaran.
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
                     class="clay-dropdown-panel hidden absolute right-0 sm:left-0 mt-2 w-52 p-2 z-[1000] space-y-1">
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
                     class="clay-dropdown-panel hidden absolute right-0 mt-2 w-52 p-2 z-[1000] space-y-1">
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

    <!-- ================= MAIN REAL-WORLD MAP & SIDE LIST CLAY PANEL ================= -->
    <section class="clay-card-content p-5 sm:p-6 space-y-4">
        
        <!-- Card Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3.5 border-b border-[#E3DDD1]">
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    KELURAHAN {{ strtoupper($kelurahan ?? 'Sukamaju') }} • OPENSTREETMAP REAL-TIME
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
            
            <!-- Left Column: Real OpenStreetMap Leaflet Viewport (7/12 or 8/12 Cols) -->
            <div class="lg:col-span-7 xl:col-span-8 flex flex-col">
                <div class="relative w-full h-[430px] sm:h-[490px] xl:h-[530px] rounded-3xl border-2 border-white/90 shadow-inner overflow-hidden bg-[#EAE5DB]">
                    
                    <!-- Leaflet Map Mount -->
                    <div id="leaflet-map" class="w-full h-full z-10"></div>

                    <!-- Floating Selected Marker Card -->
                    <div id="map-selected-card"
                         class="hidden absolute top-3 left-3 right-14 sm:right-auto sm:w-80 z-[500] clay-dropdown-panel p-3.5 space-y-2">
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

                        <div class="flex items-center justify-between gap-2 text-[10px]">
                            <span id="selected-pin-gps-badge" class="font-semibold text-[#2D6A4F]"></span>
                            <span id="selected-pin-waktu" class="font-medium text-[#6A7F73]"></span>
                        </div>

                        <div class="pt-1.5 border-t border-[#E3DDD1] flex justify-end">
                            <button type="button"
                                    id="selected-pin-detail-btn"
                                    class="clay-btn-green px-3.5 py-1.5 text-[11px] font-bold text-white cursor-pointer">
                                Detail &amp; Linimasa →
                            </button>
                        </div>
                    </div>

                    <!-- Custom Clay Controls (Bottom-Right: GPS, Zoom+, Zoom-, Fit Bounds) -->
                    <div class="absolute bottom-7 right-3 z-[500] flex flex-col gap-1.5">
                        <button type="button"
                                onclick="centerOnUserGps()"
                                class="clay-pill w-9 h-9 flex items-center justify-center text-[#1B4332] cursor-pointer"
                                title="Pusatkan ke Lokasi Saya (GPS)"
                                aria-label="Pusatkan ke Lokasi Saya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3m10-10h-3M5 12H2"/>
                            </svg>
                        </button>
                        <button type="button"
                                onclick="zoomLeaflet(1)"
                                class="clay-pill w-9 h-9 flex items-center justify-center text-sm font-black text-[#1B4332] cursor-pointer"
                                title="Perbesar peta"
                                aria-label="Perbesar peta">
                            +
                        </button>
                        <button type="button"
                                onclick="zoomLeaflet(-1)"
                                class="clay-pill w-9 h-9 flex items-center justify-center text-sm font-black text-[#1B4332] cursor-pointer"
                                title="Perkecil peta"
                                aria-label="Perkecil peta">
                            −
                        </button>
                        <button type="button"
                                onclick="resetLeafletView()"
                                class="clay-pill w-9 h-9 flex items-center justify-center text-xs font-black text-[#1B4332] cursor-pointer"
                                title="Tampilkan semua titik"
                                aria-label="Tampilkan semua titik">
                            ↻
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Synchronized Scrollable Area Report List (5/12 or 4/12 Cols) -->
            <div class="lg:col-span-5 xl:col-span-4 flex flex-col h-[430px] sm:h-[490px] xl:h-[530px] bg-[#FAF8F4]/70 rounded-3xl p-4 border border-white/80 shadow-inner">
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
                Klik titik di peta atau item di sebelah kanan untuk meninjau detail
            </span>
        </div>
    </section>
</div>

<!-- =========================================================================
     DETAIL & TIMELINE MODAL
     ========================================================================= -->
<div id="report-detail-modal"
     class="fixed inset-0 z-[1200] hidden items-center justify-center p-3 sm:p-6 bg-[#122A1E]/45 backdrop-blur-xs"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-report-title"
     onclick="closeReportDetail(event)">
    <div class="clay-card-content w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden"
         onclick="event.stopPropagation()">
        
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
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    (function () {
        const markersList = @json($mapMarkers ?? []);
        const reportDetails = @json($reportDetailMap ?? []);
        const mapCenter = @json($mapCenter ?? ['lat' => -6.95839, 'lng' => 107.61025]);

        const markerMap = {};
        const leafletMarkers = {};
        let userGpsMarker = null;

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

        // 2. Initialize Real-World Leaflet + OpenStreetMap Map
        const mapEl = document.getElementById('leaflet-map');
        let map = null;
        let boundsGroup = null;

        function buildPinHtml(marker, isSelected) {
            const scaleClass = isSelected ? 'scale-125 ring-4 ring-[#1B4332]/40' : '';
            const pulse = marker.is_priority
                ? '<span class="absolute inset-0 -m-1.5 rounded-full animate-ping opacity-35" style="background-color:' + marker.warna + '"></span>'
                : '';

            return '<div class="relative flex items-center justify-center w-8 h-8">' +
                pulse +
                '<span class="relative w-7 h-7 rounded-full border-2 border-white shadow-md flex items-center justify-center transition-transform duration-200 ' + scaleClass + '" style="background-color:' + marker.warna + '">' +
                '<span class="w-2 h-2 rounded-full bg-white"></span>' +
                '</span></div>';
        }

        if (mapEl && typeof L !== 'undefined') {
            map = L.map('leaflet-map', {
                center: [mapCenter.lat, mapCenter.lng],
                zoom: 17,
                zoomControl: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
            }).addTo(map);

            boundsGroup = L.featureGroup().addTo(map);

            markersList.forEach(function (marker) {
                const icon = L.divIcon({
                    className: 'tuntas-pin-wrapper',
                    html: buildPinHtml(marker, false),
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                const lMarker = L.marker([marker.lat, marker.lng], { icon: icon })
                    .bindTooltip(marker.kode + ' • ' + marker.judul, {
                        direction: 'top',
                        offset: [0, -12]
                    })
                    .on('click', function () {
                        window.selectMapMarker(marker.id);
                    });

                lMarker.addTo(boundsGroup);
                leafletMarkers[marker.id] = lMarker;
            });

            if (markersList.length > 1) {
                map.fitBounds(boundsGroup.getBounds().pad(0.25), { maxZoom: 17 });
            } else if (markersList.length === 1) {
                map.setView([markersList[0].lat, markersList[0].lng], 17);
            }

            map.on('click', function () {
                window.clearSelectedMarker();
            });
        }

        window.zoomLeaflet = function (delta) {
            if (!map) return;
            if (delta > 0) map.zoomIn();
            else map.zoomOut();
        };

        window.resetLeafletView = function () {
            window.clearSelectedMarker();
            if (!map) return;
            if (boundsGroup && markersList.length > 1) {
                map.fitBounds(boundsGroup.getBounds().pad(0.25), { maxZoom: 17 });
            } else if (markersList.length === 1) {
                map.flyTo([markersList[0].lat, markersList[0].lng], 17);
            } else {
                map.flyTo([mapCenter.lat, mapCenter.lng], 17);
            }
        };

        window.centerOnUserGps = function () {
            if (!map || !navigator.geolocation) {
                alert('Perangkat atau browser Anda tidak mendukung pelacakan GPS.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    map.flyTo([lat, lng], 18);

                    if (userGpsMarker) {
                        userGpsMarker.setLatLng([lat, lng]);
                    } else {
                        const myIcon = L.divIcon({
                            className: 'tuntas-pin-wrapper',
                            html: '<div class="relative flex items-center justify-center w-6 h-6"><span class="absolute inset-0 rounded-full bg-sky-500/30 animate-ping"></span><span class="relative w-4 h-4 rounded-full bg-sky-600 border-2 border-white shadow-md"></span></div>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        });
                        userGpsMarker = L.marker([lat, lng], { icon: myIcon })
                            .bindTooltip('Lokasi Anda Saat Ini', { direction: 'top', offset: [0, -10] })
                            .addTo(map);
                    }
                },
                function () {
                    alert('Tidak dapat mengakses lokasi GPS Anda. Pastikan izin lokasi browser telah diaktifkan.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        };

        // 3. Select Marker & Fly To Coordinate
        window.selectMapMarker = function (markerId, event) {
            if (event) event.stopPropagation();
            const marker = markerMap[markerId];
            if (!marker) return;

            if (map && leafletMarkers[markerId]) {
                map.flyTo([marker.lat, marker.lng], Math.max(map.getZoom(), 18), { duration: 0.5 });
            }

            Object.keys(leafletMarkers).forEach(function (id) {
                const m = markerMap[id];
                const isTarget = String(id) === String(markerId);
                leafletMarkers[id].setIcon(L.divIcon({
                    className: 'tuntas-pin-wrapper',
                    html: buildPinHtml(m, isTarget),
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                }));
                leafletMarkers[id].setZIndexOffset(isTarget ? 500 : 0);
            });

            document.querySelectorAll('[data-side-card]').forEach(function (card) {
                const isTarget = card.dataset.markerId === String(markerId);
                card.classList.toggle('ring-2', isTarget);
                card.classList.toggle('ring-[#2D6A4F]', isTarget);
                if (isTarget) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            const card = document.getElementById('map-selected-card');
            if (card) {
                document.getElementById('selected-pin-kode').textContent = marker.kode || '';
                document.getElementById('selected-pin-judul').textContent = marker.judul || '';
                document.getElementById('selected-pin-lokasi').textContent = marker.lokasi_detail + (marker.blok ? ' • ' + marker.blok : '');
                document.getElementById('selected-pin-waktu').textContent = marker.waktu || '';
                document.getElementById('selected-pin-gps-badge').textContent = marker.has_exact_gps
                    ? '📍 Koordinat GPS Akurat'
                    : '📍 Perkiraan Area RT ' + (marker.rt_nomor || '');

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

            Object.keys(leafletMarkers).forEach(function (id) {
                const m = markerMap[id];
                leafletMarkers[id].setIcon(L.divIcon({
                    className: 'tuntas-pin-wrapper',
                    html: buildPinHtml(m, false),
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                }));
                leafletMarkers[id].setZIndexOffset(0);
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
@endpush
