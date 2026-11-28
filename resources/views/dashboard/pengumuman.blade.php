@extends('layouts.warga')

@section('title', 'Pengumuman — TUNTAS')
@section('header-title', 'Pengumuman')
@section('search-action', route('warga.pengumuman.index'))
@section('search-placeholder', 'Cari pengumuman RT atau RW...')

@section('warga-content')
@php
    $filterJenisTabs = [
        'semua' => ['label' => 'Semua', 'count' => $tabCounts['semua'] ?? 0, 'dot' => null],
        'darurat' => ['label' => 'Darurat', 'count' => $tabCounts['darurat'] ?? 0, 'dot' => 'bg-[#DC2626]'],
        'informasi' => ['label' => 'Informasi', 'count' => $tabCounts['informasi'] ?? 0, 'dot' => 'bg-[#2D6A4F]'],
        'kegiatan' => ['label' => 'Kegiatan', 'count' => $tabCounts['kegiatan'] ?? 0, 'dot' => 'bg-[#D97706]'],
        'imbauan' => ['label' => 'Imbauan', 'count' => $tabCounts['imbauan'] ?? 0, 'dot' => 'bg-[#0284C7]'],
    ];

    $scopeTabs = [
        'semua' => 'Semua (RT & RW)',
        'rt' => 'RT ' . $rtNomor,
        'rw' => 'RW ' . $rwNomor,
    ];
@endphp

