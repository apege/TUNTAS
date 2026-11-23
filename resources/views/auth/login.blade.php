@extends('layouts.app')

@section('content')
<main class="min-h-[100dvh] w-full flex items-center justify-center p-3 sm:p-4 lg:py-6 lg:px-8">
    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-7 items-stretch">
        
        <!-- ================= LEFT PANEL: GREEN CLAY BANNER / MOBILE HERO ================= -->
        <section class="lg:col-span-6 clay-panel-green text-white p-6 sm:p-7 lg:p-8 flex flex-col justify-between relative overflow-hidden min-h-[calc(100dvh-1.5rem)] sm:min-h-[calc(100dvh-2rem)] lg:min-h-0">
            
            <!-- Ambient Glow Overlay -->
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-[#34D399]/10 blur-3xl pointer-events-none"></div>

            <!-- Top Header & Brand -->
            <div class="relative z-10 space-y-4 lg:space-y-5">
                <!-- Brand Badge -->
                <div class="flex items-center gap-3">
                    <div class="clay-badge-leaf w-11 h-11 flex items-center justify-center text-[#1B4332] shrink-0">
                        <!-- Minimalist Leaf Icon -->
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block font-extrabold text-lg sm:text-xl tracking-tight text-white leading-tight">TUNTAS</span>
                        <span class="block text-[11px] font-medium text-[#A7C8B4] tracking-wide">Lingkungan Lebih Baik</span>
                    </div>
                </div>

                <!-- Main Slogan & Subtitle -->
                <div class="space-y-2 pt-1">
                    <h1 class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold tracking-tight text-white leading-tight">
                        Lingkungan lebih baik,<br>
                        <span class="text-[#88D4A8]">kita tuntaskan bersama.</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-[#D0E4D8] leading-relaxed max-w-sm font-normal">
                        Satu ruang untuk warga, pengurus, dan petugas. Dari laporan pertama sampai masalah benar-benar tertangani.
                    </p>
                </div>
            </div>

            <!-- Bottom Graphic: Isometric Map & Progress Illustration -->
            <div class="relative z-10 mt-5 pt-2 flex-1 flex flex-col justify-center">
                <div class="w-full h-36 sm:h-40 lg:h-42 xl:h-44 relative clay-sunken-frame overflow-hidden flex items-center justify-center p-2.5">
                    
                    <!-- SVG Isometric Grid & Neighborhood Plot Map -->
                    <svg class="w-full h-full" viewBox="0 0 460 210" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Grid Lines -->
                        <g stroke="rgba(255,255,255,0.08)" stroke-width="1">
                            <line x1="-50" y1="50" x2="250" y2="230" />
                            <line x1="0" y1="20" x2="300" y2="200" />
                            <line x1="50" y1="-10" x2="350" y2="170" />
                            <line x1="100" y1="-40" x2="400" y2="140" />
                            <line x1="150" y1="-70" x2="450" y2="110" />
                            <line x1="200" y1="-100" x2="500" y2="80" />

                            <line x1="250" y1="-50" x2="-50" y2="130" />
                            <line x1="300" y1="-20" x2="0" y2="160" />
                            <line x1="350" y1="10" x2="50" y2="190" />
                            <line x1="400" y1="40" x2="100" y2="220" />
                            <line x1="450" y1="70" x2="150" y2="250" />
                        </g>

                        <!-- Isometric Parcels / Territory Blocks -->
                        <!-- Parcel 1: Top Left -->
                        <polygon points="120,60 170,85 130,105 80,80" fill="rgba(64, 145, 108, 0.25)" stroke="rgba(255, 255, 255, 0.2)" stroke-width="1.2" />
                        <!-- Parcel 2: Center Top -->
                        <polygon points="210,45 260,70 220,90 170,65" fill="rgba(82, 183, 136, 0.22)" stroke="rgba(255, 255, 255, 0.22)" stroke-width="1.2" />
                        <!-- Parcel 3: Center Bottom -->
                        <polygon points="175,100 235,130 195,152 135,122" fill="rgba(45, 106, 79, 0.3)" stroke="rgba(255, 255, 255, 0.18)" stroke-width="1.2" />
                        <!-- Parcel 4: Bottom Right -->
                        <polygon points="280,110 340,140 300,162 240,132" fill="rgba(64, 145, 108, 0.28)" stroke="rgba(255, 255, 255, 0.2)" stroke-width="1.2" />
                        <!-- Parcel 5: Far Right -->
                        <polygon points="320,65 370,90 330,110 280,85" fill="rgba(82, 183, 136, 0.2)" stroke="rgba(255, 255, 255, 0.15)" stroke-width="1.2" />

                        <!-- Main Primary Road Highway (Double lines) -->
                        <path d="M-20,135 Q90,130 140,88 T240,75 T350,115 T480,100" stroke="rgba(255,255,255,0.18)" stroke-width="26" stroke-linecap="round" fill="none" />
                        <path d="M-20,135 Q90,130 140,88 T240,75 T350,115 T480,100" stroke="#143425" stroke-width="22" stroke-linecap="round" fill="none" />

                        <!-- Connecting Roadway -->
                        <path d="M140,88 L230,145 L380,125" stroke="rgba(255,255,255,0.15)" stroke-width="14" stroke-linecap="round" fill="none" />
                        <path d="M140,88 L230,145 L380,125" stroke="#163828" stroke-width="11" stroke-linecap="round" fill="none" />

                        <!-- Center Road Dashes -->
                        <path d="M-20,135 Q90,130 140,88 T240,75 T350,115 T480,100" stroke="rgba(255,255,255,0.35)" stroke-width="1.5" stroke-dasharray="5 5" fill="none" />
                        <path d="M140,88 L230,145 L380,125" stroke="rgba(255,255,255,0.25)" stroke-width="1.2" stroke-dasharray="4 4" fill="none" />

                        <!-- Progress / Route Track (White Dashed Journey Line) -->
                        <path d="M75,148 Q105,115 140,115 T215,90 T275,108" stroke="#EAF4EE" stroke-width="2.5" stroke-dasharray="4 4" stroke-linecap="round" fill="none" />

                        <!-- Start Point Node (Initial Report) -->
                        <circle cx="75" cy="148" r="6" fill="#F4F9F5" stroke="#1E4433" stroke-width="2" />
                        <circle cx="75" cy="148" r="2.5" fill="#2D6A4F" />

                        <!-- Mid Point Node -->
                        <circle cx="140" cy="115" r="4.5" fill="#E2F0E7" stroke="#1E4433" stroke-width="1.5" />

                        <!-- Destination Node: TUNTAS / Resolved Checkmark Badge -->
                        <g transform="translate(275, 108)">
                            <!-- Soft Pulse Glow -->
                            <circle cx="0" cy="0" r="16" fill="rgba(82, 183, 136, 0.25)" />
                            <!-- Clay Button Node -->
                            <circle cx="0" cy="0" r="12" fill="#EAF2EB" stroke="#FFFFFF" stroke-width="1.5" filter="drop-shadow(0 2px 4px rgba(0,0,0,0.3))" />
                            <!-- Checkmark Icon -->
                            <path d="M-4.5,0.5 L-1.5,3.5 L4.5,-2.5" stroke="#1B4332" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                    </svg>
                </div>
            </div>

            <!-- Mobile Only: Bottom CTA Button to trigger Slide-Up Login Sheet -->
            <div class="lg:hidden mt-6 pt-1">
                <button
                    type="button"
                    onclick="openLoginDrawer()"
                    class="clay-btn-mobile w-full py-4 px-6 font-extrabold text-[#153828] text-base flex items-center justify-center gap-2.5 shadow-lg active:scale-[0.99] transition-transform cursor-pointer"
                >
                    <span>Masuk ke TUNTAS</span>
                    <svg class="w-5 h-5 text-[#245640]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </div>

        </section>

        <!-- ================= MOBILE BACKDROP OVERLAY ================= -->
        <div 
            id="drawerBackdrop" 
            onclick="closeLoginDrawer()" 
            class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-40 transition-opacity duration-300 opacity-0 pointer-events-none"
            aria-hidden="true"
        ></div>

        <!-- ================= RIGHT PANEL: CLAYMORPHIC LOGIN CARD / MOBILE BOTTOM SHEET ================= -->
        <section 
            id="loginDrawer"
            class="fixed inset-x-0 bottom-0 z-50 lg:static lg:z-auto lg:col-span-6 flex flex-col justify-center transition-transform duration-300 ease-out transform translate-y-full lg:translate-y-0"
        >
            <div id="drawerCard" class="clay-card p-4 sm:p-6 lg:p-8 relative rounded-t-[28px] sm:rounded-t-[32px] rounded-b-none lg:rounded-[32px] max-h-[90dvh] overflow-y-auto overscroll-contain pb-6 sm:pb-8">
                
                <!-- Mobile Handle Bar & Close Button -->
                <div class="lg:hidden relative flex items-center justify-center pt-0.5 pb-2.5 -mt-0.5" id="drawerHeader">
                    <div class="w-12 h-1.5 bg-[#CFC8BA] rounded-full"></div>
                    <button 
                        type="button" 
                        onclick="closeLoginDrawer()" 
                        class="absolute right-0 top-0 w-8 h-8 rounded-full clay-pill flex items-center justify-center text-[#55695F] hover:text-[#183B2C] text-xs font-bold shrink-0"
                        aria-label="Tutup form login"
                    >
                        ✕
                    </button>
                </div>

                <!-- Section Subtitle & Header -->
                <div class="mb-3 sm:mb-5">
                    <span class="inline-block text-[10px] sm:text-[11px] font-bold tracking-widest text-[#2D6A4F] uppercase mb-0.5 sm:mb-1">
                        AKSES AKUN
                    </span>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#122A1E] tracking-tight">
                        Masuk ke TUNTAS
                    </h2>
                    <p class="text-xs sm:text-sm text-[#54685E] mt-0.5 sm:mt-1 leading-normal">
                        Gunakan akun Anda untuk membuka ruang kerja sesuai peran.
                    </p>
                </div>

                <!-- Alert Messages -->
                @if (session('status'))
                    <div class="mb-3 sm:mb-4 p-2.5 sm:p-3 rounded-2xl bg-[#E8F5E9] text-[#1B4332] text-xs font-medium border border-[#C8E6C9] flex items-center gap-2.5 shadow-sm">
                        <svg class="w-4 h-4 shrink-0 text-[#2E7D32]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->has('login_failed') || $errors->has('email') || $errors->has('password'))
                    <div class="mb-3 sm:mb-4 p-2.5 sm:p-3 rounded-2xl bg-[#FDE8E8] text-[#9B1C1C] text-xs font-medium border border-[#F8B4B4] flex items-start gap-2.5 shadow-sm" role="alert">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-[#E02424]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div>
                            @if ($errors->has('login_failed'))
                                <span>{{ $errors->first('login_failed') }}</span>
                            @else
                                <ul class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.store') }}" method="POST" class="space-y-2.5 sm:space-y-4" novalidate>
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-1">
                        <label for="email" class="block text-xs sm:text-sm font-semibold text-[#1F372B]">
                            Alamat email
                        </label>
                        <div class="relative">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@domain.id"
                                required
                                autocomplete="email"
                                class="clay-input w-full px-3.5 py-2 sm:py-2.5 lg:py-3 text-sm text-[#122A1E] placeholder:text-[#8D9B93] focus:ring-0"
                            />
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs sm:text-sm font-semibold text-[#1F372B]">
                                Kata sandi
                            </label>
                        </div>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                required
                                autocomplete="current-password"
                                class="clay-input w-full pl-3.5 pr-24 py-2 sm:py-2.5 lg:py-3 text-sm text-[#122A1E] placeholder:text-[#8D9B93] focus:ring-0"
                            />
                            <!-- Toggle Show/Hide Password -->
                            <button
                                type="button"
                                id="togglePasswordBtn"
                                onclick="togglePasswordVisibility()"
                                class="clay-btn-toggle absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-[#245640] px-2.5 py-1"
                            >
                                Tampilkan
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                id="remember" 
                                {{ old('remember') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-[#C4C0B6] text-[#245640] focus:ring-[#245640]/30 accent-[#245640]"
                            >
                            <span class="text-xs text-[#52645B] font-medium">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-1">
                        <button
                            type="submit"
                            class="clay-btn-green w-full py-2.5 sm:py-3.5 px-6 font-bold text-white text-sm sm:text-base flex items-center justify-center gap-2 cursor-pointer shadow-md"
                        >
                            <span>Masuk</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Divider / Info Box -->
                <div class="mt-3.5 pt-3 sm:mt-5 sm:pt-4 border-t border-[#DFDBD0] flex items-start gap-2.5 text-xs text-[#52645B]">
                    <div class="clay-pill w-6 h-6 sm:w-7 sm:h-7 rounded-lg flex items-center justify-center shrink-0 text-[#2D6A4F] mt-0.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <path d="M3 9h18"/>
                            <path d="M9 21V9"/>
                        </svg>
                    </div>
                    <div class="space-y-0.5">
                        <p class="font-bold text-[#1F372B]">Belum memiliki akses?</p>
                        <p class="leading-relaxed text-[11px] sm:text-xs">Hubungi pengurus RT atau RW untuk informasi akun Anda.</p>
                    </div>
                </div>

            </div>
        </section>

    </div>
</main>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePasswordBtn');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleBtn.textContent = 'Sembunyikan';
        } else {
            passwordInput.type = 'password';
            toggleBtn.textContent = 'Tampilkan';
        }
    }

    function openLoginDrawer() {
        const drawer = document.getElementById('loginDrawer');
        const backdrop = document.getElementById('drawerBackdrop');
        if (!drawer || !backdrop) return;

        drawer.classList.remove('translate-y-full');
        drawer.classList.add('translate-y-0');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100', 'pointer-events-auto');

        // Prevent body scroll when drawer is open on mobile
        document.body.style.overflow = 'hidden';

        // Hanya auto-focus pada desktop agar keyboard ponsel tidak langsung menutupi kartu
        if (window.innerWidth >= 1024) {
            setTimeout(() => {
                const emailInput = document.getElementById('email');
                if (emailInput) emailInput.focus();
            }, 320);
        }
    }

    function closeLoginDrawer() {
        const drawer = document.getElementById('loginDrawer');
        const backdrop = document.getElementById('drawerBackdrop');
        if (!drawer || !backdrop) return;

        // Blur any focused input to collapse virtual keyboard
        if (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'BUTTON')) {
            document.activeElement.blur();
        }

        drawer.classList.add('translate-y-full');
        drawer.classList.remove('translate-y-0');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        backdrop.classList.remove('opacity-100', 'pointer-events-auto');

        document.body.style.overflow = '';
    }

    // Adaptasi dinamis saat virtual keyboard aktif di ponsel
    (function handleVirtualKeyboard() {
        const drawerCard = document.getElementById('drawerCard');
        if (!drawerCard) return;

        // Auto-scroll input ke tengah layar saat difokuskan di perangkat bergerak
        const inputs = drawerCard.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                if (window.innerWidth < 1024) {
                    setTimeout(() => {
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 200);
                }
            });
        });

        // Dukungan visualViewport untuk iOS Safari & Android Chrome
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                if (window.innerWidth < 1024) {
                    const vpHeight = window.visualViewport.height;
                    const screenHeight = window.innerHeight;
                    
                    // Jika tinggi visual viewport mengecil lebih dari 120px, tandanya keyboard terbuka
                    if (screenHeight - vpHeight > 120) {
                        drawerCard.style.maxHeight = (vpHeight - 16) + 'px';
                        if (document.activeElement && document.activeElement.tagName === 'INPUT') {
                            setTimeout(() => {
                                document.activeElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 100);
                        }
                    } else {
                        drawerCard.style.maxHeight = '';
                    }
                }
            });
        }
    })();

    // Touch Swipe down to dismiss gesture on mobile
    (function initTouchGesture() {
        const drawerHeader = document.getElementById('drawerHeader');
        if (!drawerHeader) return;

        let startY = 0;
        let currentY = 0;

        drawerHeader.addEventListener('touchstart', (e) => {
            startY = e.touches[0].clientY;
        }, { passive: true });

        drawerHeader.addEventListener('touchmove', (e) => {
            currentY = e.touches[0].clientY;
        }, { passive: true });

        drawerHeader.addEventListener('touchend', () => {
            if (currentY - startY > 60) {
                closeLoginDrawer();
            }
            startY = 0;
            currentY = 0;
        });
    })();

    // Auto-open drawer on mobile if there are errors or old input
    @if ($errors->any() || old('email'))
        if (window.innerWidth < 1024) {
            document.addEventListener('DOMContentLoaded', () => {
                openLoginDrawer();
            });
        }
    @endif
</script>
@endsection
