<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Point of Sale and Management Platform') }} - {{ $globalShopName }}</title>
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
    <header class="sticky top-0 z-50 w-full bg-[var(--color-canvas)]/90 backdrop-blur-md border-b border-[var(--color-hairline-soft)] px-[32px] h-[64px] flex items-center justify-between">
        <div class="flex items-center gap-2">
            <!-- Logo SVG -->
            <svg class="w-8 h-8 text-[var(--color-primary)]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
            <span class="type-subtitle-lg font-bold">{{ $globalShopName }}</span>
        </div>
        
        <nav class="hidden md:flex items-center gap-4">
            <a href="#features" class="pill-tab hover:bg-[var(--color-surface-soft)]">{{ __('Features') }}</a>
            <a href="{{ url('/catalog') }}" class="pill-tab hover:bg-[var(--color-surface-soft)]">{{ __('Live Catalog') }}</a>
            <a href="#about" class="pill-tab hover:bg-[var(--color-surface-soft)]">{{ __('About') }}</a>
        </nav>

        <div class="flex items-center gap-4">
            <form action="{{ route('locale.switch') }}" method="POST" class="flex items-center bg-[var(--color-surface-soft)] rounded-full p-1 border border-[var(--color-hairline-soft)]">
                @csrf
                <button type="submit" name="locale" value="en" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">EN</button>
                <button type="submit" name="locale" value="id" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'id' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">ID</button>
            </form>
            <button @click="theme = theme === 'light' ? 'dark' : 'light'" class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-ink)] hover:bg-[var(--color-hairline-soft)] transition-all">
                <svg x-show="theme === 'light'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-cloak x-show="theme === 'dark'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="type-button text-[var(--color-ink-deep)] hover:underline">{{ __('Log in') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">{{ __('Sign up') }}</a>
                    @endif
                @endauth
            @endif
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="max-w-[1280px] mx-auto section-gap flex flex-col items-center text-center mt-[64px] mb-[64px]">
            <h1 class="type-hero text-[var(--color-ink-deep)] max-w-4xl mb-6">{{ __('Manage your business with confidence.') }}</h1>
            <p class="type-subtitle-md text-[var(--color-charcoal)] max-w-2xl mb-8">
                {{ $globalShopName }} {{ __('is the all-in-one Point of Sale and management platform. Designed for speed, built for growth, and powered by smart algorithms.') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <a href="{{ route('register') ?? '#' }}" class="btn-primary">{{ __('Get Started') }}</a>
                <a href="#features" class="btn-secondary">{{ __('Explore Features') }}</a>
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
            class="mt-[80px] w-full max-w-5xl aspect-video bg-[var(--color-surface-soft)] rounded-[var(--radius-xxxl)] overflow-hidden relative border border-[var(--color-hairline-soft)] shadow-md">
                
                <!-- Slide 1: Dashboard UI -->
                <div x-show="activeSlide === 1" x-transition.opacity.duration.500ms class="absolute inset-0 bg-gradient-to-br from-[#FDFDFC] to-[#E4E6EB] p-8 md:p-12 flex flex-col gap-6">
                    <div class="w-full h-16 bg-[var(--color-canvas)] rounded-2xl shadow-sm flex items-center px-6 gap-6">
                        <div class="w-10 h-10 rounded-full bg-[var(--color-primary-soft)]"></div>
                        <div class="w-48 h-5 rounded-md bg-[var(--color-surface-soft)]"></div>
                        <div class="flex-grow"></div>
                        <div class="w-32 h-10 rounded-[var(--radius-full)] bg-[var(--color-ink-button)]"></div>
                    </div>
                    <div class="flex gap-6 flex-grow">
                        <div class="w-1/4 h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm p-6 flex flex-col gap-4">
                            <div class="w-full h-10 rounded-[var(--radius-lg)] bg-[var(--color-surface-soft)]"></div>
                            <div class="w-full h-10 rounded-[var(--radius-lg)] bg-[var(--color-surface-soft)]"></div>
                            <div class="w-full h-10 rounded-[var(--radius-lg)] bg-[var(--color-surface-soft)]"></div>
                        </div>
                        <div class="w-3/4 h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm p-8 flex flex-col gap-6">
                            <div class="flex gap-4">
                                <div class="flex-1 h-32 rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]"></div>
                                <div class="flex-1 h-32 rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]"></div>
                                <div class="flex-1 h-32 rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]"></div>
                            </div>
                            <div class="w-full flex-grow rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)] flex items-end justify-between p-8">
                                <div class="w-12 h-1/3 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-12 h-2/3 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-12 h-1/2 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-12 h-full bg-[var(--color-primary)] rounded-t-lg shadow-[var(--shadow-md)]"></div>
                                <div class="w-12 h-4/5 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-12 h-3/4 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: POS / Checkout UI -->
                <div x-cloak x-show="activeSlide === 2" x-transition.opacity.duration.500ms class="absolute inset-0 bg-[var(--color-canvas)] p-8 md:p-12 flex gap-6">
                    <!-- Products Grid -->
                    <div class="w-2/3 h-full flex flex-col gap-6">
                        <div class="w-full h-16 bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] flex items-center px-6">
                            <div class="w-1/3 h-6 bg-[var(--color-canvas)] rounded-md"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 flex-grow">
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-4 flex flex-col justify-end gap-2"><div class="w-full h-4 bg-[var(--color-canvas)] rounded-md"></div><div class="w-1/2 h-4 bg-[var(--color-canvas)] rounded-md"></div></div>
                        </div>
                    </div>
                    <!-- Receipt/Checkout Panel -->
                    <div class="w-1/3 h-full bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-6 flex flex-col">
                        <div class="w-1/2 h-6 bg-[var(--color-canvas)] rounded-md mb-6"></div>
                        <div class="flex-grow flex flex-col gap-4">
                            <div class="w-full h-12 bg-[var(--color-canvas)] rounded-lg flex items-center px-4 justify-between"><div class="w-1/2 h-4 bg-[var(--color-surface-soft)] rounded"></div><div class="w-1/4 h-4 bg-[var(--color-surface-soft)] rounded"></div></div>
                            <div class="w-full h-12 bg-[var(--color-canvas)] rounded-lg flex items-center px-4 justify-between"><div class="w-1/2 h-4 bg-[var(--color-surface-soft)] rounded"></div><div class="w-1/4 h-4 bg-[var(--color-surface-soft)] rounded"></div></div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-[var(--color-hairline-soft)] flex flex-col gap-4">
                            <div class="w-full h-16 bg-[var(--color-primary)] rounded-[var(--radius-full)]"></div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Live Catalog UI -->
                <div x-cloak x-show="activeSlide === 3" x-transition.opacity.duration.500ms class="absolute inset-0 bg-[#0A131A] p-8 md:p-12 flex flex-col items-center justify-center">
                    <div class="w-3/4 h-5/6 bg-[var(--color-canvas)] rounded-[var(--radius-xxxl)] overflow-hidden flex flex-col shadow-2xl transform scale-105">
                        <div class="h-16 border-b border-[var(--color-hairline-soft)] flex items-center px-8 gap-4">
                            <div class="w-8 h-8 rounded-full bg-[var(--color-primary)]"></div>
                            <div class="w-32 h-6 bg-[var(--color-surface-soft)] rounded"></div>
                        </div>
                        <div class="p-8 grid grid-cols-4 gap-6 flex-grow">
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] aspect-[3/4]"></div>
                        </div>
                    </div>
                </div>

                <!-- Carousel Controls -->
                <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-3">
                    <template x-for="slide in slides">
                        <button @click="activeSlide = slide" 
                                :class="{'w-8 bg-[var(--color-primary)]': activeSlide === slide, 'w-3 bg-gray-400': activeSlide !== slide}" 
                                class="h-3 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
        </section>

        <!-- Features Section with Documentation Tabs -->
        <section id="features" class="max-w-[1280px] mx-auto section-gap mb-[64px]" x-data="{ activeFeature: 'pos' }">
            <h2 class="type-display text-[var(--color-ink-deep)] text-center mb-[64px]">{{ __('Built for modern retail.') }}</h2>
            
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Feature List -->
                <div class="flex flex-col gap-4 lg:w-1/3">
                    <!-- Feature 1 -->
                    <button @click="activeFeature = 'pos'" :class="{'ring-2 ring-[var(--color-primary)] bg-[var(--color-surface-soft)]': activeFeature === 'pos', 'hover:bg-[var(--color-surface-soft)]': activeFeature !== 'pos'}" class="text-left card p-[24px] flex flex-col gap-3 transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary-deep)] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ __('Smart POS') }}</h3>
                        <p class="type-body-sm text-[var(--color-slate)]">{{ __('Process transactions instantly and manage your store efficiently.') }}</p>
                    </button>
                    
                    <!-- Feature 2 -->
                    <button @click="activeFeature = 'bi'" :class="{'ring-2 ring-[var(--color-primary)] bg-[var(--color-surface-soft)]': activeFeature === 'bi', 'hover:bg-[var(--color-surface-soft)]': activeFeature !== 'bi'}" class="text-left card p-[24px] flex flex-col gap-3 transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary-deep)] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ __('AI Business Intel') }}</h3>
                        <p class="type-body-sm text-[var(--color-slate)]">{{ __('K-Means clustering automatically identifies top selling products and predicts restocking needs.') }}</p>
                    </button>

                </div>

                <!-- Documentation Image Viewport -->
                <div class="lg:w-2/3 min-h-[400px] bg-[var(--color-surface-soft)] rounded-[var(--radius-xxxl)] border border-[var(--color-hairline-soft)] relative overflow-hidden flex items-center justify-center">
                    
                    <!-- POS Doc Image -->
                    <div x-cloak x-show="activeFeature === 'pos'" x-transition.opacity class="absolute inset-0 p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex overflow-hidden">
                            <div class="w-2/3 p-6 border-r border-[var(--color-hairline-soft)] flex flex-col gap-4">
                                <div class="h-8 bg-[var(--color-surface-soft)] w-1/3 rounded"></div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="h-24 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                    <div class="h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="h-24 bg-[var(--color-surface-soft)] rounded-lg"></div><div class="h-24 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                </div>
                            </div>
                            <div class="w-1/3 p-6 flex flex-col justify-between">
                                <div class="flex flex-col gap-4">
                                    <div class="h-10 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                    <div class="h-10 bg-[var(--color-surface-soft)] rounded-lg"></div>
                                </div>
                                <div class="h-12 bg-[var(--color-success)] rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- BI Doc Image -->
                    <div x-cloak x-show="activeFeature === 'bi'" x-transition.opacity class="absolute inset-0 p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col p-6 gap-6">
                            <div class="h-8 bg-[var(--color-surface-soft)] w-1/4 rounded"></div>
                            <div class="flex-grow flex items-end justify-around pb-4">
                                <div class="w-16 h-1/4 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-16 h-1/2 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                                <div class="w-16 h-full bg-[var(--color-primary)] rounded-t-lg relative">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[var(--color-ink-deep)] text-white text-xs px-2 py-1 rounded">Top!</div>
                                </div>
                                <div class="w-16 h-3/4 bg-[var(--color-primary-soft)] rounded-t-lg"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Debt Doc Image -->
                    <div x-cloak x-show="activeFeature === 'debt'" x-transition.opacity class="absolute inset-0 p-8 flex items-center justify-center">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col p-6 gap-4">
                            <div class="h-8 bg-[var(--color-surface-soft)] w-1/3 rounded mb-4"></div>
                            <div class="w-full h-12 border border-[var(--color-critical)] rounded-lg flex items-center px-4 justify-between">
                        <div class="w-full h-full bg-[var(--color-canvas)] rounded-2xl shadow-sm border border-[var(--color-hairline-soft)] flex flex-col overflow-hidden">
                            <div class="h-12 border-b border-[var(--color-hairline-soft)] flex items-center px-6 gap-4">
                                <div class="w-32 h-4 bg-[var(--color-surface-soft)] rounded"></div>
                            </div>
                            <div class="flex-grow p-6 flex flex-col gap-6">
                                <div class="w-full h-1/2 bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)]"></div>
                                <div class="flex gap-4 flex-grow">
                                    <div class="flex-1 bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)]"></div>
                                    <div class="flex-1 bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <!-- Live Product Catalog Callout -->
        <section class="max-w-[1280px] mx-auto section-gap mb-[120px]">
            <div class="card bg-[var(--color-surface-soft)] p-[64px] border-none text-center">
                <h2 class="type-display text-[var(--color-ink-deep)] mb-6">{{ __('Integrated Live Catalog') }}</h2>
                <p class="type-subtitle-md text-[var(--color-charcoal)] max-w-2xl mx-auto mb-8">
                    {{ __('Let your customers browse your products directly. The catalog updates in real-time and highlights top-selling products powered by our K-Means AI clustering engine.') }}
                </p>
                <div class="flex justify-center">
                    <a href="{{ url('/catalog') }}" class="btn-ghost bg-[var(--color-canvas)]">{{ __('View Live Catalog') }}</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-canvas)] border-t border-[var(--color-hairline-soft)] py-[64px] px-[32px] mt-auto">
        <div class="max-w-[1280px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-[var(--color-primary)]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <span class="type-body-sm-bold text-[var(--color-ink-deep)]">{{ $globalShopName }}</span>
                </div>
                <p class="type-caption text-[var(--color-slate)] max-w-xs">{{ __('The smartest POS and business management platform, optimized for retail growth.') }}</p>
            </div>
            <div>
                <h4 class="type-body-sm-bold text-[var(--color-ink)] mb-4">{{ __('Product') }}</h4>
                <ul class="flex flex-col gap-3">
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Features') }}</a></li>
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Pricing') }}</a></li>
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Integrations') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="type-body-sm-bold text-[var(--color-ink)] mb-4">{{ __('Resources') }}</h4>
                <ul class="flex flex-col gap-3">
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Documentation') }}</a></li>
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('API Reference') }}</a></li>
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Blog') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="type-body-sm-bold text-[var(--color-ink)] mb-4">{{ __('Company') }}</h4>
                <ul class="flex flex-col gap-3">
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('About') }}</a></li>
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Contact') }}</a></li>
                    <li><a href="#" class="type-caption text-[var(--color-steel)] hover:text-[var(--color-ink)] transition-colors">{{ __('Privacy Policy') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-[1280px] mx-auto border-t border-[var(--color-hairline-soft)] pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="type-caption text-[var(--color-stone)]">&copy; {{ date('Y') }} {{ $globalShopName }}. All rights reserved.</span>
            <div class="flex gap-4">
                <a href="#" class="type-caption text-[var(--color-stone)] hover:text-[var(--color-ink)] transition-colors">{{ __('Terms of Service') }}</a>
                <a href="#" class="type-caption text-[var(--color-stone)] hover:text-[var(--color-ink)] transition-colors">{{ __('Cookies') }}</a>
            </div>
        </div>
    </footer>
</body>
</html>