<div class="space-y-5">

    <!-- ================= TOP BAR: SUBTITLE + SCOPE SWITCHER ================= -->
    <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
        <p class="text-xs sm:text-sm text-[#4E6457] leading-relaxed">
            Informasi, imbauan, kegiatan, dan peringatan resmi dari pengurus
            <span class="font-bold text-[#1B4332]">RT {{ $rtNomor }} / RW {{ $rwNomor }}</span>
            ({{ $kelurahan }}).
        </p>

        <div class="clay-sunken-note p-1 inline-flex items-center gap-1 rounded-2xl shrink-0 self-start lg:self-auto" role="group" aria-label="Filter cakupan wilayah pengumuman">
            @foreach ($scopeTabs as $scopeKey => $scopeLabel)
                @php
                    $isScopeActive = $scope === $scopeKey;
                    $scopeUrl = route('warga.pengumuman.index', array_filter([
                        'scope' => $scopeKey !== 'semua' ? $scopeKey : null,
                        'jenis' => $activeJenis !== 'semua' ? $activeJenis : null,
                        'q' => $search !== '' ? $search : null,
                    ]));
                @endphp
                <a href="{{ $scopeUrl }}"
                   class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all whitespace-nowrap {{ $isScopeActive ? 'clay-nav-active shadow-xs' : 'text-[#44594D] hover:text-[#122A1E]' }}">
                    {{ $scopeLabel }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- ================= SINGLE-ROW CLAY CATEGORY TABS RAIL ================= -->
    <section aria-label="Filter kategori pengumuman">
        <div class="clay-sunken-note p-1.5 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
            @foreach ($filterJenisTabs as $jenisKey => $tabMeta)
                @php
                    $isActiveTab = $activeJenis === $jenisKey;
                    $tabUrl = route('warga.pengumuman.index', array_filter([
                        'scope' => $scope !== 'semua' ? $scope : null,
                        'jenis' => $jenisKey !== 'semua' ? $jenisKey : null,
                        'q' => $search !== '' ? $search : null,
                    ]));
                @endphp
                <a href="{{ $tabUrl }}"
                   class="shrink-0 px-3.5 py-2 rounded-2xl inline-flex items-center gap-2 text-xs font-bold transition-all {{ $isActiveTab ? 'clay-nav-active shadow-sm' : 'text-[#44594D] hover:text-[#122A1E] hover:bg-white/50' }}">
                    @if ($tabMeta['dot'])
                        <span class="w-2 h-2 rounded-full {{ $isActiveTab ? 'bg-white/90' : $tabMeta['dot'] }}"></span>
                    @endif
                    <span>{{ $tabMeta['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold leading-none {{ $isActiveTab ? 'bg-white/20 text-white' : ($jenisKey === 'darurat' && $tabMeta['count'] > 0 ? 'bg-[#FEE2E2] text-[#991B1B]' : 'bg-[#EAE4D8] text-[#142D21]') }}">
                        {{ $tabMeta['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- ================= CALLOUT BANNER: PERINGATAN DARURAT ================= -->
    @if (($tabCounts['darurat'] ?? 0) > 0 && $activeJenis !== 'darurat')
        @php
            $daruratFilterUrl = route('warga.pengumuman.index', array_filter([
                'scope' => $scope !== 'semua' ? $scope : null,
                'jenis' => 'darurat',
                'q' => $search !== '' ? $search : null,
            ]));
        @endphp
        <section class="clay-helper-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="clay-icon-badge w-10 h-10 bg-gradient-to-br from-[#FFF5F5] to-[#FEE2E2] text-[#B91C1C] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-[#122A1E]">
                        {{ $tabCounts['darurat'] }} pengumuman darurat aktif di wilayah Anda
                    </h3>
                    <p class="text-[11px] sm:text-xs text-[#4E6457]">
                        Terdapat informasi penting yang memerlukan perhatian segera dari seluruh warga.
                    </p>
                </div>
            </div>
            <a href="{{ $daruratFilterUrl }}"
               class="clay-pill px-4 py-2 text-xs font-bold text-[#991B1B] shrink-0 self-start sm:self-auto">
                Lihat Darurat →
            </a>
        </section>
    @endif

    <!-- ================= MAIN ANNOUNCEMENT LIST PANEL ================= -->
    <section class="clay-card-content p-5 sm:p-6">
        <!-- Panel Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-[#E3DDD1] mb-5">
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">
                    KABAR LINGKUNGAN RT {{ $rtNomor }} / RW {{ $rwNomor }}
                </span>
                <h3 class="text-base sm:text-lg font-black text-[#122A1E] mt-0.5">
                    {{ $pengumumans->total() }} Pengumuman Ditemukan
                    @if ($search !== '')
                        <span class="text-xs font-bold text-[#4E6457]">• "{{ $search }}"</span>
                    @endif
                </h3>
            </div>

            @if ($activeJenis !== 'semua' || $scope !== 'semua' || $search !== '')
                <a href="{{ route('warga.pengumuman.index') }}"
                   class="clay-pill px-3.5 py-1.5 inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1B4332] self-start sm:self-auto">
                    <span>Reset Filter</span>
                    <span>✕</span>
                </a>
            @endif
        </div>

        @if ($pengumumans->isNotEmpty())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($pengumumans as $item)
                    @php
                        $badge = $item->jenis_badge;
                        $detail = $pengumumanDetailMap[$item->id] ?? null;
                        $pembuatNama = $item->pembuat?->nama ?? 'Pengurus Lingkungan';
                        $pembuatWords = preg_split('/\s+/', trim($pembuatNama)) ?: [];
                        $pembuatInitials = count($pembuatWords) >= 2
                            ? strtoupper(substr($pembuatWords[0], 0, 1) . substr($pembuatWords[1], 0, 1))
                            : strtoupper(substr($pembuatNama, 0, 2));
                        $isDarurat = $item->jenis === 'darurat';
                    @endphp

                    <article class="clay-item-card p-4 sm:p-5 flex flex-col justify-between gap-4 {{ $isDarurat ? 'ring-1 ring-[#F3BABA]' : '' }}">
                        <div class="space-y-2.5">
                            <!-- Top Meta Row -->
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                        @if ($isDarurat)
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626] animate-pulse"></span>
                                        @endif
                                        <span>{{ $badge['label'] }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/80 text-[#30483C] border border-[#E3DDD1]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2D6A4F]"></span>
                                        <span>{{ $detail['wilayah_label'] ?? 'Lingkungan' }}</span>
                                    </span>
                                </div>

                                <time class="text-[11px] font-semibold text-[#60776C]" datetime="{{ $item->terbit_at?->toIso8601String() }}">
                                    {{ $detail['waktu_relatif'] ?? '-' }}
                                </time>
                            </div>

                            <!-- Title & Excerpt -->
                            <div>
                                <h4 class="text-sm sm:text-base font-extrabold text-[#122A1E] leading-snug">
                                    {{ $item->judul }}
                                </h4>
                                <p class="text-xs sm:text-[13px] text-[#52645B] mt-1.5 line-clamp-3 leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($item->isi))), 185) }}
                                </p>
                            </div>
                        </div>

                        <!-- Bottom Meta & Action -->
                        <div class="flex items-center justify-between gap-3 pt-3 border-t border-[#E3DDD1]/80">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-[#EFECE5] border border-white/80 text-[#1B4332] font-black text-[11px] flex items-center justify-center shrink-0">
                                    {{ $pembuatInitials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#142D21] truncate">{{ $pembuatNama }}</p>
                                    <p class="text-[10px] sm:text-[11px] text-[#60776C] truncate">{{ $detail['tanggal_lengkap'] ?? '-' }}</p>
                                </div>
                            </div>

                            <button type="button"
                                    data-open-pengumuman="{{ $item->id }}"
                                    class="clay-pill px-4 py-2 text-xs font-bold {{ $isDarurat ? 'text-[#991B1B]' : 'text-[#1B4332]' }} inline-flex items-center gap-1.5 shrink-0 cursor-pointer">
                                <span>Baca Detail</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($pengumumans->hasPages())
                <div class="mt-6 pt-4 border-t border-[#E3DDD1] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-xs text-[#60776C]">
                        Menampilkan <span class="font-bold text-[#122A1E]">{{ $pengumumans->firstItem() }}–{{ $pengumumans->lastItem() }}</span>
                        dari <span class="font-bold text-[#122A1E]">{{ $pengumumans->total() }}</span> pengumuman
                    </p>
                    <div class="flex items-center gap-2">
                        @if ($pengumumans->onFirstPage())
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold text-[#8A9C92] bg-[#EAE4D8] cursor-not-allowed">Sebelumnya</span>
                        @else
                            <a href="{{ $pengumumans->previousPageUrl() }}" class="clay-pill px-4 py-1.5 text-xs font-bold text-[#1B4332]">Sebelumnya</a>
                        @endif

                        @if ($pengumumans->hasMorePages())
                            <a href="{{ $pengumumans->nextPageUrl() }}" class="clay-btn-green px-4 py-1.5 text-xs font-bold text-white">Berikutnya</a>
                        @else
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold text-[#8A9C92] bg-[#EAE4D8] cursor-not-allowed">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <!-- Clean Claymorphic Empty State -->
            <div class="py-12 px-4 flex flex-col items-center justify-center text-center">
                <div class="clay-icon-mint w-16 h-16 flex items-center justify-center text-[#2D6A4F] mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>

                @if ($search !== '' || $activeJenis !== 'semua' || $scope !== 'semua')
                    <h4 class="font-extrabold text-base text-[#153425]">
                        Tidak ada pengumuman yang cocok dengan filter.
                    </h4>
                    <p class="text-xs sm:text-sm text-[#6B8074] mt-1 max-w-sm">
                        Coba ubah tab kategori, pilih cakupan RT/RW lain, atau hapus kata kunci pencarian Anda.
                    </p>
                    <a href="{{ route('warga.pengumuman.index') }}"
                       class="clay-pill px-5 py-2.5 mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#1B4332]">
                        <span>Tampilkan Semua Pengumuman</span>
                    </a>
                @else
                    <h4 class="font-extrabold text-base text-[#153425]">
                        Belum ada pengumuman yang diterbitkan.
                    </h4>
                    <p class="text-xs sm:text-sm text-[#6B8074] mt-1 max-w-sm">
                        Pengurus RT {{ $rtNomor }} maupun RW {{ $rwNomor }} belum menerbitkan informasi terbaru untuk warga.
                    </p>
                @endif
            </div>
        @endif
    </section>
</div>

<!-- =========================================================================
     INTERACTIVE ANNOUNCEMENT READER MODAL (CLAYMORPHISM)
     ========================================================================= -->
<div id="pengumuman-detail-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-6 bg-[#122A1E]/45 backdrop-blur-xs"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-pengumuman-judul">
    <div class="clay-card-content w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">

        <!-- Modal Header -->
        <div class="p-5 sm:p-6 pb-4 border-b border-[#E3DDD1] flex items-start justify-between gap-4 shrink-0">
            <div class="min-w-0 space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="modal-pengumuman-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold"></span>
                    <span id="modal-pengumuman-wilayah" class="text-[11px] font-bold text-[#30483C] bg-white px-2.5 py-0.5 rounded-full border border-[#E3DDD1]"></span>
                </div>
                <h3 id="modal-pengumuman-judul" class="text-base sm:text-xl font-black text-[#122A1E] leading-snug"></h3>
                <p id="modal-pengumuman-waktu" class="text-[11px] text-[#60776C] font-medium"></p>
            </div>

            <button type="button"
                    data-close-pengumuman
                    class="clay-pill w-9 h-9 flex items-center justify-center text-[#52665B] hover:text-[#122A1E] shrink-0 cursor-pointer"
                    aria-label="Tutup detail pengumuman">
                ✕
            </button>
        </div>

        <!-- Modal Scrollable Body -->
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-5 flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="clay-sunken-note p-3">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">Diterbitkan Oleh</span>
                    <span id="modal-pengumuman-pembuat" class="block text-xs font-bold text-[#142D21] mt-0.5"></span>
                </div>
                <div class="clay-sunken-note p-3">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#63796E]">Waktu Terbit</span>
                    <span id="modal-pengumuman-tanggal" class="block text-xs font-bold text-[#142D21] mt-0.5"></span>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-[#63796E] mb-2">Isi Pengumuman</h4>
                <div id="modal-pengumuman-isi" class="space-y-3 text-xs sm:text-sm text-[#30483C] leading-relaxed"></div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 sm:px-6 py-4 border-t border-[#E3DDD1] flex flex-wrap items-center justify-between gap-3 shrink-0">
            <button type="button"
                    id="btn-salin-pengumuman"
                    class="clay-pill px-4 py-2 text-xs font-bold text-[#1B4332] inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span id="btn-salin-label">Salin Teks Pengumuman</span>
            </button>

            <button type="button"
                    data-close-pengumuman
                    class="clay-btn-green px-5 py-2 text-xs font-bold text-white cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const pengumumanMap = @json($pengumumanDetailMap);
    const modal = document.getElementById('pengumuman-detail-modal');
    if (!modal) {
        return;
    }

    const badgeEl = document.getElementById('modal-pengumuman-badge');
    const wilayahEl = document.getElementById('modal-pengumuman-wilayah');
    const judulEl = document.getElementById('modal-pengumuman-judul');
    const waktuEl = document.getElementById('modal-pengumuman-waktu');
    const isiEl = document.getElementById('modal-pengumuman-isi');
    const pembuatEl = document.getElementById('modal-pengumuman-pembuat');
    const tanggalEl = document.getElementById('modal-pengumuman-tanggal');
    const copyBtn = document.getElementById('btn-salin-pengumuman');
    const copyLabel = document.getElementById('btn-salin-label');

    let currentAnnouncement = null;
    let copyResetTimer = null;

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        currentAnnouncement = null;
    };

    const openModal = (id) => {
        const item = pengumumanMap[id];
        if (!item) {
            return;
        }

        currentAnnouncement = item;

        badgeEl.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ' + (item.jenis_badge?.bg || 'bg-[#EFECE5]') + ' ' + (item.jenis_badge?.text || 'text-[#30483C]');
        badgeEl.textContent = item.jenis_badge?.label || item.jenis;

        wilayahEl.textContent = item.wilayah_label || 'Lingkungan';
        judulEl.textContent = item.judul || '';
        waktuEl.textContent = 'Dipublikasikan ' + (item.waktu_relatif || '');
        pembuatEl.textContent = item.pembuat_nama || 'Pengurus Lingkungan';
        tanggalEl.textContent = item.tanggal_lengkap || '-';

        isiEl.replaceChildren();
        const paragraphs = String(item.isi || '')
            .split(/\n+/)
            .map((line) => line.trim())
            .filter(Boolean);

        if (paragraphs.length === 0) {
            const p = document.createElement('p');
            p.textContent = '-';
            isiEl.appendChild(p);
        } else {
            paragraphs.forEach((paragraph) => {
                const p = document.createElement('p');
                p.textContent = paragraph;
                isiEl.appendChild(p);
            });
        }

        if (copyLabel) {
            copyLabel.textContent = 'Salin Teks Pengumuman';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    document.addEventListener('click', (event) => {
        const openBtn = event.target.closest('[data-open-pengumuman]');
        if (openBtn) {
            openModal(openBtn.getAttribute('data-open-pengumuman'));
            return;
        }

        if (event.target.closest('[data-close-pengumuman]') || event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            if (!currentAnnouncement) {
                return;
            }

            const badgeLabel = currentAnnouncement.jenis_badge?.label || 'Pengumuman';
            const shareText = `[${badgeLabel}] ${currentAnnouncement.judul}\n\n${currentAnnouncement.isi}\n\n— ${currentAnnouncement.pembuat_nama} (${currentAnnouncement.wilayah_label}), ${currentAnnouncement.tanggal_lengkap}`;

            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(shareText);
                } else {
                    const tempArea = document.createElement('textarea');
                    tempArea.value = shareText;
                    tempArea.setAttribute('readonly', '');
                    tempArea.style.position = 'fixed';
                    tempArea.style.opacity = '0';
                    document.body.appendChild(tempArea);
                    tempArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempArea);
                }

                if (copyLabel) {
                    copyLabel.textContent = 'Teks Tersalin!';
                    clearTimeout(copyResetTimer);
                    copyResetTimer = setTimeout(() => {
                        copyLabel.textContent = 'Salin Teks Pengumuman';
                    }, 2200);
                }
            } catch (_) {
                if (copyLabel) {
                    copyLabel.textContent = 'Gagal menyalin';
                }
            }
        });
    }
});
</script>
@endpush
