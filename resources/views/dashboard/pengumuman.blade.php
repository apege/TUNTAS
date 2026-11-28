@extends('layouts.warga')

@section('title', 'Pengumuman — TUNTAS')
@section('header-title', 'Pengumuman')
@section('search-action', route('warga.pengumuman.index'))
@section('search-placeholder', 'Cari pengumuman RT atau RW...')

@section('warga-content')
@php
    $filterJenisTabs = [
        'semua' => ['label' => 'Semua', 'count' => $tabCounts['semua'] ?? 0, 'dot' => 'bg-[#215E3C]'],
        'darurat' => ['label' => 'Darurat', 'count' => $tabCounts['darurat'] ?? 0, 'dot' => 'bg-red-500'],
        'informasi' => ['label' => 'Informasi', 'count' => $tabCounts['informasi'] ?? 0, 'dot' => 'bg-emerald-500'],
        'kegiatan' => ['label' => 'Kegiatan', 'count' => $tabCounts['kegiatan'] ?? 0, 'dot' => 'bg-amber-500'],
        'imbauan' => ['label' => 'Imbauan', 'count' => $tabCounts['imbauan'] ?? 0, 'dot' => 'bg-sky-500'],
    ];

    $scopeTabs = [
        'semua' => 'Semua (RT & RW)',
        'rt' => 'RT ' . $rtNomor,
        'rw' => 'RW ' . $rwNomor,
    ];
@endphp

