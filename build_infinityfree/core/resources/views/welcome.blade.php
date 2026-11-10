<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('BisnisKu - Point of Sale and Management Platform') }} - {{ $globalShopName }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--color-canvas, #ffffff);
            color: var(--color-ink, #1C2B33);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[var(--color-canvas)] text-[var(--color-ink)] min-h-screen flex flex-col transition-colors duration-300">

    <!-- Navigation -->
    <header class="sticky top-0 z-50 w-full bg-[var(--color-canvas)]/90 backdrop-blur-md border-b border-[var(--color-hairline-soft)] px-4 sm:px-6 md:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <!-- Logo SVG -->
            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[var(--color-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
            <span class="type-subtitle-lg font-bold truncate max-w-[130px] sm:max-w-none">{{ $globalShopName }}</span>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-4">
            <form action="{{ route('locale.switch') }}" method="POST" class="flex items-center bg-[var(--color-surface-soft)] rounded-full p-0.5 sm:p-1 border border-[var(--color-hairline-soft)]">
                @csrf
                <button type="submit" name="locale" value="en" class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">EN</button>
                <button type="submit" name="locale" value="id" class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold transition-colors {{ app()->getLocale() === 'id' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">ID</button>
            </form>
            <button @click="theme = theme === 'light' ? 'dark' : 'light'" class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-ink)] hover:bg-[var(--color-hairline-soft)] transition-all">
                <svg x-show="theme === 'light'" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-cloak x-show="theme === 'dark'" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
            @if (Route::has('login'))
                @auth
                    <!-- Dashboard button removed -->
                @else
                    <a href="{{ route('login') }}" class="type-button text-xs sm:text-sm text-[var(--color-ink-deep)] hover:underline">{{ __('Log in') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary text-xs sm:text-sm py-1.5 px-3 sm:py-2 sm:px-4 hidden sm:inline-flex">{{ __('Sign up') }}</a>
                    @endif
                @endauth
            @endif
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="max-w-[1280px] mx-auto px-4 sm:px-6 md:px-8 flex flex-col items-center text-center mt-8 sm:mt-12 md:mt-16 mb-8 sm:mb-12 md:mb-16">
            <h1 class="text-3xl sm:text-4xl md:type-hero font-bold text-[var(--color-ink-deep)] max-w-4xl mb-4 sm:mb-6 leading-tight sm:leading-tight">{{ __('Manage your business with confidence.') }}</h1>
            <p class="text-sm sm:text-base md:type-subtitle-md text-[var(--color-charcoal)] max-w-2xl mb-6 sm:mb-8 px-2">
                {{ $globalShopName }} {{ __('is the all-in-one Point of Sale and management platform. Designed for speed, built for growth, and powered by smart algorithms.') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto px-4 sm:px-0">
                <a href="{{ route('register') ?? '#' }}" class="btn-primary w-full sm:w-auto text-center justify-center py-3">{{ __('Get Started') }}</a>
            </div>
            
            <!-- Alpine.js Carousel for Hero -->
            <div x-data="{ 
                activeSlide: 1, 
                slides: [1, 2, 3],
                timer: null,
                startTimer() {
                    this.timer = setInterval(() => {
                        this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1;
                    }, 4000);
                },
                stopTimer() {
                    clearInterval(this.timer);
                }
            }" 
            x-init="startTimer()"
            @mouseenter="stopTimer()"
            @mouseleave="startTimer()"
            class="mt-8 sm:mt-12 md:mt-16 w-full max-w-5xl aspect-[4/3] sm:aspect-video bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] sm:rounded-[var(--radius-xxxl)] overflow-hidden relative border border-[var(--color-hairline-soft)] shadow-md">
                
                <!-- Slide 1: Dashboard UI -->
                <div x-show="activeSlide === 1" x-transition.opacity.duration.500ms class="absolute inset-0 bg-gradient-to-br from-[#FDFDFC] to-[#E4E6EB] p-4 sm:p-8 md:p-12 flex flex-col gap-3 sm:gap-6">
                    <div class="w-full h-12 sm:h-16 bg-[var(--color-canvas)] rounded-xl sm:rounded-2xl shadow-sm flex items-center px-4 sm:px-6 gap-3 sm:gap-6">
                        <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-[var(--color-primary-soft)] flex-shrink-0"></div>
                        <div class="w-28 sm:w-48 h-4 sm:h-5 rounded-md bg-[var(--color-surface-soft)]"></div>
                        <div class="flex-grow"></div>
                        <div class="w-20 sm:w-32 h-8 sm:h-10 rounded-[var(--radius-full)] bg-[var(--color-ink-button)]"></div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-6 flex-grow overflow-hidden">
                        <div class="hidden sm:flex w-1/4 h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm p-6 flex-col gap-4">
                            <div class="w-full h-10 rounded-[var(--radius-lg)] bg-[var(--color-surface-soft)]"></div>
                            <div class="w-full h-10 rounded-[var(--radius-lg)] bg-[var(--color-surface-soft)]"></div>
                            <div class="w-full h-10 rounded-[var(--radius-lg)] bg-[var(--color-surface-soft)]"></div>
                        </div>
                        <div class="w-full sm:w-3/4 h-full bg-[var(--color-canvas)] rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-8 flex flex-col gap-4 sm:gap-6">
                            <div class="flex gap-2 sm:gap-4">
                                <div class="flex-1 h-20 sm:h-32 rounded-lg sm:rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]"></div>
                                <div class="flex-1 h-20 sm:h-32 rounded-lg sm:rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]"></div>
                                <div class="flex-1 h-20 sm:h-32 rounded-lg sm:rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]"></div>
                            </div>
                            <div class="w-full flex-grow rounded-lg sm:rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)] flex items-end justify-between p-4 sm:p-8">
                                <div class="w-6 sm:w-12 h-1/3 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-6 sm:w-12 h-2/3 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-6 sm:w-12 h-1/2 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-6 sm:w-12 h-full bg-[var(--color-primary)] rounded-t-lg shadow-[var(--shadow-md)]"></div>
                                <div class="w-6 sm:w-12 h-4/5 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-6 sm:w-12 h-3/4 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: POS / Checkout UI -->
                <div x-cloak x-show="activeSlide === 2" x-transition.opacity.duration.500ms class="absolute inset-0 bg-[var(--color-canvas)] p-4 sm:p-8 md:p-12 flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <!-- Products Grid -->
                    <div class="w-full sm:w-2/3 h-2/3 sm:h-full flex flex-col gap-3 sm:gap-6">
                        <div class="w-full h-12 sm:h-16 bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] flex items-center px-4 sm:px-6">
                            <div class="w-1/3 h-5 sm:h-6 bg-[var(--color-canvas)] rounded-md"></div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-4 flex-grow">
                            <div class="bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] p-3 sm:p-4 flex flex-col justify-end gap-2"><div class="w-full h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] p-3 sm:p-4 flex flex-col justify-end gap-2"><div class="w-full h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] p-3 sm:p-4 flex flex-col justify-end gap-2"><div class="w-full h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] p-3 sm:p-4 flex flex-col justify-end gap-2"><div class="w-full h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-3 sm:h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="hidden sm:flex bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="hidden sm:flex bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                        </div>
                    </div>
                    <!-- Receipt/Checkout Panel -->
                    <div class="w-full sm:w-1/3 h-1/3 sm:h-full bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] p-4 sm:p-6 flex flex-col justify-between">
                        <div>
                            <div class="w-1/2 h-4 sm:h-6 bg-[var(--color-canvas)] rounded-md mb-3 sm:mb-6"></div>
                            <div class="flex flex-col gap-2 sm:gap-4">
                                <div class="w-full h-8 sm:h-12 bg-[var(--color-canvas)] rounded-lg flex items-center px-3 sm:px-4 justify-between"><div class="w-1/2 h-3 sm:h-4 bg-[var(--color-surface-soft)] rounded"></div><div class="w-1/4 h-3 sm:h-4 bg-[var(--color-surface-soft)] rounded"></div></div>
                                <div class="hidden sm:flex w-full h-12 bg-[var(--color-canvas)] rounded-lg items-center px-4 justify-between"><div class="w-1/2 h-4 bg-[var(--color-surface-soft)] rounded"></div><div class="w-1/4 h-4 bg-[var(--color-surface-soft)] rounded"></div></div>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 sm:mt-4 sm:pt-4 border-t border-[var(--color-hairline-soft)] flex flex-col gap-2 sm:gap-4">
                            <div class="w-full h-10 sm:h-16 bg-[var(--color-primary)] rounded-[var(--radius-full)]"></div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Live Catalog UI -->
                <div x-cloak x-show="activeSlide === 3" x-transition.opacity.duration.500ms class="absolute inset-0 bg-[#0A131A] p-4 sm:p-8 md:p-12 flex flex-col items-center justify-center">
                    <div class="w-full sm:w-3/4 h-5/6 bg-[var(--color-canvas)] rounded-xl sm:rounded-[var(--radius-xxxl)] overflow-hidden flex flex-col shadow-2xl">
                        <div class="h-12 sm:h-16 border-b border-[var(--color-hairline-soft)] flex items-center px-4 sm:px-8 gap-3 sm:gap-4">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-[var(--color-primary)]"></div>
                            <div class="w-24 sm:w-32 h-5 sm:h-6 bg-[var(--color-surface-soft)] rounded"></div>
                        </div>
                        <div class="p-4 sm:p-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-6 flex-grow">
                            <div class="bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-lg sm:rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                            <div class="hidden sm:block bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                            <div class="hidden sm:block bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                        </div>
                    </div>
                </div>

                <!-- Carousel Controls -->
                <div class="absolute bottom-3 sm:bottom-6 left-0 right-0 flex justify-center gap-2 sm:gap-3">
                    <template x-for="slide in slides">
                        <button @click="activeSlide = slide" 
                                :class="{'w-6 sm:w-8 bg-[var(--color-primary)]': activeSlide === slide, 'w-2 sm:w-3 bg-gray-400': activeSlide !== slide}" 
                                class="h-2 sm:h-3 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
        </section>

        <!-- Features Section with Documentation Tabs -->
        <section id="features" class="max-w-[1280px] mx-auto px-4 sm:px-6 md:px-8 mb-12 sm:mb-16" x-data="{ activeFeature: 'pos' }">
            <h2 class="text-2xl sm:text-3xl md:type-display text-[var(--color-ink-deep)] text-center mb-8 sm:mb-12 md:mb-16">{{ __('Built for modern retail.') }}</h2>
            
            <div class="flex flex-col lg:flex-row gap-6 sm:gap-12">
                <!-- Feature List -->
                <div class="flex flex-col gap-3 sm:gap-4 lg:w-1/3">
                    <!-- Feature 1: POS & Inventory -->
                    <button @click="activeFeature = 'pos'" :class="{'ring-2 ring-[var(--color-primary)] bg-[var(--color-surface-soft)]': activeFeature === 'pos', 'hover:bg-[var(--color-surface-soft)]': activeFeature !== 'pos'}" class="text-left card p-4 sm:p-6 flex flex-col gap-2 sm:gap-3 transition-all duration-300">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary-deep)] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ __('Smart POS & Inventory') }}</h3>
                        <p class="text-xs sm:text-sm text-[var(--color-slate)]">{{ __('Manage sales, purchases, and stock movements in real-time easily.') }}</p>
                    </button>
                    
                    <!-- Feature 2: AI & BI -->
                    <button @click="activeFeature = 'bi'" :class="{'ring-2 ring-[var(--color-primary)] bg-[var(--color-surface-soft)]': activeFeature === 'bi', 'hover:bg-[var(--color-surface-soft)]': activeFeature !== 'bi'}" class="text-left card p-4 sm:p-6 flex flex-col gap-2 sm:gap-3 transition-all duration-300">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary-deep)] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ __('Reports & Business Intelligence') }}</h3>
                        <p class="text-xs sm:text-sm text-[var(--color-slate)]">{{ __('Generate comprehensive business performance reports and sales trend analysis automatically.') }}</p>
                    </button>

                    <!-- Feature 3: Service & Debt -->
                    <button @click="activeFeature = 'service'" :class="{'ring-2 ring-[var(--color-primary)] bg-[var(--color-surface-soft)]': activeFeature === 'service', 'hover:bg-[var(--color-surface-soft)]': activeFeature !== 'service'}" class="text-left card p-4 sm:p-6 flex flex-col gap-2 sm:gap-3 transition-all duration-300">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary-deep)] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ __('Service & Debt Management') }}</h3>
                        <p class="text-xs sm:text-sm text-[var(--color-slate)]">{{ __('Structured tracking of phone repair status and customer debt/installment systems.') }}</p>
                    </button>

                    <!-- Feature 4: Integrations -->
                    <button @click="activeFeature = 'integrations'" :class="{'ring-2 ring-[var(--color-primary)] bg-[var(--color-surface-soft)]': activeFeature === 'integrations', 'hover:bg-[var(--color-surface-soft)]': activeFeature !== 'integrations'}" class="text-left card p-4 sm:p-6 flex flex-col gap-2 sm:gap-3 transition-all duration-300">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary-deep)] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ __('QRIS & WhatsApp Integration') }}</h3>
                        <p class="text-xs sm:text-sm text-[var(--color-slate)]">{{ __('Accept payments via QRIS DANA and send invoice notifications via WhatsApp API.') }}</p>
                    </button>
                </div>

                <!-- Documentation Image Viewport -->
                <div class="lg:w-2/3 min-h-[280px] sm:min-h-[350px] md:min-h-[400px] bg-[var(--color-surface-soft)] rounded-2xl sm:rounded-[var(--radius-xxxl)] border border-[var(--color-hairline-soft)] relative overflow-hidden flex items-center justify-center">
                    
                    <!-- POS Doc Image -->
                    <div x-cloak x-show="activeFeature === 'pos'" x-transition.opacity class="absolute inset-0 p-4 sm:p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-xl sm:rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col sm:flex-row overflow-hidden">
                            <div class="w-full sm:w-2/3 p-4 sm:p-6 border-b sm:border-b-0 sm:border-r border-[var(--color-hairline-soft)] flex flex-col gap-3 sm:gap-4">
                                <div class="h-6 sm:h-8 bg-[var(--color-surface-soft)] w-1/3 rounded"></div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-4">
                                    <div class="h-16 sm:h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="h-16 sm:h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="hidden sm:block h-24 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                    <div class="h-16 sm:h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="h-16 sm:h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="hidden sm:block h-24 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                </div>
                            </div>
                            <div class="w-full sm:w-1/3 p-4 sm:p-6 flex flex-col justify-between">
                                <div class="flex flex-col gap-2 sm:gap-4">
                                    <div class="h-8 sm:h-10 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                    <div class="h-8 sm:h-10 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                </div>
                                <div class="h-10 sm:h-12 bg-[var(--color-success)] rounded-full mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- BI Doc Image -->
                    <div x-cloak x-show="activeFeature === 'bi'" x-transition.opacity class="absolute inset-0 p-4 sm:p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-xl sm:rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col p-4 sm:p-6 gap-4 sm:gap-6">
                            <div class="h-6 sm:h-8 bg-[var(--color-surface-soft)] w-1/4 rounded"></div>
                            <div class="flex-grow flex items-end justify-around pb-2 sm:pb-4">
                                <div class="w-10 sm:w-16 h-1/4 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-10 sm:w-16 h-1/2 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-10 sm:w-16 h-full bg-[var(--color-primary)] rounded-t-lg relative">
                                    <div class="absolute -top-7 sm:-top-8 left-1/2 -translate-x-1/2 bg-[var(--color-ink-deep)] text-white text-[10px] sm:text-xs px-2 py-0.5 sm:py-1 rounded">Top!</div>
                                </div>
                                <div class="w-10 sm:w-16 h-3/4 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Service & Debt Doc Image -->
                    <div x-cloak x-show="activeFeature === 'service'" x-transition.opacity class="absolute inset-0 p-4 sm:p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-xl sm:rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col p-4 sm:p-6 gap-3 sm:gap-4 justify-center">
                            <div class="h-6 sm:h-8 bg-[var(--color-surface-soft)] w-1/3 rounded mb-2 sm:mb-4"></div>
                            <div class="w-full h-10 sm:h-12 border border-[var(--color-hairline-strong)] rounded-lg flex items-center px-3 sm:px-4 justify-between">
                                <div class="flex gap-2 items-center"><div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-[var(--color-primary)]"></div> <div class="w-20 sm:w-24 h-3 sm:h-4 bg-[var(--color-surface-soft)] rounded"></div></div>
                                <div class="w-12 sm:w-16 h-3 sm:h-4 bg-[var(--color-surface-soft)] rounded"></div>
                            </div>
                            <div class="w-full h-10 sm:h-12 border border-[var(--color-hairline-strong)] rounded-lg flex items-center px-3 sm:px-4 justify-between">
                                <div class="flex gap-2 items-center"><div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-orange-400"></div> <div class="w-20 sm:w-24 h-3 sm:h-4 bg-[var(--color-surface-soft)] rounded"></div></div>
                                <div class="w-12 sm:w-16 h-3 sm:h-4 bg-[var(--color-surface-soft)] rounded"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Integrations Doc Image -->
                    <div x-cloak x-show="activeFeature === 'integrations'" x-transition.opacity class="absolute inset-0 p-4 sm:p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-xl sm:rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col items-center justify-center gap-4 sm:gap-6">
                            <div class="w-24 h-24 sm:w-32 sm:h-32 bg-[var(--color-surface-soft)] rounded-lg border-2 border-dashed border-[var(--color-hairline-strong)] flex items-center justify-center">
                                <span class="text-xs sm:text-sm text-[var(--color-stone)]">QR Code</span>
                            </div>
                            <div class="w-40 sm:w-48 h-7 sm:h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold">
                                WhatsApp Notification Sent
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <!-- Live Product Catalog Callout -->
        <section class="max-w-[1280px] mx-auto px-4 sm:px-6 md:px-8 mb-16 sm:mb-24 md:mb-[120px]">
            <div class="card bg-[var(--color-surface-soft)] p-6 sm:p-12 md:p-[64px] border-none text-center rounded-2xl sm:rounded-[var(--radius-xxxl)]">
                <h2 class="text-2xl sm:text-3xl md:type-display text-[var(--color-ink-deep)] mb-4 sm:mb-6">{{ __('Integrated Live Catalog') }}</h2>
                <p class="text-sm sm:text-base md:type-subtitle-md text-[var(--color-charcoal)] max-w-2xl mx-auto mb-6 sm:mb-8">
                    {{ __('Let customers browse your products directly. The catalog updates in real-time and highlights top-selling products from your store.') }}
                </p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-canvas)] border-t border-[var(--color-hairline-soft)] py-6 px-4 sm:py-8 sm:px-8 mt-auto">
        <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row justify-between items-center gap-3 sm:gap-4 text-center md:text-left">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
                <span class="type-body-sm-bold text-[var(--color-ink-deep)]">{{ $globalShopName }}</span>
            </div>
            <span class="type-caption text-[var(--color-stone)]">&copy; {{ date('Y') }} {{ $globalShopName }}. All rights reserved.</span>
        </div>
    </footer>
</body>
</html>
