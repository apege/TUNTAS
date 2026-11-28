@extends('layouts.warga')

@section('header-title', 'Buat Laporan')

@section('warga-content')
@php
    $selectedKategoriId = old('kategori_id', $kategoris->first()->id ?? '');
    $initialKategori = $kategoris->firstWhere('id', $selectedKategoriId) ?? $kategoris->first();
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

                        <!-- Smart Contextual Assistant for Selected Category -->
                        @if ($initialKategori)
                            <div id="kategori-assistant" class="clay-sunken-note p-3.5 sm:p-4 mt-3.5 space-y-2.5">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-[#D8ECE0] text-[#1B4332] flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#2D6A4F]">
                                            Panduan Kategori <span id="assistant-category-title">{{ $initialKategori->short_name }}</span>
                                        </span>
                                        <p id="assistant-tips" class="text-xs text-[#385244] mt-0.5 leading-relaxed">
                                            {{ $initialKategori->tips }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Quick Title Preset Chips -->
                                <div class="pt-2 border-t border-[#DCD6C8] flex flex-wrap items-center gap-1.5">
                                    <span class="text-[10px] font-bold text-[#60776C] mr-1">Saran judul cepat:</span>
                                    <div id="contoh-judul-chips" class="flex flex-wrap gap-1.5">
                                        @foreach ($initialKategori->contoh_judul ?? [] as $contoh)
                                            <button type="button"
                                                    onclick="applyJudulChip(this)"
                                                    data-judul="{{ $contoh }}"
                                                    class="px-2.5 py-1 rounded-full bg-white/80 hover:bg-[#1B4332] hover:text-white text-[#1B4332] border border-[#C6D4CA] text-[11px] font-semibold transition-all shadow-2xs cursor-pointer">
                                                + {{ $contoh }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
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
                               placeholder="{{ $initialKategori->placeholder_judul ?? 'Contoh: Saluran air tersumbat di Gang 2' }}"
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
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label for="dampak" class="block text-xs sm:text-sm font-extrabold text-[#142D21]">
                                    Dampak bagi warga <span class="text-red-500">*</span>
                                </label>
                                <span id="dampak-auto-badge" class="hidden text-[10px] font-bold text-[#2D6A4F] bg-[#E8F1EC] px-2 py-0.5 rounded-full">
                                    Rekomendasi
                                </span>
                            </div>
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
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label for="urgensi" class="block text-xs sm:text-sm font-extrabold text-[#142D21]">
                                    Urgensi penanganan <span class="text-red-500">*</span>
                                </label>
                                <span id="urgensi-auto-badge" class="hidden text-[10px] font-bold text-[#2D6A4F] bg-[#E8F1EC] px-2 py-0.5 rounded-full">
                                    Rekomendasi
                                </span>
                            </div>
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

                        <!-- Upload Dropzone Container -->
                        <div id="foto-dropzone"
                             class="clay-upload-dropzone p-6 sm:p-8 flex flex-col items-center justify-center text-center cursor-pointer transition-all"
                             onclick="document.getElementById('foto-input').click()">
                            
                            <!-- State 1: No Photo Selected -->
                            <div id="foto-empty-state" class="flex flex-col items-center">
                                <div class="clay-pill w-12 h-12 flex items-center justify-center text-[#2D6A4F] mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h4 class="font-extrabold text-xs sm:text-sm text-[#142D21]">
                                    Pilih atau seret foto kondisi lingkungan
                                </h4>
                                <p class="text-[11px] text-[#63796E] mt-1 font-medium">
                                    JPG, PNG, atau WebP • maksimal 5 MB
                                </p>
                            </div>

                            <!-- State 2: Photo Selected & Preview Visible -->
                            <div id="foto-preview-state" class="hidden w-full flex flex-col items-center space-y-3">
                                <div class="relative max-w-xs rounded-2xl overflow-hidden border-2 border-white shadow-md">
                                    <img id="foto-preview-img" src="" alt="Pratinjau Foto" class="w-full h-44 object-cover">
                                    <button type="button" 
                                            onclick="clearPhoto(event)" 
                                            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center text-xs font-bold shadow-md hover:bg-red-700 transition-colors"
                                            title="Hapus foto">
                                        ✕
                                    </button>
                                </div>
                                <div class="text-center">
                                    <span id="foto-preview-name" class="block text-xs font-bold text-[#142D21]"></span>
                                    <span id="foto-preview-size" class="block text-[10px] text-[#63796E]"></span>
                                    <span class="inline-block text-[11px] font-bold text-[#2D6A4F] mt-1 hover:underline">
                                        Klik untuk mengganti foto
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="text-[10px] sm:text-[11px] text-[#718579] mt-2 px-1">
                            Foto hanya dibaca sebagai pratinjau lokal sebelum dikirim, lalu disimpan aman untuk verifikasi pengurus RT.
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

                <!-- 4 Step Interactive Guide -->
                <div class="mt-5 space-y-3">
                    <!-- Step 1 -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-1-badge" class="w-6 h-6 rounded-full bg-[#2D6A4F] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            ✓
                        </div>
                        <p class="text-xs text-[#142D21] leading-snug pt-0.5 font-semibold">
                            Pilih kategori yang paling sesuai
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-2-badge" class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            2
                        </div>
                        <p class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                            Tulis judul & patokan lokasi dengan jelas
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-3-badge" class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
                            3
                        </div>
                        <p class="text-xs text-[#30483C] leading-snug pt-0.5 font-medium">
                            Jelaskan kondisi secara singkat dan objektif
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex items-start gap-3">
                        <div id="guide-step-4-badge" class="w-6 h-6 rounded-full bg-[#E5DFD4] text-[#142D21] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-white/60 transition-colors">
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

<!-- =========================================================================
     PURE VANILLA JS FOR FORM INTERACTIVITY
     (Kategori Selection, Contextual Tips, Quick Title Chips, Character Count, Photo Upload)
     ========================================================================= -->
<script>
    (function () {
        let userModifiedDampak = {{ old('dampak') ? 'true' : 'false' }};
        let userModifiedUrgensi = {{ old('urgensi') ? 'true' : 'false' }};

        const kategoriInput = document.getElementById('kategori_id');
        const judulInput = document.getElementById('judul');
        const lokasiInput = document.getElementById('lokasi_detail');
        const dampakSelect = document.getElementById('dampak');
        const urgensiSelect = document.getElementById('urgensi');
        const uraianInput = document.getElementById('uraian');
        const fotoInput = document.getElementById('foto-input');
        const dropzone = document.getElementById('foto-dropzone');

        // 1. Track manual changes to Dampak & Urgensi
        if (dampakSelect) {
            dampakSelect.addEventListener('change', function () {
                userModifiedDampak = true;
                const badge = document.getElementById('dampak-auto-badge');
                if (badge) badge.classList.add('hidden');
            });
        }

        if (urgensiSelect) {
            urgensiSelect.addEventListener('change', function () {
                userModifiedUrgensi = true;
                const badge = document.getElementById('urgensi-auto-badge');
                if (badge) badge.classList.add('hidden');
            });
        }

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

            // Rebuild quick title chips
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
                        btn.className = 'px-2.5 py-1 rounded-full bg-white/80 hover:bg-[#1B4332] hover:text-white text-[#1B4332] border border-[#C6D4CA] text-[11px] font-semibold transition-all shadow-2xs cursor-pointer';
                        btn.textContent = '+ ' + contoh;
                        chipsContainer.appendChild(btn);
                    });
                } catch (err) {
                    console.error('Gagal memproses contoh judul:', err);
                }
            }

            // Smart suggestion for Dampak & Urgensi when user hasn't manually customized them
            if (applySuggestions) {
                if (dampakSelect && !userModifiedDampak && activeCard.dataset.suggestedDampak) {
                    dampakSelect.value = activeCard.dataset.suggestedDampak;
                    const badge = document.getElementById('dampak-auto-badge');
                    if (badge) badge.classList.remove('hidden');
                }
                if (urgensiSelect && !userModifiedUrgensi && activeCard.dataset.suggestedUrgensi) {
                    urgensiSelect.value = activeCard.dataset.suggestedUrgensi;
                    const badge = document.getElementById('urgensi-auto-badge');
                    if (badge) badge.classList.remove('hidden');
                }
            }

            updateChecklist();
        };

        // 3. Apply Quick Title Chip
        window.applyJudulChip = function (buttonEl) {
            if (!judulInput || !buttonEl) return;
            const suggested = buttonEl.dataset.judul || '';
            judulInput.value = suggested;
            judulInput.focus();
            judulInput.classList.add('ring-2', 'ring-[#2D6A4F]/50');
            setTimeout(function () {
                judulInput.classList.remove('ring-2', 'ring-[#2D6A4F]/50');
            }, 600);
            updateChecklist();
        };

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

        // 5. Right-panel live checklist updater
        function setBadgeDone(badgeEl, numberLabel, done) {
            if (!badgeEl) return;
            if (done) {
                badgeEl.textContent = '✓';
                badgeEl.classList.remove('bg-[#E5DFD4]', 'text-[#142D21]');
                badgeEl.classList.add('bg-[#2D6A4F]', 'text-white');
            } else {
                badgeEl.textContent = String(numberLabel);
                badgeEl.classList.remove('bg-[#2D6A4F]', 'text-white');
                badgeEl.classList.add('bg-[#E5DFD4]', 'text-[#142D21]');
            }
        }

        function updateChecklist() {
            const hasKategori = Boolean(kategoriInput && kategoriInput.value);
            const hasJudulAndLokasi = Boolean(
                judulInput && judulInput.value.trim().length > 0 &&
                lokasiInput && lokasiInput.value.trim().length > 0
            );
            const hasUraian = Boolean(uraianInput && uraianInput.value.trim().length >= 20);
            const hasPhoto = Boolean(fotoInput && fotoInput.files && fotoInput.files.length > 0);

            setBadgeDone(document.getElementById('guide-step-1-badge'), 1, hasKategori);
            setBadgeDone(document.getElementById('guide-step-2-badge'), 2, hasJudulAndLokasi);
            setBadgeDone(document.getElementById('guide-step-3-badge'), 3, hasUraian);
            setBadgeDone(document.getElementById('guide-step-4-badge'), 4, hasPhoto);
        }

        // 6. Photo Upload & Drag-n-Drop Preview
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

            const emptyState = document.getElementById('foto-empty-state');
            const previewState = document.getElementById('foto-preview-state');
            const previewImg = document.getElementById('foto-preview-img');
            const previewName = document.getElementById('foto-preview-name');
            const previewSize = document.getElementById('foto-preview-size');

            const reader = new FileReader();
            reader.onload = function (e) {
                if (previewImg) previewImg.src = e.target.result;
                if (previewName) previewName.textContent = file.name;
                if (previewSize) previewSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                if (emptyState) emptyState.classList.add('hidden');
                if (previewState) previewState.classList.remove('hidden');
                updateChecklist();
            };
            reader.readAsDataURL(file);
        }

        if (fotoInput) {
            fotoInput.addEventListener('change', function (e) {
                if (e.target.files && e.target.files[0]) {
                    handlePhotoFile(e.target.files[0]);
                }
            });
        }

        window.clearPhoto = function (event) {
            if (event) event.stopPropagation();
            if (fotoInput) fotoInput.value = '';
            const emptyState = document.getElementById('foto-empty-state');
            const previewState = document.getElementById('foto-preview-state');
            const previewImg = document.getElementById('foto-preview-img');

            if (previewImg) previewImg.src = '';
            if (previewState) previewState.classList.add('hidden');
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
                if (files && files[0] && fotoInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[0]);
                    fotoInput.files = dataTransfer.files;
                    handlePhotoFile(files[0]);
                }
            });
        }

        // Initialize state on load
        if (kategoriInput && kategoriInput.value) {
            window.selectKategori(kategoriInput.value, !userModifiedDampak && !userModifiedUrgensi);
        }
        updateUraianCounter();
    })();
</script>
@endsection