<div class="space-y-5">
    {{-- Subheader & Scope Switcher --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3.5">
        <div>
            <p class="text-sm text-[#4F6358] leading-relaxed">
                Informasi, imbauan, kegiatan, dan peringatan darurat resmi dari pengurus
                <span class="font-semibold text-[#1B4B30]">RT {{ $rtNomor }} / RW {{ $rwNomor }}</span>
                ({{ $kelurahan }}).
            </p>
        </div>

        <div class="clay-sunken-note p-1.5 rounded-2xl inline-flex items-center gap-1 self-start lg:self-auto" role="group" aria-label="Filter cakupan wilayah pengumuman">
            @foreach($scopeTabs as $scopeKey => $scopeLabel)
                @php
                    $isScopeActive = $scope === $scopeKey;
                    $scopeUrl = route('warga.pengumuman.index', array_filter([
                        'scope' => $scopeKey !== 'semua' ? $scopeKey : null,
                        'jenis' => $activeJenis !== 'semua' ? $activeJenis : null,
                        'q' => $search !== '' ? $search : null,
                    ]));
                @endphp
                <a
                    href="{{ $scopeUrl }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $isScopeActive ? 'bg-[#1D5436] text-white shadow-sm' : 'text-[#4F6358] hover:text-[#14251B] hover:bg-white/60' }}"
                >
                    {{ $scopeLabel }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Urgent Notification Banner --}}
    @if(($tabCounts['darurat'] ?? 0) > 0 && $activeJenis !== 'darurat')
        @php
            $daruratFilterUrl = route('warga.pengumuman.index', array_filter([
                'scope' => $scope !== 'semua' ? $scope : null,
                'jenis' => 'darurat',
                'q' => $search !== '' ? $search : null,
            ]));
        @endphp
        <div class="rounded-2xl border border-red-200/80 bg-gradient-to-r from-red-50 via-[#FFF5F5] to-white px-4 py-3.5 shadow-[6px_8px_20px_rgba(185,28,28,0.06),inset_1px_1px_0_rgba(255,255,255,0.9)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-red-700">
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                            Peringatan Darurat Aktif
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-[#4A1C1C] mt-0.5">
                        Terdapat <span class="font-bold">{{ $tabCounts['darurat'] }} pengumuman darurat</span> di wilayah Anda yang memerlukan perhatian segera.
                    </p>
                </div>
            </div>
            <a href="{{ $daruratFilterUrl }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition-colors shrink-0">
                <span>Lihat Darurat</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    @endif

    {{-- Category Filter Tabs & Active Search Info --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="clay-sunken-note p-1.5 rounded-2xl flex items-center gap-1.5 overflow-x-auto no-scrollbar">
            @foreach($filterJenisTabs as $jenisKey => $tab)
                @php
                    $isActive = $activeJenis === $jenisKey;
                    $tabUrl = route('warga.pengumuman.index', array_filter([
                        'scope' => $scope !== 'semua' ? $scope : null,
                        'jenis' => $jenisKey !== 'semua' ? $jenisKey : null,
                        'q' => $search !== '' ? $search : null,
                    ]));
                @endphp
                <a
                    href="{{ $tabUrl }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $isActive ? 'bg-white text-[#14251B] shadow-[3px_4px_10px_rgba(20,37,27,0.08)] ring-1 ring-[#1D5436]/15' : 'text-[#52665B] hover:text-[#14251B] hover:bg-white/50' }}"
                >
                    @if($jenisKey !== 'semua')
                        <span class="w-2 h-2 rounded-full {{ $tab['dot'] }}"></span>
                    @endif
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $isActive ? ($jenisKey === 'darurat' ? 'bg-red-100 text-red-700' : 'bg-[#E5F2EA] text-[#1D5436]') : 'bg-[#E9EFEA] text-[#5B6E64]' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        @if($search !== '')
            @php
                $clearSearchUrl = route('warga.pengumuman.index', array_filter([
                    'scope' => $scope !== 'semua' ? $scope : null,
                    'jenis' => $activeJenis !== 'semua' ? $activeJenis : null,
                ]));
            @endphp
            <div class="inline-flex items-center gap-2 self-start md:self-auto px-3 py-1.5 rounded-xl bg-white/80 border border-[#D4E2D9] text-xs text-[#3C4F44]">
                <span>Pencarian: <strong class="text-[#14251B]">"{{ $search }}"</strong></span>
                <a href="{{ $clearSearchUrl }}" class="text-[#1D5436] hover:text-[#14251B] font-bold underline underline-offset-2">
                    Hapus
                </a>
            </div>
        @endif
    </div>

    {{-- Announcement Grid --}}
    @if($pengumumans->isEmpty())
        <div class="clay-card-content p-10 sm:p-14 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl clay-icon-mint flex items-center justify-center text-[#1D5436] mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                </svg>
            </div>
            <h2 class="text-base sm:text-lg font-extrabold text-[#14251B]">Belum ada pengumuman yang sesuai</h2>
            <p class="text-xs sm:text-sm text-[#5B6E64] mt-1.5 max-w-md mx-auto">
                @if($search !== '' || $activeJenis !== 'semua' || $scope !== 'semua')
                    Tidak ditemukan pengumuman untuk kombinasi filter atau kata kunci saat ini.
                @else
                    Pengurus RT {{ $rtNomor }} maupun RW {{ $rwNomor }} belum menerbitkan pengumuman baru.
                @endif
            </p>
            @if($search !== '' || $activeJenis !== 'semua' || $scope !== 'semua')
                <div class="mt-5">
                    <a href="{{ route('warga.pengumuman.index') }}" class="clay-btn-green inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold">
                        <span>Tampilkan Semua Pengumuman</span>
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach($pengumumans as $item)
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

                <article class="clay-item-card p-5 sm:p-6 flex flex-col justify-between gap-4 {{ $isDarurat ? 'ring-2 ring-red-200/90 bg-gradient-to-br from-[#FFF9F9] via-white to-[#F7FAF7]' : '' }}">
                    <div class="space-y-3">
                        {{-- Meta Tags --}}
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                    @if($isDarurat)
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span>
                                    @endif
                                    {{ $badge['label'] }}
                                </span>

                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#EDF4EF] text-[#1D5436]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    {{ $detail['wilayah_label'] ?? 'Lingkungan' }}
                                </span>
                            </div>

                            <time class="text-[11px] font-semibold text-[#64786D]" datetime="{{ $item->terbit_at?->toIso8601String() }}">
                                {{ $detail['waktu_relatif'] ?? '-' }}
                            </time>
                        </div>

                        {{-- Title & Excerpt --}}
                        <div>
                            <h2 class="text-base sm:text-[17px] font-extrabold text-[#14251B] leading-snug">
                                {{ $item->judul }}
                            </h2>
                            <p class="mt-2 text-xs sm:text-sm text-[#4F6358] leading-relaxed line-clamp-3">
                                {{ \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($item->isi))), 185) }}
                            </p>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="pt-3.5 border-t border-[#E6EFE9] flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-[#E5F2EA] text-[#1D5436] font-extrabold text-[11px] flex items-center justify-center shrink-0">
                                {{ $pembuatInitials }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-[#14251B] truncate">{{ $pembuatNama }}</p>
                                <p class="text-[11px] text-[#64786D] truncate">{{ $detail['tanggal_lengkap'] ?? '-' }}</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            data-open-pengumuman="{{ $item->id }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $isDarurat ? 'bg-red-600 hover:bg-red-700 text-white shadow-sm' : 'bg-[#EDF5F0] hover:bg-[#1D5436] text-[#1D5436] hover:text-white' }}"
                        >
                            <span>Baca</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </article>
            @endforeach
        </div>

        @if($pengumumans->hasPages())
            <div class="clay-card-content px-5 py-3.5 flex items-center justify-between">
                <p class="text-xs text-[#5B6E64]">
                    Menampilkan <span class="font-bold text-[#14251B]">{{ $pengumumans->firstItem() }}–{{ $pengumumans->lastItem() }}</span>
                    dari <span class="font-bold text-[#14251B]">{{ $pengumumans->total() }}</span> pengumuman
                </p>
                <div class="flex items-center gap-2">
                    @if($pengumumans->onFirstPage())
                        <span class="px-3 py-1.5 rounded-xl text-xs font-semibold text-[#9CB0A5] bg-[#F1F5F2] cursor-not-allowed">Sebelumnya</span>
                    @else
                        <a href="{{ $pengumumans->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-[#1D5436] bg-[#EDF5F0] hover:bg-[#DFEDE4] transition-colors">Sebelumnya</a>
                    @endif

                    @if($pengumumans->hasMorePages())
                        <a href="{{ $pengumumans->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#1D5436] hover:bg-[#16422A] transition-colors">Berikutnya</a>
                    @else
                        <span class="px-3 py-1.5 rounded-xl text-xs font-semibold text-[#9CB0A5] bg-[#F1F5F2] cursor-not-allowed">Berikutnya</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- Interactive Claymorphism Reader Modal --}}
<div
    id="pengumuman-detail-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#0F1F16]/45 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-pengumuman-judul"
>
    <div class="clay-card-content w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden bg-[#F9FBF9] shadow-[0_24px_60px_rgba(15,31,22,0.28)]">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-[#E5EEE8] flex items-center justify-between gap-3 bg-white/70">
            <div class="flex flex-wrap items-center gap-2">
                <span id="modal-pengumuman-badge" class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold"></span>
                <span id="modal-pengumuman-wilayah" class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#EDF4EF] text-[#1D5436]"></span>
            </div>

            <button
                type="button"
                data-close-pengumuman
                class="w-8 h-8 rounded-xl bg-[#EEF3EF] hover:bg-[#DFEAE3] text-[#45594E] flex items-center justify-center transition-colors"
                aria-label="Tutup detail pengumuman"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 overflow-y-auto space-y-5">
            <div>
                <h3 id="modal-pengumuman-judul" class="text-xl font-extrabold text-[#14251B] leading-snug"></h3>
                <p id="modal-pengumuman-waktu" class="text-xs font-semibold text-[#64786D] mt-1.5"></p>
            </div>

            <div id="modal-pengumuman-isi" class="space-y-3 text-sm text-[#2F4237] leading-relaxed"></div>

            <div class="clay-sunken-note p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#64786D]">Diterbitkan Oleh</p>
                    <p id="modal-pengumuman-pembuat" class="text-sm font-bold text-[#14251B] mt-0.5"></p>
                </div>
                <div class="sm:text-right">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#64786D]">Waktu Terbit</p>
                    <p id="modal-pengumuman-tanggal" class="text-xs font-semibold text-[#3C4F44] mt-0.5"></p>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t border-[#E5EEE8] bg-white/70 flex flex-wrap items-center justify-between gap-3">
            <button
                type="button"
                id="btn-salin-pengumuman"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#EDF5F0] hover:bg-[#DFEDE4] text-[#1D5436] text-xs font-bold transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span id="btn-salin-label">Salin Teks Pengumuman</span>
            </button>

            <button
                type="button"
                data-close-pengumuman
                class="clay-btn-green px-5 py-2 text-xs font-bold"
            >
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

        badgeEl.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold ' + (item.jenis_badge?.bg || 'bg-slate-100') + ' ' + (item.jenis_badge?.text || 'text-slate-700');
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
