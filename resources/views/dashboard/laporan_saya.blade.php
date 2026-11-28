@extends('layouts.warga')

@section('header-title', 'Laporan Saya')
@section('search-action', route('warga.laporan.index'))

@section('warga-content')
@php
    $statusTabs = [
        'semua' => ['label' => 'Semua', 'count' => $tabCounts['semua'] ?? 0],
        'menunggu' => ['label' => 'Menunggu', 'count' => $tabCounts['menunggu'] ?? 0],
        'diproses' => ['label' => 'Diproses', 'count' => $tabCounts['diproses'] ?? 0],
        'konfirmasi' => ['label' => 'Konfirmasi', 'count' => $tabCounts['konfirmasi'] ?? 0],
        'selesai' => ['label' => 'Selesai', 'count' => $tabCounts['selesai'] ?? 0],
    ];

    if (($tabCounts['ditolak'] ?? 0) > 0 || $activeStatus === 'ditolak') {
        $statusTabs['ditolak'] = ['label' => 'Ditolak', 'count' => $tabCounts['ditolak'] ?? 0];
    }

    $selectedKatObj = $activeKategori !== 'semua' ? $kategoris->firstWhere('id', $activeKategori) : null;
    $activeKategoriLabel = $selectedKatObj?->short_name ?? 'Semua Kategori';

    $sortLabels = [
        'terbaru' => 'Terbaru',
        'terlama' => 'Terlama',
        'urgensi' => 'Prioritas Tertinggi',
    ];
    $activeSortLabel = $sortLabels[$sort] ?? 'Terbaru';
@endphp

