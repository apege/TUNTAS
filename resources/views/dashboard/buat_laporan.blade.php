@extends('layouts.warga')

@section('header-title', 'Buat Laporan')

@section('warga-content')
@php
    $selectedKategoriId = old('kategori_id', $kategoris->first()->id ?? '');
    $initialKategori = $kategoris->firstWhere('id', $selectedKategoriId) ?? $kategoris->first();

    $dampakOptions = [
        'rendah' => ['label' => 'Ringan', 'short' => 'Dampak minimal', 'desc' => 'Tidak mengganggu aktivitas utama', 'dot' => '#16A34A'],
        'sedang' => ['label' => 'Sedang', 'short' => 'Akses terganggu', 'desc' => 'Akses / kenyamanan terganggu', 'dot' => '#D97706'],
        'tinggi' => ['label' => 'Berat', 'short' => 'Bahaya / darurat', 'desc' => 'Membahayakan keselamatan / aktivitas terhenti', 'dot' => '#DC2626'],
    ];

    $urgensiOptions = [
        'rendah' => ['label' => 'Rendah', 'short' => 'Fleksibel', 'desc' => 'Penanganan berkala / fleksibel', 'dot' => '#64748B'],
        'sedang' => ['label' => 'Sedang', 'short' => 'Segera ditangani', 'desc' => 'Perlu segera ditangani pengurus', 'dot' => '#0284C7'],
        'tinggi' => ['label' => 'Tinggi', 'short' => 'Butuh cepat', 'desc' => 'Butuh penanganan cepat', 'dot' => '#EA580C'],
        'mendesak' => ['label' => 'Darurat', 'short' => 'Tindakan langsung', 'desc' => 'Butuh tindakan langsung di lapangan', 'dot' => '#DC2626'],
    ];

    $selectedDampak = old('dampak', 'sedang');
    $selectedUrgensi = old('urgensi', 'sedang');
    $activeDampak = $dampakOptions[$selectedDampak] ?? $dampakOptions['sedang'];
    $activeUrgensi = $urgensiOptions[$selectedUrgensi] ?? $urgensiOptions['sedang'];
@endphp

