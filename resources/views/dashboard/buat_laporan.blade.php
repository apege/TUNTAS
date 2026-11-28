@extends('layouts.warga')

@section('header-title', 'Buat Laporan')

@section('warga-content')
<div x-data="{ 
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
@endsection