<div class="space-y-5">

    <!-- ================= TOP BAR: INTRO + FILTER CONTROLS + CTA ================= -->
    <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
        <p class="text-xs sm:text-sm text-[#4E6457] leading-relaxed">
            Pantau setiap perkembangan laporan Anda dari verifikasi RT hingga tuntas di lapangan.
        </p>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Custom Clay Category Filter Dropdown -->
            <div class="relative" id="filter-kategori-wrap">
                <button type="button"
                        id="filter-kategori-trigger"
                        onclick="toggleFilterDropdown('kategori', event)"
                        class="clay-select-trigger px-3.5 py-2 flex items-center gap-2 text-xs font-bold text-[#142D21]">
                    <svg class="w-3.5 h-3.5 text-[#2D6A4F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>{{ $activeKategoriLabel }}</span>
                    <svg id="filter-kategori-chevron" class="w-3.5 h-3.5 text-[#52665B] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="filter-kategori-menu"
                     class="clay-dropdown-panel hidden absolute right-0 sm:left-0 mt-2 w-52 p-2 z-30 space-y-1">
                    <a href="{{ route('warga.laporan.index', array_filter(['status' => $activeStatus !== 'semua' ? $activeStatus : null, 'q' => $search !== '' ? $search : null, 'sort' => $sort !== 'terbaru' ? $sort : null])) }}"
                       class="clay-dropdown-option px-3 py-2 flex items-center justify-between text-xs font-bold text-[#142D21] {{ $activeKategori === 'semua' ? 'is-selected' : '' }}">
                        <span>Semua Kategori</span>
                        @if ($activeKategori === 'semua')
                            <span class="text-[#1B4332]">✓</span>
                        @endif
                    </a>
                    @foreach ($kategoris as $kat)
                        <a href="{{ route('warga.laporan.index', array_filter(['status' => $activeStatus !== 'semua' ? $activeStatus : null, 'kategori' => $kat->id, 'q' => $search !== '' ? $search : null, 'sort' => $sort !== 'terbaru' ? $sort : null])) }}"
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

            <!-- Custom Clay Sort Dropdown -->
            <div class="relative" id="filter-sort-wrap">
                <button type="button"
                        id="filter-sort-trigger"
                        onclick="toggleFilterDropdown('sort', event)"
                        class="clay-select-trigger px-3.5 py-2 flex items-center gap-2 text-xs font-bold text-[#142D21]">
                    <svg class="w-3.5 h-3.5 text-[#2D6A4F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    <span>{{ $activeSortLabel }}</span>
                    <svg id="filter-sort-chevron" class="w-3.5 h-3.5 text-[#52665B] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="filter-sort-menu"
                     class="clay-dropdown-panel hidden absolute right-0 mt-2 w-48 p-2 z-30 space-y-1">
                    @foreach ($sortLabels as $sortKey => $sortLabel)
                        <a href="{{ route('warga.laporan.index', array_filter(['status' => $activeStatus !== 'semua' ? $activeStatus : null, 'kategori' => $activeKategori !== 'semua' ? $activeKategori : null, 'q' => $search !== '' ? $search : null, 'sort' => $sortKey !== 'terbaru' ? $sortKey : null])) }}"
                           class="clay-dropdown-option px-3 py-2 flex items-center justify-between text-xs font-semibold text-[#142D21] {{ $sort === $sortKey ? 'is-selected' : '' }}">
                            <span>{{ $sortLabel }}</span>
                            @if ($sort === $sortKey)
                                <span class="text-[#1B4332]">✓</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Primary CTA -->
            <a href="{{ route('warga.laporan.create') }}"
               class="clay-btn-green inline-flex items-center gap-2 px-4 py-2 text-white font-bold text-xs shadow-md hover:shadow-lg transition-all">
                <span>Buat Laporan</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- ================= SINGLE-ROW CLAY STATUS TABS RAIL ================= -->
    <section aria-label="Filter status laporan">
        <div class="clay-sunken-note p-1.5 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
            @foreach ($statusTabs as $tabKey => $tabMeta)
                @php
                    $isActiveTab = $activeStatus === $tabKey;
                    $tabUrl = route('warga.laporan.index', array_filter([
                        'status' => $tabKey !== 'semua' ? $tabKey : null,
                        'kategori' => $activeKategori !== 'semua' ? $activeKategori : null,
                        'q' => $search !== '' ? $search : null,
                        'sort' => $sort !== 'terbaru' ? $sort : null,
                    ]));
                @endphp
                <a href="{{ $tabUrl }}"
                   class="shrink-0 px-3.5 py-2 rounded-2xl inline-flex items-center gap-2 text-xs font-bold transition-all {{ $isActiveTab ? 'clay-nav-active shadow-sm' : 'text-[#44594D] hover:text-[#122A1E] hover:bg-white/50' }}">
                    <span>{{ $tabMeta['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold leading-none {{ $isActiveTab ? 'bg-white/20 text-white' : ($tabKey === 'konfirmasi' && $tabMeta['count'] > 0 ? 'bg-[#EDE9FE] text-[#5B21B6]' : 'bg-[#EAE4D8] text-[#142D21]') }}">
                        {{ $tabMeta['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- ================= CALLOUT BANNER: KONFIRMASI WARGA ================= -->
    @if (($tabCounts['konfirmasi'] ?? 0) > 0 && $activeStatus !== 'konfirmasi')
        <section class="clay-helper-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="clay-icon-mint w-10 h-10 text-[#5B21B6] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-[#122A1E]">
                        {{ $tabCounts['konfirmasi'] }} laporan menunggu konfirmasi Anda
                    </h3>
                    <p class="text-[11px] sm:text-xs text-[#4E6457]">
                        Petugas telah mencatat penyelesaian. Periksa hasilnya dan konfirmasi apakah masalah sudah tuntas.
                    </p>
                </div>
            </div>
            <a href="{{ route('warga.laporan.index', ['status' => 'konfirmasi']) }}"
               class="clay-pill px-4 py-2 text-xs font-bold text-[#1B4332] shrink-0 self-start sm:self-auto">
                Tinjau Sekarang →
            </a>
        </section>
    @endif

    <!-- ================= MAIN REPORT LIST PANEL ================= -->
    <section class="clay-card-content p-5 sm:p-6">
        <!-- Panel Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-[#E3DDD1] mb-5">
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    RIWAYAT PELAPORAN RT {{ $rtNomor ?? '02' }} / RW {{ $rwNomor ?? '05' }}
                </span>
                <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
                    {{ $laporans->total() }} Laporan Ditemukan
                </h3>
            </div>

            @if ($activeStatus !== 'semua' || $activeKategori !== 'semua' || $search !== '')
                <a href="{{ route('warga.laporan.index') }}"
                   class="clay-pill px-3.5 py-1.5 inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1B4332] self-start sm:self-auto">
                    <span>Reset Filter</span>
                    <span>✕</span>
                </a>
            @endif
        </div>

        @if ($laporans->isNotEmpty())
            <div class="space-y-4">
                @foreach ($laporans as $lap)
                    @php
                        $step = $lap->progress_step;
                        $needsConfirm = $lap->status_penerimaan === 'diverifikasi' && $lap->kasus?->status === 'menunggu_konfirmasi';
                        $firstPhoto = $lap->lampirans->first();
                        $hasVisiblePhoto = $firstPhoto && \Illuminate\Support\Facades\Storage::disk('public')->exists($firstPhoto->storage_key);
                    @endphp
                    <article class="clay-item-card p-4 sm:p-5">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            
                            <!-- Left/Main Info -->
                            <div class="flex-1 min-w-0 space-y-2.5">
                                <!-- Top Meta Row -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[11px] font-black text-[#1E3A2B] font-mono tracking-tight bg-[#EFECE5] px-2.5 py-0.5 rounded-lg border border-white/70">
                                        {{ $lap->kode }}
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/80 text-[#30483C] border border-[#E3DDD1]">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $lap->kategori?->warna_peta ?? '#2D6A4F' }}"></span>
                                        <span>{{ $lap->kategori?->nama ?? 'Umum' }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold {{ $lap->status_badge['bg'] }} {{ $lap->status_badge['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $lap->status_badge['dot'] }}"></span>
                                        <span>{{ $lap->status_label }}</span>
                                    </span>
                                </div>

                                <!-- Title & Excerpt -->
                                <div>
                                    <h4 class="text-sm sm:text-base font-extrabold text-[#122A1E] leading-snug">
                                        {{ $lap->judul }}
                                    </h4>
                                    <p class="text-xs sm:text-[13px] text-[#52645B] mt-1 line-clamp-2 leading-relaxed">
                                        {{ $lap->uraian }}
                                    </p>
                                </div>

                                <!-- Compact 4-Step Progress Bar -->
                                @if ($step > 0)
                                    <div class="pt-1">
                                        <div class="grid grid-cols-4 gap-1.5 max-w-md">
                                            <div class="space-y-1">
                                                <div class="h-1.5 rounded-full {{ $step >= 1 ? 'bg-[#2D6A4F]' : 'bg-[#DFDAD0]' }}"></div>
                                                <span class="block text-[9px] sm:text-[10px] font-bold {{ $step >= 1 ? 'text-[#1B4332]' : 'text-[#798D82]' }} truncate">1. Terkirim</span>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="h-1.5 rounded-full {{ $step >= 2 ? 'bg-[#2D6A4F]' : 'bg-[#DFDAD0]' }}"></div>
                                                <span class="block text-[9px] sm:text-[10px] font-bold {{ $step >= 2 ? 'text-[#1B4332]' : 'text-[#798D82]' }} truncate">2. Verifikasi</span>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="h-1.5 rounded-full {{ $step >= 3 ? 'bg-[#2D6A4F]' : 'bg-[#DFDAD0]' }}"></div>
                                                <span class="block text-[9px] sm:text-[10px] font-bold {{ $step >= 3 ? 'text-[#1B4332]' : 'text-[#798D82]' }} truncate">3. Ditangani</span>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="h-1.5 rounded-full {{ $step >= 5 ? 'bg-[#16A34A]' : ($step === 4 ? 'bg-[#7C3AED]' : 'bg-[#DFDAD0]') }}"></div>
                                                <span class="block text-[9px] sm:text-[10px] font-bold {{ $step >= 5 ? 'text-[#166534]' : ($step === 4 ? 'text-[#5B21B6]' : 'text-[#798D82]') }} truncate">
                                                    {{ $step === 4 ? '4. Konfirmasi' : '4. Selesai' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Bottom Meta: Location + Area + Date -->
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 pt-2 border-t border-[#E3DDD1]/80 text-[11px] text-[#60776C]">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#2D6A4F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $lap->lokasi_detail }}</span>
                                    </span>

                                    @if ($lap->blok)
                                        <span class="px-2 py-0.5 rounded-full bg-[#E8F1EC] text-[#1B4332] font-bold text-[10px]">
                                            {{ $lap->blok }}
                                        </span>
                                    @endif

                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-[#798D82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $lap->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Right Column: Thumbnail + Action Button -->
                            <div class="flex sm:flex-row md:flex-col items-center md:items-end justify-between gap-3 shrink-0">
                                @if ($hasVisiblePhoto)
                                    <button type="button"
                                            onclick="openReportDetail('{{ $lap->id }}')"
                                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden border-2 border-white shadow-sm hover:scale-105 transition-transform cursor-pointer shrink-0"
                                            title="Lihat foto lampiran">
                                        <img src="{{ asset('storage/' . $firstPhoto->storage_key) }}"
                                             alt="Foto {{ $lap->kode }}"
                                             class="w-full h-full object-cover">
                                    </button>
                                @elseif ($lap->lampirans->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#EFECE5] text-[10px] font-bold text-[#52665B]">
                                        📷 {{ $lap->lampirans->count() }} Lampiran
                                    </span>
                                @endif

                                @if ($needsConfirm)
                                    <button type="button"
                                            onclick="openReportDetail('{{ $lap->id }}')"
                                            class="clay-btn-green px-4 py-2 text-xs font-bold text-white inline-flex items-center gap-1.5 cursor-pointer">
                                        <span>Konfirmasi Hasil</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="openReportDetail('{{ $lap->id }}')"
                                            class="clay-pill px-4 py-2 text-xs font-bold text-[#1B4332] inline-flex items-center gap-1.5 cursor-pointer">
                                        <span>Lihat Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($laporans->hasPages())
                <div class="mt-6 pt-4 border-t border-[#E3DDD1]">
                    {{ $laporans->links() }}
                </div>
            @endif
        @else
            <!-- Clean Claymorphic Empty State -->
            <div class="py-12 px-4 flex flex-col items-center justify-center text-center">
                <div class="clay-icon-mint w-16 h-16 flex items-center justify-center text-[#2D6A4F] mb-4">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                    </svg>
                </div>

                @if ($activeStatus !== 'semua' || $activeKategori !== 'semua' || $search !== '')
                    <h4 class="font-extrabold text-base text-[#153425]">
                        Tidak ada laporan yang cocok dengan filter.
                    </h4>
                    <p class="text-xs sm:text-sm text-[#6B8074] mt-1 max-w-sm">
                        Coba ubah tab status, pilih kategori lain, atau hapus kata kunci pencarian Anda.
                    </p>
                    <a href="{{ route('warga.laporan.index') }}"
                       class="clay-pill px-5 py-2.5 mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#1B4332]">
                        <span>Tampilkan Semua Laporan</span>
                    </a>
                @else
                    <h4 class="font-extrabold text-base text-[#153425]">
                        Belum ada laporan yang Anda kirim.
                    </h4>
                    <p class="text-xs sm:text-sm text-[#6B8074] mt-1 max-w-sm">
                        Jika ada masalah sampah, saluran air, jalan rusak, atau lampu padam di sekitar Anda, laporkan sekarang.
                    </p>
                    <a href="{{ route('warga.laporan.create') }}"
                       class="clay-btn-green px-5 py-2.5 mt-4 inline-flex items-center gap-2 text-xs font-bold text-white">
                        <span>Buat Laporan Pertama</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @endif
            </div>
        @endif
    </section>
</div>

<!-- =========================================================================
     INTERACTIVE DETAIL & TIMELINE MODAL (CLAYMORPHISM)
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

        <!-- Modal Scrollable Body -->
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-5 flex-1">
            
            <!-- Meta Grid -->
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

            <!-- Full Description -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-1.5">Uraian Permasalahan</h4>
                <p id="modal-report-uraian" class="text-xs sm:text-sm text-[#30483C] leading-relaxed whitespace-pre-line"></p>
            </div>

            <!-- Photo Gallery (if any) -->
            <div id="modal-photos-wrap" class="hidden">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-2">Foto Pendukung</h4>
                <div id="modal-photos-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
            </div>

            <!-- Interactive Citizen Confirmation Box (Visible when status == menunggu_konfirmasi) -->
            <div id="modal-confirm-box" class="hidden clay-helper-card p-4 sm:p-5 space-y-3">
                <div>
                    <span class="block text-[10px] font-black uppercase tracking-wider text-[#5B21B6]">Konfirmasi Hasil Penanganan</span>
                    <h4 class="text-sm font-black text-[#122A1E] mt-0.5">Apakah masalah di lapangan sudah tuntas?</h4>
                    <p class="text-xs text-[#4E6457] mt-0.5">Konfirmasi Anda memastikan penyelesaian benar-benar dirasakan warga.</p>
                </div>

                <form id="modal-confirm-form" method="POST" action="" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <label class="clay-dropdown-option p-3 bg-white/80 border border-[#C6D4CA] flex items-center gap-2.5 cursor-pointer">
                            <input type="radio" name="keputusan" value="tuntas" checked onchange="toggleConfirmCatatan(false)" class="accent-[#1B4332]">
                            <div>
                                <span class="block text-xs font-extrabold text-[#122A1E]">Sudah Tuntas ✓</span>
                                <span class="block text-[10px] text-[#52645B]">Masalah selesai ditangani</span>
                            </div>
                        </label>
                        <label class="clay-dropdown-option p-3 bg-white/80 border border-[#E3DDD1] flex items-center gap-2.5 cursor-pointer">
                            <input type="radio" name="keputusan" value="belum_tuntas" onchange="toggleConfirmCatatan(true)" class="accent-[#B45309]">
                            <div>
                                <span class="block text-xs font-extrabold text-[#122A1E]">Belum Tuntas</span>
                                <span class="block text-[10px] text-[#52645B]">Minta petugas cek kembali</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <textarea name="catatan"
                                  id="modal-confirm-catatan"
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Tulis catatan atau apresiasi Anda (wajib jika memilih Belum Tuntas)..."
                                  class="clay-input w-full px-3.5 py-2.5 text-xs text-[#142D21] placeholder-[#798D82] focus:ring-0"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="clay-btn-green px-5 py-2 text-xs font-bold text-white cursor-pointer">
                            Kirim Konfirmasi →
                        </button>
                    </div>
                </form>
            </div>

            <!-- Chronological Handling Timeline -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-3">Linimasa Penanganan</h4>
                <div id="modal-timeline-list" class="space-y-3 border-l-2 border-[#D4DFD7] pl-4 ml-1.5"></div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     VANILLA JS FOR FILTERS & DETAIL MODAL
     ========================================================================= -->
<script>
    (function () {
        const reportDetails = @json($reportDetailMap ?? []);

        window.toggleFilterDropdown = function (kind, event) {
            if (event) event.stopPropagation();
            const otherKind = kind === 'kategori' ? 'sort' : 'kategori';

            const otherMenu = document.getElementById('filter-' + otherKind + '-menu');
            const otherChevron = document.getElementById('filter-' + otherKind + '-chevron');
            if (otherMenu) otherMenu.classList.add('hidden');
            if (otherChevron) otherChevron.classList.remove('rotate-180');

            const menu = document.getElementById('filter-' + kind + '-menu');
            const chevron = document.getElementById('filter-' + kind + '-chevron');
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180', isHidden);
        };

        document.addEventListener('click', function (e) {
            ['kategori', 'sort'].forEach(function (kind) {
                const wrap = document.getElementById('filter-' + kind + '-wrap');
                const menu = document.getElementById('filter-' + kind + '-menu');
                const chevron = document.getElementById('filter-' + kind + '-chevron');
                if (menu && !menu.classList.contains('hidden') && (!wrap || !wrap.contains(e.target))) {
                    menu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            });
        });

        window.toggleConfirmCatatan = function (isRequired) {
            const textarea = document.getElementById('modal-confirm-catatan');
            if (textarea) {
                textarea.required = isRequired;
            }
        };

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

            // Photos
            const photosWrap = document.getElementById('modal-photos-wrap');
            const photosGrid = document.getElementById('modal-photos-grid');
            if (photosWrap && photosGrid) {
                photosGrid.innerHTML = '';
                if (Array.isArray(data.photos) && data.photos.length > 0) {
                    data.photos.forEach(function (photo) {
                        const card = document.createElement('a');
                        card.href = photo.url;
                        card.target = '_blank';
                        card.rel = 'noopener noreferrer';
                        card.className = 'block rounded-2xl overflow-hidden border-2 border-white shadow-sm hover:opacity-95 transition-opacity';

                        const img = document.createElement('img');
                        img.src = photo.url;
                        img.alt = photo.name || 'Foto lampiran';
                        img.className = 'w-full h-44 object-cover';

                        card.appendChild(img);
                        photosGrid.appendChild(card);
                    });
                    photosWrap.classList.remove('hidden');
                } else {
                    photosWrap.classList.add('hidden');
                }
            }

            // Confirmation form
            const confirmBox = document.getElementById('modal-confirm-box');
            const confirmForm = document.getElementById('modal-confirm-form');
            if (confirmBox && confirmForm) {
                if (data.needs_confirmation && data.confirm_url) {
                    confirmForm.action = data.confirm_url;
                    confirmBox.classList.remove('hidden');
                } else {
                    confirmBox.classList.add('hidden');
                }
            }

            // Timeline
            const timelineList = document.getElementById('modal-timeline-list');
            if (timelineList) {
                timelineList.innerHTML = '';
                (data.timeline || []).forEach(function (item) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';

                    const dot = document.createElement('span');
                    const dotColor = item.tone === 'amber' ? 'bg-[#D97706]' : (item.tone === 'blue' ? 'bg-[#0284C7]' : 'bg-[#2D6A4F]');
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