<div>
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
                    <p class="text-xs sm:text-sm text-[#55695D] mt-1 leading-relaxed">
                        Ceritakan masalah di sekitar Anda. Pengurus RT akan membantu menindaklanjutinya.
                    </p>
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
                        <div class="flex items-center justify-between gap-2 mb-2.5">
                            <label class="block text-xs sm:text-sm font-extrabold text-[#142D21]">
                                Kategori masalah <span class="text-red-500">*</span>
                            </label>
                            <span id="kategori-selected-pill" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#E8F1EC] text-[#1B4332] text-[10px] sm:text-[11px] font-bold">
                                <span id="kategori-pill-dot" class="w-2 h-2 rounded-full" style="background-color: {{ $initialKategori->warna_peta ?? '#1B4332' }}"></span>
                                <span id="kategori-pill-name">{{ $initialKategori->nama ?? 'Pilih kategori' }}</span>
                            </span>
                        </div>

                        <input type="hidden" 
                               name="kategori_id" 
                               id="kategori_id" 
                               value="{{ $selectedKategoriId }}" 
                               required>

                        <!-- 5 Selectable Category Cards Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 sm:gap-3" id="kategori-grid">
                            @foreach ($kategoris as $kat)
                                @php
                                    $isSelected = (string) $selectedKategoriId === (string) $kat->id;
                                    $iconIdleClasses = match ($kat->icon_type) {
                                        'sampah' => 'bg-[#E2F3E8] text-[#1B4332]',
                                        'drainase' => 'bg-[#E0EEFC] text-[#1D4ED8]',
                                        'jalan' => 'bg-[#FCEFD9] text-[#B45309]',
                                        'penerangan' => 'bg-[#FEF3C7] text-[#B45309]',
                                        default => 'bg-[#ECE7FC] text-[#6D28D9]',
                                    };
                                @endphp
                                <button type="button"
                                        data-kategori-card
                                        data-kategori-id="{{ $kat->id }}"
                                        data-short-name="{{ $kat->short_name }}"
                                        data-full-name="{{ $kat->nama }}"
                                        data-warna="{{ $kat->warna_peta ?? '#2D6A4F' }}"
                                        data-tips="{{ $kat->tips }}"
                                        data-placeholder-judul="{{ $kat->placeholder_judul }}"
                                        data-placeholder-uraian="{{ $kat->placeholder_uraian }}"
                                        data-contoh-judul='@json($kat->contoh_judul)'
                                        data-suggested-dampak="{{ $kat->suggested_dampak }}"
                                        data-suggested-urgensi="{{ $kat->suggested_urgensi }}"
                                        data-idle-classes="{{ $iconIdleClasses }}"
                                        onclick="selectKategori('{{ $kat->id }}')"
                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                        class="clay-category-card relative p-3 sm:p-3.5 flex flex-col items-center justify-center text-center group focus:outline-none {{ $isSelected ? 'is-selected ring-2 ring-[#2D6A4F]' : '' }}"
                                        aria-label="Pilih Kategori {{ $kat->short_name }}">
                                    
                                    <!-- Active Check Badge -->
                                    <span data-check-badge class="absolute top-2 right-2 w-4 h-4 rounded-full bg-[#1B4332] text-white flex items-center justify-center shadow-xs transition-all {{ $isSelected ? 'opacity-100 scale-100' : 'opacity-0 scale-75 pointer-events-none' }}">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>

                                    <!-- Category Icon -->
                                    <div data-icon-wrap class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center mb-2 transition-all group-hover:scale-105 {{ $isSelected ? 'bg-[#2D6A4F] text-white shadow-sm' : $iconIdleClasses }}">
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
                                            <!-- Streetlight Icon -->
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                        @else
                                            <!-- Public Facilities Icon -->
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

                        <!-- Ringkas 1-baris Tips Kategori (Tanpa kotak berat) -->
                        @if ($initialKategori)
                            <p class="mt-2.5 flex items-start gap-2 text-[11px] sm:text-xs text-[#4E6457] leading-relaxed px-0.5">
                                <svg class="w-3.5 h-3.5 text-[#2D6A4F] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>
                                    <strong class="font-bold text-[#1B4332]">Tips <span id="assistant-category-title">{{ $initialKategori->short_name }}</span>:</strong>
                                    <span id="assistant-tips">{{ $initialKategori->tips }}</span>
                                </span>
                            </p>
                        @endif
                    </div>

                    <!-- ================= FIELD 2: JUDUL LAPORAN ================= -->
                    <div class="relative" id="judul-preset-wrap">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <label for="judul" class="block text-xs sm:text-sm font-extrabold text-[#142D21]">
                                Judul laporan <span class="text-red-500">*</span>
                            </label>
                            @if ($initialKategori)
                                <button type="button"
                                        id="btn-toggle-contoh-judul"
                                        onclick="toggleContohJudul(event)"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2D6A4F] hover:text-[#142D21] transition-colors cursor-pointer">
                                    <span>Butuh contoh judul?</span>
                                    <svg id="contoh-judul-chevron" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <input type="text" 
                               name="judul" 
                               id="judul" 
                               value="{{ old('judul') }}"
                               placeholder="{{ $initialKategori->placeholder_judul ?? 'Contoh: Saluran air tersumbat di Gang 2' }}"
                               required
                               class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0">

                        <!-- Optional Popover: Saran Judul Cepat (Tertutup rapi sampai diklik) -->
                        @if ($initialKategori)
                            <div id="contoh-judul-popover"
                                 class="clay-dropdown-panel hidden absolute left-0 right-0 mt-2 p-2.5 z-30 space-y-1">
                                <span class="block px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-[#6A7F73]">
                                    Pilih salah satu contoh judul
                                </span>
                                <div id="contoh-judul-chips" class="space-y-1">
                                    @foreach ($initialKategori->contoh_judul ?? [] as $contoh)
                                        <button type="button"
                                                onclick="applyJudulChip(this)"
                                                data-judul="{{ $contoh }}"
                                                class="clay-dropdown-option w-full px-3 py-2 text-left text-xs font-semibold text-[#142D21] flex items-center justify-between gap-2">
                                            <span>{{ $contoh }}</span>
                                            <span class="text-[10px] font-bold text-[#2D6A4F] shrink-0">Pakai</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- ================= FIELD 3: LOKASI & AREA LINGKUNGAN (2 COLS WITH CUSTOM AUTOCOMPLETE) ================= -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Lokasi Masalah with Custom Autocomplete -->
                        <div class="relative" id="lokasi-autocomplete-wrap">
                            <label for="lokasi_detail" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                Lokasi masalah <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="lokasi_detail" 
                                       id="lokasi_detail" 
                                       autocomplete="off"
                                       value="{{ old('lokasi_detail') }}"
                                       placeholder="Nama jalan, gang, atau patokan"
                                       required
                                       class="clay-input w-full pl-4 pr-9 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0">
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#798D82]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </span>
                            </div>

                            <!-- Floating Custom Autocomplete Panel (Lokasi) -->
                            <div id="lokasi-autocomplete-menu"
                                 class="clay-dropdown-panel hidden absolute left-0 right-0 mt-2 p-2 z-30 max-h-56 overflow-y-auto custom-scrollbar space-y-1">
                            </div>
                        </div>

                        <!-- Area Lingkungan with Custom Clay Autocomplete Combo -->
                        <div class="relative" id="blok-autocomplete-wrap">
                            <label for="blok" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                Area lingkungan <span class="text-[11px] font-normal text-[#6B8074]">(opsional)</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="blok" 
                                       id="blok" 
                                       autocomplete="off"
                                       maxlength="50"
                                       value="{{ old('blok') }}"
                                       placeholder="Pilih atau ketik area / gang"
                                       class="clay-input w-full pl-4 pr-14 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0">
                                <div class="absolute inset-y-0 right-2.5 flex items-center gap-1">
                                    <button type="button"
                                            id="blok-clear-btn"
                                            onclick="clearBlokInput()"
                                            class="hidden w-6 h-6 rounded-full hover:bg-black/5 text-[#6A7F73] hover:text-[#142D21] flex items-center justify-center text-xs font-bold transition-colors cursor-pointer"
                                            title="Kosongkan area"
                                            aria-label="Kosongkan area">
                                        ✕
                                    </button>
                                    <button type="button"
                                            id="blok-toggle-btn"
                                            onclick="toggleBlokAutocomplete()"
                                            class="w-6 h-6 rounded-full hover:bg-black/5 text-[#52665B] flex items-center justify-center transition-transform cursor-pointer"
                                            title="Lihat daftar area RT"
                                            aria-label="Lihat daftar area RT">
                                        <svg id="blok-chevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Floating Custom Autocomplete Panel (Area Lingkungan) -->
                            <div id="blok-autocomplete-menu"
                                 class="clay-dropdown-panel hidden absolute left-0 right-0 mt-2 p-2 z-30 max-h-60 overflow-y-auto custom-scrollbar space-y-1">
                            </div>
                        </div>
                    </div>

                    <!-- ================= FIELD 4: DAMPAK & URGENSI (CUSTOM CLAY DROPDOWNS) ================= -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Custom Dropdown 1: Dampak bagi warga -->
                        <div class="relative" id="custom-select-dampak">
                            <label id="dampak-label" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                Dampak bagi warga <span class="text-red-500">*</span>
                            </label>

                            <input type="hidden" name="dampak" id="dampak" value="{{ $selectedDampak }}" required>

                            <button type="button"
                                    id="dampak-trigger"
                                    onclick="toggleCustomSelect('dampak', event)"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    aria-labelledby="dampak-label"
                                    class="clay-select-trigger w-full px-4 py-3 flex items-center justify-between gap-2.5 text-left">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span id="dampak-trigger-dot" class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $activeDampak['dot'] }}"></span>
                                    <div class="min-w-0 flex-1 truncate text-xs sm:text-sm">
                                        <span id="dampak-trigger-label" class="font-extrabold text-[#122A1E]">{{ $activeDampak['label'] }}</span>
                                        <span class="text-[#798D82] mx-1">—</span>
                                        <span id="dampak-trigger-desc" class="text-[#4E6457] font-medium">{{ $activeDampak['short'] }}</span>
                                    </div>
                                </div>
                                <svg id="dampak-chevron" class="w-4 h-4 text-[#52665B] shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Floating Clay Options Panel -->
                            <div id="dampak-menu"
                                 role="listbox"
                                 class="clay-dropdown-panel hidden absolute left-0 right-0 mt-2 p-2 z-30 space-y-1">
                                @foreach ($dampakOptions as $optValue => $optMeta)
                                    <button type="button"
                                            role="option"
                                            data-select-option="dampak"
                                            data-value="{{ $optValue }}"
                                            data-label="{{ $optMeta['label'] }}"
                                            data-short="{{ $optMeta['short'] }}"
                                            data-dot="{{ $optMeta['dot'] }}"
                                            onclick="selectCustomOption('dampak', '{{ $optValue }}')"
                                            class="clay-dropdown-option w-full p-2.5 sm:p-3 flex items-start justify-between gap-2.5 text-left {{ $selectedDampak === $optValue ? 'is-selected' : '' }}">
                                        <div class="flex items-start gap-2.5 min-w-0">
                                            <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0" style="background-color: {{ $optMeta['dot'] }}"></span>
                                            <div class="min-w-0">
                                                <span class="block text-xs sm:text-sm font-extrabold text-[#122A1E] leading-tight">{{ $optMeta['label'] }}</span>
                                                <span class="block text-[11px] text-[#52645B] mt-0.5 leading-snug">{{ $optMeta['desc'] }}</span>
                                            </div>
                                        </div>
                                        <span data-opt-check class="w-5 h-5 rounded-full bg-[#1B4332] text-white text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5 {{ $selectedDampak === $optValue ? '' : 'hidden' }}">
                                            ✓
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom Dropdown 2: Urgensi penanganan -->
                        <div class="relative" id="custom-select-urgensi">
                            <label id="urgensi-label" class="block text-xs sm:text-sm font-extrabold text-[#142D21] mb-2">
                                Urgensi penanganan <span class="text-red-500">*</span>
                            </label>

                            <input type="hidden" name="urgensi" id="urgensi" value="{{ $selectedUrgensi }}" required>

                            <button type="button"
                                    id="urgensi-trigger"
                                    onclick="toggleCustomSelect('urgensi', event)"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    aria-labelledby="urgensi-label"
                                    class="clay-select-trigger w-full px-4 py-3 flex items-center justify-between gap-2.5 text-left">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span id="urgensi-trigger-dot" class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $activeUrgensi['dot'] }}"></span>
                                    <div class="min-w-0 flex-1 truncate text-xs sm:text-sm">
                                        <span id="urgensi-trigger-label" class="font-extrabold text-[#122A1E]">{{ $activeUrgensi['label'] }}</span>
                                        <span class="text-[#798D82] mx-1">—</span>
                                        <span id="urgensi-trigger-desc" class="text-[#4E6457] font-medium">{{ $activeUrgensi['short'] }}</span>
                                    </div>
                                </div>
                                <svg id="urgensi-chevron" class="w-4 h-4 text-[#52665B] shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Floating Clay Options Panel -->
                            <div id="urgensi-menu"
                                 role="listbox"
                                 class="clay-dropdown-panel hidden absolute left-0 right-0 mt-2 p-2 z-30 space-y-1">
                                @foreach ($urgensiOptions as $optValue => $optMeta)
                                    <button type="button"
                                            role="option"
                                            data-select-option="urgensi"
                                            data-value="{{ $optValue }}"
                                            data-label="{{ $optMeta['label'] }}"
                                            data-short="{{ $optMeta['short'] }}"
                                            data-dot="{{ $optMeta['dot'] }}"
                                            onclick="selectCustomOption('urgensi', '{{ $optValue }}')"
                                            class="clay-dropdown-option w-full p-2.5 sm:p-3 flex items-start justify-between gap-2.5 text-left {{ $selectedUrgensi === $optValue ? 'is-selected' : '' }}">
                                        <div class="flex items-start gap-2.5 min-w-0">
                                            <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0" style="background-color: {{ $optMeta['dot'] }}"></span>
                                            <div class="min-w-0">
                                                <span class="block text-xs sm:text-sm font-extrabold text-[#122A1E] leading-tight">{{ $optMeta['label'] }}</span>
                                                <span class="block text-[11px] text-[#52645B] mt-0.5 leading-snug">{{ $optMeta['desc'] }}</span>
                                            </div>
                                        </div>
                                        <span data-opt-check class="w-5 h-5 rounded-full bg-[#1B4332] text-white text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5 {{ $selectedUrgensi === $optValue ? '' : 'hidden' }}">
                                            ✓
                                        </span>
                                    </button>
                                @endforeach
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
                                  maxlength="1500"
                                  placeholder="{{ $initialKategori->placeholder_uraian ?? 'Jelaskan kondisi, sejak kapan terjadi, dan dampaknya bagi warga...' }}"
                                  required
                                  class="clay-input w-full px-4 py-3 text-xs sm:text-sm text-[#142D21] placeholder-[#798D82] focus:ring-0 leading-relaxed">{{ old('uraian') }}</textarea>
                        
                        <div class="flex items-center justify-between text-[11px] text-[#697D72] mt-1.5 px-1 font-medium">
                            <span>
                                <span id="uraian-char-count">{{ strlen(old('uraian', '')) }}</span>/1500 karakter • minimal 20 karakter
                            </span>
                            <span id="uraian-status-text" class="{{ strlen(old('uraian', '')) >= 20 ? 'text-[#1B4332] font-bold' : 'text-[#A07040]' }}">
                                @if (strlen(old('uraian', '')) >= 20)
                                    ✓ Panjang memadai
                                @else
                                    Butuh {{ 20 - strlen(old('uraian', '')) }} karakter lagi
                                @endif
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
                               class="hidden">

                        <!-- Native mobile camera capture fallback -->
                        <input type="file"
                               id="foto-camera-fallback"
                               accept="image/*"
                               capture="environment"
                               class="hidden">

                        <!-- Upload & Camera Dropzone Container -->
                        <div id="foto-dropzone"
                             class="clay-upload-dropzone p-5 sm:p-7 flex flex-col items-center justify-center text-center transition-all">
                            
                            <!-- State 1: Empty State (Upload or Live Camera) -->
                            <div id="foto-empty-state" class="flex flex-col items-center w-full">
                                <div class="clay-pill w-12 h-12 flex items-center justify-center text-[#2D6A4F] mb-3 cursor-pointer"
                                     onclick="document.getElementById('foto-input').click()">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h4 class="font-extrabold text-xs sm:text-sm text-[#142D21]">
                                    Pilih, seret, atau ambil foto kondisi lingkungan
                                </h4>
                                <p class="text-[11px] text-[#63796E] mt-1 font-medium">
                                    JPG, PNG, atau WebP • maksimal 5 MB
                                </p>

                                <div class="mt-4 flex flex-wrap items-center justify-center gap-2.5">
                                    <button type="button"
                                            onclick="document.getElementById('foto-input').click()"
                                            class="clay-pill px-4 py-2 inline-flex items-center gap-2 text-xs font-bold text-[#1B4332] cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span>Pilih dari Galeri</span>
                                    </button>

                                    <button type="button"
                                            id="btn-open-camera"
                                            onclick="openCameraCapture(event)"
                                            class="clay-btn-green px-4 py-2 inline-flex items-center gap-2 text-xs font-bold text-white cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <circle cx="12" cy="13" r="3" />
                                        </svg>
                                        <span>Ambil Foto Kamera</span>
                                    </button>
                                </div>
                            </div>

                            <!-- State 2: Live Camera Viewfinder -->
                            <div id="foto-camera-state" class="hidden w-full flex flex-col items-center space-y-3">
                                <div class="relative w-full max-w-md rounded-2xl overflow-hidden bg-[#112B1F] border-2 border-white shadow-md">
                                    <video id="camera-video" autoplay playsinline muted class="w-full aspect-video object-cover"></video>
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full bg-black/50 backdrop-blur-xs text-[10px] font-bold text-white inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        Kamera Aktif
                                    </span>
                                </div>

                                <!-- Camera Permission / Error Fallback Notice -->
                                <div id="camera-error-box" class="hidden w-full max-w-md p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs text-center space-y-2">
                                    <p id="camera-error-text" class="font-medium"></p>
                                    <button type="button"
                                            onclick="document.getElementById('foto-camera-fallback').click()"
                                            class="clay-pill px-3.5 py-1.5 text-[11px] font-bold text-[#1B4332] cursor-pointer">
                                        Buka Kamera / Galeri Perangkat
                                    </button>
                                </div>

                                <div class="flex flex-wrap items-center justify-center gap-2.5">
                                    <button type="button"
                                            onclick="closeCameraCapture(event)"
                                            class="clay-btn-cancel px-4 py-2 text-xs font-bold cursor-pointer">
                                        Batal
                                    </button>

                                    <button type="button"
                                            id="btn-switch-camera"
                                            onclick="switchCameraFacing(event)"
                                            class="clay-pill px-3.5 py-2 inline-flex items-center gap-1.5 text-xs font-bold text-[#1B4332] cursor-pointer"
                                            title="Ganti kamera depan / belakang">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>Balik Kamera</span>
                                    </button>

                                    <button type="button"
                                            id="btn-capture-snapshot"
                                            onclick="captureCameraSnapshot(event)"
                                            class="clay-btn-green px-5 py-2 inline-flex items-center gap-2 text-xs font-bold text-white cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <circle cx="12" cy="12" r="9" />
                                            <circle cx="12" cy="12" r="4" fill="currentColor" />
                                        </svg>
                                        <span>Jepret Foto</span>
                                    </button>
                                </div>
                            </div>

                            <!-- State 3: Photo Selected & Preview Visible -->
                            <div id="foto-preview-state" class="hidden w-full flex flex-col items-center space-y-3">
                                <div class="relative max-w-xs rounded-2xl overflow-hidden border-2 border-white shadow-md">
                                    <img id="foto-preview-img" src="" alt="Pratinjau Foto" class="w-full h-44 object-cover">
                                    <button type="button" 
                                            onclick="clearPhoto(event)" 
                                            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center text-xs font-bold shadow-md hover:bg-red-700 transition-colors cursor-pointer"
                                            title="Hapus foto">
                                        ✕
                                    </button>
                                </div>
                                <div class="text-center">
                                    <span id="foto-preview-name" class="block text-xs font-bold text-[#142D21]"></span>
                                    <span id="foto-preview-size" class="block text-[10px] text-[#63796E]"></span>
                                    <div class="mt-2.5 flex flex-wrap items-center justify-center gap-2">
                                        <button type="button"
                                                onclick="document.getElementById('foto-input').click()"
                                                class="clay-pill px-3.5 py-1.5 text-[11px] font-bold text-[#1B4332] cursor-pointer">
                                            Ganti File
                                        </button>
                                        <button type="button"
                                                onclick="openCameraCapture(event)"
                                                class="clay-pill px-3.5 py-1.5 text-[11px] font-bold text-[#1B4332] cursor-pointer">
                                            Foto Ulang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-[10px] sm:text-[11px] text-[#718579] mt-2 px-1">
                            Anda dapat memilih foto dari galeri atau mengambil foto langsung dari kamera, lalu disimpan aman untuk verifikasi pengurus RT.
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
            
            <!-- Card 1: Laporan yang Baik, Penanganan Lebih Tepat (Interactive Checklist) -->
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

                <!-- 4 Step Interactive Guide (1-to-1 dengan field form) -->
                <div class="mt-5 space-y-3">
                    <!-- Step 1: Kategori -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-1-badge" class="w-6 h-6 rounded-full bg-[#2D6A4F] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            ✓
                        </div>
                        <p id="guide-step-1-text" class="text-xs text-[#142D21] leading-snug pt-0.5 font-semibold">
                            Pilih kategori yang paling sesuai
                        </p>
                    </div>

                    <!-- Step 2: Judul -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-2-badge" class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            2
                        </div>
                        <p id="guide-step-2-text" class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                            Tulis judul laporan yang jelas
                        </p>
                    </div>

                    <!-- Step 3: Lokasi -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-3-badge" class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            3
                        </div>
                        <p id="guide-step-3-text" class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                            Tulis lokasi dan patokan dengan jelas
                        </p>
                    </div>

                    <!-- Step 4: Uraian -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-4-badge" class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            4
                        </div>
                        <div class="pt-0.5">
                            <p id="guide-step-4-text" class="text-xs text-[#30483C] leading-snug font-medium">
                                Jelaskan kondisi secara singkat &amp; objektif
                            </p>
                            <span id="guide-step-4-sub" class="block text-[10px] text-[#718579] mt-0.5">
                                Minimal 20 karakter
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Privacy Reminder / Live Sensitive-Data Check -->
                <div id="privacy-guard-box" class="clay-sunken-note p-3 mt-4 flex items-start gap-2 text-[11px] text-[#4E6457]">
                    <svg id="privacy-guard-icon" class="w-4 h-4 text-[#2D6A4F] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span id="privacy-guard-text" class="leading-relaxed">
                        Hindari mencantumkan data pribadi (NIK / No. HP) pada foto atau uraian.
                    </span>
                </div>
            </div>

            <!-- Card 2: Setelah Laporan Dikirim -->
            <div class="clay-helper-card p-5">
                <span class="block text-[10px] font-black uppercase tracking-wider text-[#4E6659] mb-1.5">
                    SETELAH LAPORAN DIKIRIM
                </span>
                <p class="text-xs text-[#203D2E] leading-relaxed font-medium">
                    Admin RT akan memeriksa laporan. Anda dapat memantau perkembangan dan memberikan konfirmasi hasil melalui <a href="{{ route('warga.laporan.index') }}" class="font-bold text-[#1B4332] hover:underline">Laporan Saya</a>.
                </p>
            </div>

        </div>
    </div>
</div>

<!-- =========================================================================
     PURE VANILLA JS FOR FORM INTERACTIVITY
     (Kategori Selection, Custom Clay Autocomplete, Custom Clay Selects, Checklist, Photo)
     ========================================================================= -->
<script>
    (function () {
        let userModifiedDampak = {{ old('dampak') ? 'true' : 'false' }};
        let userModifiedUrgensi = {{ old('urgensi') ? 'true' : 'false' }};

        const areaDataset = @json($daftarArea ?? []);
        const lokasiDataset = @json($daftarLokasi ?? []);

        const kategoriInput = document.getElementById('kategori_id');
        const judulInput = document.getElementById('judul');
        const lokasiInput = document.getElementById('lokasi_detail');
        const blokInput = document.getElementById('blok');
        const dampakInput = document.getElementById('dampak');
        const urgensiInput = document.getElementById('urgensi');
        const uraianInput = document.getElementById('uraian');
        const fotoInput = document.getElementById('foto-input');
        const dropzone = document.getElementById('foto-dropzone');

        // 1. Custom Clay Select Dropdowns (Dampak & Urgensi)
        function closeAllCustomSelects(exceptField = null) {
            ['dampak', 'urgensi'].forEach(function (field) {
                if (field === exceptField) return;
                const menu = document.getElementById(field + '-menu');
                const trigger = document.getElementById(field + '-trigger');
                const chevron = document.getElementById(field + '-chevron');
                if (menu) menu.classList.add('hidden');
                if (trigger) {
                    trigger.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded', 'false');
                }
                if (chevron) chevron.classList.remove('rotate-180');
            });
        }

        window.toggleCustomSelect = function (field, event) {
            if (event) event.stopPropagation();
            closeAllAutocompletes();
            closeContohJudul();
            closeAllCustomSelects(field);

            const menu = document.getElementById(field + '-menu');
            const trigger = document.getElementById(field + '-trigger');
            const chevron = document.getElementById(field + '-chevron');
            if (!menu || !trigger) return;

            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            trigger.classList.toggle('is-open', isHidden);
            trigger.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            if (chevron) chevron.classList.toggle('rotate-180', isHidden);
        };

        window.selectCustomOption = function (field, value, isManual = true) {
            const hiddenInput = document.getElementById(field);
            const options = document.querySelectorAll('[data-select-option="' + field + '"]');
            let chosenOption = null;

            options.forEach(function (opt) {
                const checkEl = opt.querySelector('[data-opt-check]');
                if (opt.dataset.value === String(value)) {
                    chosenOption = opt;
                    opt.classList.add('is-selected');
                    if (checkEl) checkEl.classList.remove('hidden');
                } else {
                    opt.classList.remove('is-selected');
                    if (checkEl) checkEl.classList.add('hidden');
                }
            });

            if (hiddenInput && chosenOption) {
                hiddenInput.value = value;
                const dotEl = document.getElementById(field + '-trigger-dot');
                const labelEl = document.getElementById(field + '-trigger-label');
                const descEl = document.getElementById(field + '-trigger-desc');
                if (dotEl && chosenOption.dataset.dot) dotEl.style.backgroundColor = chosenOption.dataset.dot;
                if (labelEl) labelEl.textContent = chosenOption.dataset.label || '';
                if (descEl) descEl.textContent = chosenOption.dataset.short || '';
            }

            if (isManual) {
                if (field === 'dampak') {
                    userModifiedDampak = true;
                } else if (field === 'urgensi') {
                    userModifiedUrgensi = true;
                }
                closeAllCustomSelects();
            }
        };

        // 2. Category selection handler
        window.selectKategori = function (kategoriId, applySuggestions = true) {
            if (!kategoriInput) return;
            kategoriInput.value = kategoriId;

            const cards = document.querySelectorAll('[data-kategori-card]');
            let activeCard = null;

            cards.forEach(function (card) {
                const isTarget = card.dataset.kategoriId === String(kategoriId);
                const iconWrap = card.querySelector('[data-icon-wrap]');
                const checkBadge = card.querySelector('[data-check-badge]');
                const idleClasses = (card.dataset.idleClasses || '').split(' ').filter(Boolean);

                if (isTarget) {
                    activeCard = card;
                    card.classList.add('is-selected', 'ring-2', 'ring-[#2D6A4F]');
                    card.setAttribute('aria-pressed', 'true');

                    if (iconWrap) {
                        idleClasses.forEach(cls => iconWrap.classList.remove(cls));
                        iconWrap.classList.add('bg-[#2D6A4F]', 'text-white', 'shadow-sm');
                    }
                    if (checkBadge) {
                        checkBadge.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                        checkBadge.classList.add('opacity-100', 'scale-100');
                    }
                } else {
                    card.classList.remove('is-selected', 'ring-2', 'ring-[#2D6A4F]');
                    card.setAttribute('aria-pressed', 'false');

                    if (iconWrap) {
                        iconWrap.classList.remove('bg-[#2D6A4F]', 'text-white', 'shadow-sm');
                        idleClasses.forEach(cls => iconWrap.classList.add(cls));
                    }
                    if (checkBadge) {
                        checkBadge.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                        checkBadge.classList.remove('opacity-100', 'scale-100');
                    }
                }
            });

            if (!activeCard) return;

            // Update pill indicator & contextual guide
            const pillDot = document.getElementById('kategori-pill-dot');
            const pillName = document.getElementById('kategori-pill-name');
            const assistantTitle = document.getElementById('assistant-category-title');
            const assistantTips = document.getElementById('assistant-tips');
            const chipsContainer = document.getElementById('contoh-judul-chips');

            if (pillDot && activeCard.dataset.warna) {
                pillDot.style.backgroundColor = activeCard.dataset.warna;
            }
            if (pillName) {
                pillName.textContent = activeCard.dataset.fullName || activeCard.dataset.shortName;
            }
            if (assistantTitle) {
                assistantTitle.textContent = activeCard.dataset.shortName;
            }
            if (assistantTips) {
                assistantTips.textContent = activeCard.dataset.tips;
            }

            // Update dynamic placeholders
            if (judulInput && activeCard.dataset.placeholderJudul) {
                judulInput.placeholder = activeCard.dataset.placeholderJudul;
            }
            if (uraianInput && activeCard.dataset.placeholderUraian) {
                uraianInput.placeholder = activeCard.dataset.placeholderUraian;
            }

            // Rebuild quick title chips inside optional popover
            if (chipsContainer && activeCard.dataset.contohJudul) {
                try {
                    const examples = JSON.parse(activeCard.dataset.contohJudul);
                    chipsContainer.innerHTML = '';
                    examples.forEach(function (contoh) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.dataset.judul = contoh;
                        btn.onclick = function () {
                            window.applyJudulChip(btn);
                        };
                        btn.className = 'clay-dropdown-option w-full px-3 py-2 text-left text-xs font-semibold text-[#142D21] flex items-center justify-between gap-2';

                        const labelSpan = document.createElement('span');
                        labelSpan.textContent = contoh;
                        const actionSpan = document.createElement('span');
                        actionSpan.className = 'text-[10px] font-bold text-[#2D6A4F] shrink-0';
                        actionSpan.textContent = 'Pakai';

                        btn.appendChild(labelSpan);
                        btn.appendChild(actionSpan);
                        chipsContainer.appendChild(btn);
                    });
                } catch (err) {
                    console.error('Gagal memproses contoh judul:', err);
                }
            }

            // Smart suggestion for Custom Dampak & Urgensi dropdowns
            if (applySuggestions) {
                if (dampakInput && !userModifiedDampak && activeCard.dataset.suggestedDampak) {
                    window.selectCustomOption('dampak', activeCard.dataset.suggestedDampak, false);
                }
                if (urgensiInput && !userModifiedUrgensi && activeCard.dataset.suggestedUrgensi) {
                    window.selectCustomOption('urgensi', activeCard.dataset.suggestedUrgensi, false);
                }
            }

            updateChecklist();
        };

        // 3. Optional Quick Title Popover & Chip Applier
        const contohPopover = document.getElementById('contoh-judul-popover');
        const contohChevron = document.getElementById('contoh-judul-chevron');

        function closeContohJudul() {
            if (contohPopover) contohPopover.classList.add('hidden');
            if (contohChevron) contohChevron.classList.remove('rotate-180');
        }

        window.toggleContohJudul = function (event) {
            if (event) event.stopPropagation();
            closeAllCustomSelects();
            closeAllAutocompletes();
            if (!contohPopover) return;
            const isHidden = contohPopover.classList.contains('hidden');
            contohPopover.classList.toggle('hidden');
            if (contohChevron) contohChevron.classList.toggle('rotate-180', isHidden);
        };

        window.applyJudulChip = function (buttonEl) {
            if (!judulInput || !buttonEl) return;
            const suggested = buttonEl.dataset.judul || '';
            judulInput.value = suggested;
            closeContohJudul();
            judulInput.focus();
            judulInput.classList.add('ring-2', 'ring-[#2D6A4F]/50');
            setTimeout(function () {
                judulInput.classList.remove('ring-2', 'ring-[#2D6A4F]/50');
            }, 600);
            updateChecklist();
        };

        // 3b. Reusable Custom Clay Autocomplete Engine (Area Lingkungan & Lokasi Masalah)
        function createHighlightedSpan(text, query) {
            const span = document.createElement('span');
            span.className = 'truncate';
            if (!query) {
                span.textContent = text;
                return span;
            }
            const lowerText = text.toLowerCase();
            const idx = lowerText.indexOf(query.toLowerCase());
            if (idx === -1) {
                span.textContent = text;
                return span;
            }
            span.appendChild(document.createTextNode(text.slice(0, idx)));
            const strong = document.createElement('strong');
            strong.className = 'font-black text-[#1B4332] underline decoration-[#2D6A4F]/40';
            strong.textContent = text.slice(idx, idx + query.length);
            span.appendChild(strong);
            span.appendChild(document.createTextNode(text.slice(idx + query.length)));
            return span;
        }

        const blokMenu = document.getElementById('blok-autocomplete-menu');
        const blokChevron = document.getElementById('blok-chevron');
        const blokClearBtn = document.getElementById('blok-clear-btn');
        const lokasiMenu = document.getElementById('lokasi-autocomplete-menu');
        let blokHighlightIndex = -1;
        let lokasiHighlightIndex = -1;

        function closeAllAutocompletes(except = null) {
            if (except !== 'blok' && blokMenu) {
                blokMenu.classList.add('hidden');
                if (blokChevron) blokChevron.classList.remove('rotate-180');
                blokHighlightIndex = -1;
            }
            if (except !== 'lokasi' && lokasiMenu) {
                lokasiMenu.classList.add('hidden');
                lokasiHighlightIndex = -1;
            }
        }

        function syncBlokClearBtn() {
            if (!blokClearBtn || !blokInput) return;
            if (blokInput.value.trim().length > 0) {
                blokClearBtn.classList.remove('hidden');
            } else {
                blokClearBtn.classList.add('hidden');
            }
        }

        function syncAreaChips() {
            if (!blokInput) return;
            const currentVal = blokInput.value.trim().toLowerCase();
            document.querySelectorAll('[data-area-chip]').forEach(function (chip) {
                const isActive = currentVal !== '' && (chip.dataset.area || '').trim().toLowerCase() === currentVal;
                if (isActive) {
                    chip.classList.remove('bg-white/80', 'text-[#1B4332]', 'border-[#C6D4CA]');
                    chip.classList.add('bg-[#1B4332]', 'text-white', 'border-[#1B4332]');
                } else {
                    chip.classList.remove('bg-[#1B4332]', 'text-white', 'border-[#1B4332]');
                    chip.classList.add('bg-white/80', 'text-[#1B4332]', 'border-[#C6D4CA]');
                }
            });
            syncBlokClearBtn();
        }

        function renderBlokAutocomplete(showAll = false) {
            if (!blokMenu || !blokInput) return;
            const query = blokInput.value.trim();
            const lowerQuery = query.toLowerCase();
            const matches = showAll || query === ''
                ? areaDataset
                : areaDataset.filter(item => item.nama.toLowerCase().includes(lowerQuery));

            blokMenu.innerHTML = '';
            blokHighlightIndex = -1;

            const exactMatch = areaDataset.some(item => item.nama.toLowerCase() === lowerQuery);

            if (matches.length === 0 && query === '') {
                const emptyNote = document.createElement('div');
                emptyNote.className = 'px-3 py-2 text-xs text-[#60776C] italic';
                emptyNote.textContent = 'Belum ada daftar area RT. Silakan ketik nama jalan atau gang secara langsung.';
                blokMenu.appendChild(emptyNote);
            } else {
                matches.forEach(function (item) {
                    const isSelected = query !== '' && item.nama.toLowerCase() === lowerQuery;
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.dataset.autocompleteItem = 'blok';
                    row.className = 'clay-dropdown-option w-full px-3 py-2.5 flex items-center justify-between gap-2 text-left text-xs ' + (isSelected ? 'is-selected' : '');

                    const left = document.createElement('div');
                    left.className = 'flex items-center gap-2 min-w-0';
                    const pin = document.createElement('span');
                    pin.className = 'w-1.5 h-1.5 rounded-full shrink-0 ' + (item.is_master ? 'bg-[#2D6A4F]' : 'bg-[#8EA598]');
                    left.appendChild(pin);
                    left.appendChild(createHighlightedSpan(item.nama, showAll ? '' : query));

                    const tag = document.createElement('span');
                    tag.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 ' + (item.is_master ? 'bg-[#E8F1EC] text-[#1B4332]' : 'bg-[#EFECE5] text-[#60776C]');
                    tag.textContent = item.label || 'Area RT';

                    row.appendChild(left);
                    row.appendChild(tag);

                    row.onclick = function () {
                        blokInput.value = item.nama;
                        syncAreaChips();
                        closeAllAutocompletes();
                    };
                    blokMenu.appendChild(row);
                });

                if (query !== '' && !exactMatch) {
                    const customBtn = document.createElement('button');
                    customBtn.type = 'button';
                    customBtn.dataset.autocompleteItem = 'blok';
                    customBtn.className = 'clay-dropdown-option w-full px-3 py-2.5 flex items-center justify-between gap-2 text-left text-xs border-t border-[#E3DDD1] mt-1';

                    const textSpan = document.createElement('span');
                    textSpan.className = 'font-bold text-[#1B4332] truncate';
                    textSpan.textContent = '+ Pakai area baru: "' + query + '"';

                    const badge = document.createElement('span');
                    badge.className = 'px-2 py-0.5 rounded-full bg-[#D8ECE0] text-[#1B4332] text-[10px] font-extrabold shrink-0';
                    badge.textContent = 'Baru';

                    customBtn.appendChild(textSpan);
                    customBtn.appendChild(badge);
                    customBtn.onclick = function () {
                        blokInput.value = query;
                        syncAreaChips();
                        closeAllAutocompletes();
                    };
                    blokMenu.appendChild(customBtn);
                }
            }

            closeAllCustomSelects();
            closeAllAutocompletes('blok');
            blokMenu.classList.remove('hidden');
            if (blokChevron) blokChevron.classList.add('rotate-180');
        }

        function renderLokasiAutocomplete() {
            if (!lokasiMenu || !lokasiInput) return;
            const query = lokasiInput.value.trim();
            const lowerQuery = query.toLowerCase();
            const matches = query === ''
                ? lokasiDataset
                : lokasiDataset.filter(loc => loc.toLowerCase().includes(lowerQuery));

            if (matches.length === 0) {
                lokasiMenu.classList.add('hidden');
                return;
            }

            lokasiMenu.innerHTML = '';
            lokasiHighlightIndex = -1;

            const header = document.createElement('div');
            header.className = 'px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider text-[#6A7F73]';
            header.textContent = query === '' ? 'Riwayat Lokasi di RT Ini' : 'Kecocokan Lokasi RT';
            lokasiMenu.appendChild(header);

            matches.forEach(function (loc) {
                const isSelected = query !== '' && loc.toLowerCase() === lowerQuery;
                const row = document.createElement('button');
                row.type = 'button';
                row.dataset.autocompleteItem = 'lokasi';
                row.className = 'clay-dropdown-option w-full px-3 py-2 flex items-center justify-between gap-2 text-left text-xs text-[#142D21] ' + (isSelected ? 'is-selected' : '');

                const left = document.createElement('div');
                left.className = 'flex items-center gap-2 min-w-0';
                const dot = document.createElement('span');
                dot.className = 'w-1.5 h-1.5 rounded-full bg-[#2D6A4F] shrink-0';
                left.appendChild(dot);
                left.appendChild(createHighlightedSpan(loc, query));

                row.appendChild(left);
                row.onclick = function () {
                    lokasiInput.value = loc;
                    closeAllAutocompletes();
                    updateChecklist();
                };
                lokasiMenu.appendChild(row);
            });

            closeAllCustomSelects();
            closeAllAutocompletes('lokasi');
            lokasiMenu.classList.remove('hidden');
        }

        function handleAutocompleteKeydown(e, menuEl, kind) {
            if (!menuEl || menuEl.classList.contains('hidden')) return;
            const items = menuEl.querySelectorAll('[data-autocomplete-item="' + kind + '"]');
            if (items.length === 0) return;

            let idx = kind === 'blok' ? blokHighlightIndex : lokasiHighlightIndex;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                idx = (idx + 1) % items.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                idx = (idx - 1 + items.length) % items.length;
            } else if (e.key === 'Enter' && idx >= 0) {
                e.preventDefault();
                items[idx].click();
                return;
            } else if (e.key === 'Escape') {
                closeAllAutocompletes();
                return;
            } else {
                return;
            }

            if (kind === 'blok') blokHighlightIndex = idx;
            else lokasiHighlightIndex = idx;

            items.forEach(function (el, i) {
                el.classList.toggle('is-highlighted', i === idx);
                if (i === idx) el.scrollIntoView({ block: 'nearest' });
            });
        }

        window.toggleBlokAutocomplete = function () {
            if (!blokMenu) return;
            if (blokMenu.classList.contains('hidden')) {
                renderBlokAutocomplete(true);
                if (blokInput) blokInput.focus();
            } else {
                closeAllAutocompletes();
            }
        };

        window.clearBlokInput = function () {
            if (!blokInput) return;
            blokInput.value = '';
            syncAreaChips();
            renderBlokAutocomplete(true);
            blokInput.focus();
        };

        window.applyAreaChip = function (buttonEl) {
            if (!blokInput || !buttonEl) return;
            const areaName = buttonEl.dataset.area || '';
            if (blokInput.value.trim().toLowerCase() === areaName.trim().toLowerCase()) {
                blokInput.value = '';
            } else {
                blokInput.value = areaName;
                blokInput.classList.add('ring-2', 'ring-[#2D6A4F]/50');
                setTimeout(function () {
                    blokInput.classList.remove('ring-2', 'ring-[#2D6A4F]/50');
                }, 500);
            }
            syncAreaChips();
            closeAllAutocompletes();
        };

        if (blokInput) {
            blokInput.addEventListener('focus', function () {
                renderBlokAutocomplete(true);
            });
            blokInput.addEventListener('input', function () {
                syncAreaChips();
                renderBlokAutocomplete(false);
            });
            blokInput.addEventListener('keydown', function (e) {
                handleAutocompleteKeydown(e, blokMenu, 'blok');
            });
            syncAreaChips();
        }

        if (lokasiInput) {
            lokasiInput.addEventListener('focus', renderLokasiAutocomplete);
            lokasiInput.addEventListener('input', renderLokasiAutocomplete);
            lokasiInput.addEventListener('keydown', function (e) {
                handleAutocompleteKeydown(e, lokasiMenu, 'lokasi');
            });
        }

        // Global outside click & Escape closer
        document.addEventListener('click', function (e) {
            const dampakWrap = document.getElementById('custom-select-dampak');
            const urgensiWrap = document.getElementById('custom-select-urgensi');
            const blokWrap = document.getElementById('blok-autocomplete-wrap');
            const lokasiWrap = document.getElementById('lokasi-autocomplete-wrap');
            const judulWrap = document.getElementById('judul-preset-wrap');

            if ((!dampakWrap || !dampakWrap.contains(e.target)) && (!urgensiWrap || !urgensiWrap.contains(e.target))) {
                closeAllCustomSelects();
            }
            if ((!blokWrap || !blokWrap.contains(e.target)) && (!lokasiWrap || !lokasiWrap.contains(e.target))) {
                closeAllAutocompletes();
            }
            if (!judulWrap || !judulWrap.contains(e.target)) {
                closeContohJudul();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllCustomSelects();
                closeAllAutocompletes();
                closeContohJudul();
            }
        });

        // 4. Uraian character counter & readiness
        function updateUraianCounter() {
            if (!uraianInput) return;
            const len = uraianInput.value.length;
            const countEl = document.getElementById('uraian-char-count');
            const statusEl = document.getElementById('uraian-status-text');

            if (countEl) countEl.textContent = len;
            if (statusEl) {
                if (len >= 20) {
                    statusEl.textContent = '✓ Panjang memadai';
                    statusEl.className = 'text-[#1B4332] font-bold';
                } else {
                    statusEl.textContent = 'Butuh ' + (20 - len) + ' karakter lagi';
                    statusEl.className = 'text-[#A07040]';
                }
            }
            updateChecklist();
        }

        if (uraianInput) {
            uraianInput.addEventListener('input', updateUraianCounter);
        }

        if (judulInput) {
            judulInput.addEventListener('input', updateChecklist);
        }

        if (lokasiInput) {
            lokasiInput.addEventListener('input', updateChecklist);
        }

        // 5. Right-panel live checklist updater + sensitive data guard
        function setStepState(stepNum, done) {
            const badgeEl = document.getElementById('guide-step-' + stepNum + '-badge');
            const textEl = document.getElementById('guide-step-' + stepNum + '-text');

            if (badgeEl) {
                if (done) {
                    badgeEl.textContent = '✓';
                    badgeEl.classList.remove('bg-[#E5DFD4]', 'text-[#142D21]');
                    badgeEl.classList.add('bg-[#2D6A4F]', 'text-white');
                } else {
                    badgeEl.textContent = String(stepNum);
                    badgeEl.classList.remove('bg-[#2D6A4F]', 'text-white');
                    badgeEl.classList.add('bg-[#E5DFD4]', 'text-[#142D21]');
                }
            }

            if (textEl) {
                if (done) {
                    textEl.classList.remove('text-[#30483C]', 'font-medium');
                    textEl.classList.add('text-[#142D21]', 'font-semibold');
                } else {
                    textEl.classList.remove('text-[#142D21]', 'font-semibold');
                    textEl.classList.add('text-[#30483C]', 'font-medium');
                }
            }
        }

        function updateChecklist() {
            const hasKategori = Boolean(kategoriInput && kategoriInput.value);
            const hasJudul = Boolean(judulInput && judulInput.value.trim().length > 0);
            const hasLokasi = Boolean(lokasiInput && lokasiInput.value.trim().length > 0);
            const uraianTrimLen = uraianInput ? uraianInput.value.trim().length : 0;
            const hasUraian = uraianTrimLen >= 20;

            setStepState(1, hasKategori);
            setStepState(2, hasJudul);
            setStepState(3, hasLokasi);
            setStepState(4, hasUraian);

            const step4Sub = document.getElementById('guide-step-4-sub');
            if (step4Sub) {
                if (hasUraian) {
                    step4Sub.textContent = '✓ Panjang uraian memadai';
                    step4Sub.className = 'block text-[10px] font-bold text-[#1B4332] mt-0.5';
                } else if (uraianTrimLen > 0) {
                    step4Sub.textContent = 'Kurang ' + (20 - uraianTrimLen) + ' karakter lagi';
                    step4Sub.className = 'block text-[10px] font-semibold text-[#A07040] mt-0.5';
                } else {
                    step4Sub.textContent = 'Minimal 20 karakter';
                    step4Sub.className = 'block text-[10px] text-[#718579] mt-0.5';
                }
            }

            // Live sensitive-data check (NIK 16 digit / No. HP 08xx)
            const privacyBox = document.getElementById('privacy-guard-box');
            const privacyIcon = document.getElementById('privacy-guard-icon');
            const privacyText = document.getElementById('privacy-guard-text');
            if (privacyBox && privacyText) {
                const combinedText = [
                    judulInput ? judulInput.value : '',
                    lokasiInput ? lokasiInput.value : '',
                    uraianInput ? uraianInput.value : ''
                ].join(' ');

                const hasSensitiveDigits = /\b\d{16}\b/.test(combinedText) || /\b(?:\+62|62|0)8[1-9][0-9]{7,10}\b/.test(combinedText);
                if (hasSensitiveDigits) {
                    privacyBox.classList.add('border', 'border-amber-300', 'bg-amber-50/80', 'text-amber-900');
                    privacyBox.classList.remove('text-[#4E6457]');
                    if (privacyIcon) {
                        privacyIcon.classList.remove('text-[#2D6A4F]');
                        privacyIcon.classList.add('text-amber-600');
                    }
                    privacyText.textContent = 'Terdeteksi angka menyerupai NIK atau nomor HP. Sebaiknya hapus data pribadi sebelum mengirim.';
                } else {
                    privacyBox.classList.remove('border', 'border-amber-300', 'bg-amber-50/80', 'text-amber-900');
                    privacyBox.classList.add('text-[#4E6457]');
                    if (privacyIcon) {
                        privacyIcon.classList.add('text-[#2D6A4F]');
                        privacyIcon.classList.remove('text-amber-600');
                    }
                    privacyText.textContent = 'Hindari mencantumkan data pribadi (NIK / No. HP) pada foto atau uraian.';
                }
            }
        }

        // 6. Photo Upload, Drag-n-Drop & Live Camera Capture
        let activeMediaStream = null;
        let currentFacingMode = 'environment';

        const emptyState = document.getElementById('foto-empty-state');
        const cameraState = document.getElementById('foto-camera-state');
        const previewState = document.getElementById('foto-preview-state');
        const cameraVideo = document.getElementById('camera-video');
        const cameraErrorBox = document.getElementById('camera-error-box');
        const cameraErrorText = document.getElementById('camera-error-text');
        const btnCapture = document.getElementById('btn-capture-snapshot');
        const btnSwitchCamera = document.getElementById('btn-switch-camera');
        const cameraFallbackInput = document.getElementById('foto-camera-fallback');

        function stopCameraStream() {
            if (activeMediaStream) {
                activeMediaStream.getTracks().forEach(function (track) {
                    track.stop();
                });
                activeMediaStream = null;
            }
            if (cameraVideo) {
                cameraVideo.srcObject = null;
            }
        }

        function assignFileToMainInput(file) {
            if (!fotoInput || !file) return;
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fotoInput.files = dataTransfer.files;
            handlePhotoFile(file);
        }

        function handlePhotoFile(file) {
            if (!file) return;

            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Format foto harus JPG, PNG, atau WebP.');
                if (fotoInput) fotoInput.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran foto melebihi batas 5 MB. Silakan pilih foto dengan ukuran lebih kecil.');
                if (fotoInput) fotoInput.value = '';
                return;
            }

            const previewImg = document.getElementById('foto-preview-img');
            const previewName = document.getElementById('foto-preview-name');
            const previewSize = document.getElementById('foto-preview-size');

            const reader = new FileReader();
            reader.onload = function (e) {
                stopCameraStream();
                if (previewImg) previewImg.src = e.target.result;
                if (previewName) previewName.textContent = file.name;
                if (previewSize) previewSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                if (emptyState) emptyState.classList.add('hidden');
                if (cameraState) cameraState.classList.add('hidden');
                if (previewState) previewState.classList.remove('hidden');
                updateChecklist();
            };
            reader.readAsDataURL(file);
        }

        async function startCameraStream() {
            stopCameraStream();

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                if (cameraErrorBox && cameraErrorText) {
                    cameraErrorText.textContent = 'Browser tidak mendukung kamera langsung di halaman ini. Gunakan kamera bawaan perangkat di bawah.';
                    cameraErrorBox.classList.remove('hidden');
                }
                if (btnCapture) btnCapture.classList.add('hidden');
                if (btnSwitchCamera) btnSwitchCamera.classList.add('hidden');
                return;
            }

            if (cameraErrorBox) cameraErrorBox.classList.add('hidden');
            if (btnCapture) btnCapture.classList.remove('hidden');
            if (btnSwitchCamera) btnSwitchCamera.classList.remove('hidden');

            try {
                activeMediaStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: currentFacingMode },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });
                if (cameraVideo) {
                    cameraVideo.srcObject = activeMediaStream;
                }
            } catch (err) {
                // Fallback ke constraint sederhana jika facingMode tidak tersedia (misal webcam laptop)
                try {
                    activeMediaStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    if (cameraVideo) {
                        cameraVideo.srcObject = activeMediaStream;
                    }
                } catch (fallbackErr) {
                    if (cameraErrorBox && cameraErrorText) {
                        cameraErrorText.textContent = 'Izin kamera belum aktif atau kamera tidak terdeteksi. Izinkan akses kamera browser atau buka kamera perangkat.';
                        cameraErrorBox.classList.remove('hidden');
                    }
                    if (btnCapture) btnCapture.classList.add('hidden');
                    if (btnSwitchCamera) btnSwitchCamera.classList.add('hidden');
                }
            }
        }

        window.openCameraCapture = function (event) {
            if (event) event.stopPropagation();
            if (emptyState) emptyState.classList.add('hidden');
            if (previewState) previewState.classList.add('hidden');
            if (cameraState) cameraState.classList.remove('hidden');
            startCameraStream();
        };

        window.closeCameraCapture = function (event) {
            if (event) event.stopPropagation();
            stopCameraStream();
            if (cameraState) cameraState.classList.add('hidden');
            if (fotoInput && fotoInput.files && fotoInput.files.length > 0) {
                if (previewState) previewState.classList.remove('hidden');
            } else {
                if (emptyState) emptyState.classList.remove('hidden');
            }
        };

        window.switchCameraFacing = function (event) {
            if (event) event.stopPropagation();
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            startCameraStream();
        };

        window.captureCameraSnapshot = function (event) {
            if (event) event.stopPropagation();
            if (!cameraVideo || !cameraVideo.videoWidth || !cameraVideo.videoHeight) return;

            const canvas = document.createElement('canvas');
            canvas.width = cameraVideo.videoWidth;
            canvas.height = cameraVideo.videoHeight;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(function (blob) {
                if (!blob) return;
                const now = new Date();
                const stamp = now.toISOString().replace(/[-:T.]/g, '').slice(0, 14);
                const capturedFile = new File([blob], 'foto_kamera_' + stamp + '.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });
                assignFileToMainInput(capturedFile);
            }, 'image/jpeg', 0.9);
        };

        if (fotoInput) {
            fotoInput.addEventListener('change', function (e) {
                if (e.target.files && e.target.files[0]) {
                    handlePhotoFile(e.target.files[0]);
                }
            });
        }

        if (cameraFallbackInput) {
            cameraFallbackInput.addEventListener('change', function (e) {
                if (e.target.files && e.target.files[0]) {
                    assignFileToMainInput(e.target.files[0]);
                    cameraFallbackInput.value = '';
                }
            });
        }

        window.clearPhoto = function (event) {
            if (event) event.stopPropagation();
            stopCameraStream();
            if (fotoInput) fotoInput.value = '';
            const previewImg = document.getElementById('foto-preview-img');

            if (previewImg) previewImg.src = '';
            if (previewState) previewState.classList.add('hidden');
            if (cameraState) cameraState.classList.add('hidden');
            if (emptyState) emptyState.classList.remove('hidden');
            updateChecklist();
        };

        if (dropzone) {
            ['dragenter', 'dragover'].forEach(function (evtName) {
                dropzone.addEventListener(evtName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('ring-2', 'ring-[#2D6A4F]', 'bg-[#DCE7DF]');
                });
            });

            ['dragleave', 'drop'].forEach(function (evtName) {
                dropzone.addEventListener(evtName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('ring-2', 'ring-[#2D6A4F]', 'bg-[#DCE7DF]');
                });
            });

            dropzone.addEventListener('drop', function (e) {
                const files = e.dataTransfer ? e.dataTransfer.files : null;
                if (files && files[0]) {
                    assignFileToMainInput(files[0]);
                }
            });
        }

        window.addEventListener('beforeunload', stopCameraStream);

        // Initialize state on load
        if (kategoriInput && kategoriInput.value) {
            window.selectKategori(kategoriInput.value, !userModifiedDampak && !userModifiedUrgensi);
        }
        updateUraianCounter();
    })();
</script>
@endsection
