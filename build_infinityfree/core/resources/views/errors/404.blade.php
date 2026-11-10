<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - {{ __('Halaman Tidak Ditemukan') }} - {{ config('app.name', 'BisnisKu') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="h-full bg-[var(--color-canvas)] transition-colors duration-300">

<!-- Top Right Theme & Language Toggle -->
<div class="fixed top-6 right-6 z-50 flex items-center gap-3">
    <form action="{{ route('locale.switch') }}" method="POST" class="flex items-center bg-[var(--color-surface-soft)] rounded-full p-1 border border-[var(--color-hairline-soft)] shadow-sm">
        @csrf
        <button type="submit" name="locale" value="en" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">EN</button>
        <button type="submit" name="locale" value="id" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'id' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">ID</button>
    </form>
    <button @click="theme = theme === 'light' ? 'dark' : 'light'" class="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-ink)] hover:bg-[var(--color-hairline-soft)] transition-all shadow-sm border border-[var(--color-hairline-soft)]">
        <svg x-show="theme === 'light'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg x-cloak x-show="theme === 'dark'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>
</div>

<div class="flex min-h-screen items-center justify-center p-6">
    <div class="text-center max-w-lg">
        <!-- 404 Illustration Icon -->
        <div class="mx-auto mb-8 flex h-28 w-28 items-center justify-center rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary)] shadow-sm border border-[var(--color-hairline-soft)]">
            <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-7xl font-bold text-[var(--color-ink-deep)] mb-4 tracking-tight">404</h1>
        <h2 class="text-2xl sm:text-3xl font-bold text-[var(--color-ink-deep)] mb-4">{{ __('Halaman Tidak Ditemukan') }}</h2>
        <p class="text-[var(--color-slate)] mb-10 leading-relaxed text-sm sm:text-base">
            {{ __('Maaf, halaman yang Anda cari tidak tersedia, telah dipindahkan, atau alamat URL yang Anda masukkan salah.') }}
        </p>

        <!-- Navigation Buttons -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <button onclick="window.history.back()" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-[var(--color-hairline)] bg-[var(--color-surface)] text-[var(--color-ink)] font-semibold hover:bg-[var(--color-surface-soft)] transition-colors flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Kembali') }}
            </button>

            @auth
                <!-- Sudah ada sesi login: tombol ke Dashboard -->
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[var(--color-primary)] text-white font-semibold shadow-sm hover:bg-[var(--color-primary-deep)] transition-colors flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    {{ __('Ke Dashboard') }}
                </a>
            @else
                <!-- Belum ada sesi login: tombol ke Landing Page -->
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[var(--color-primary)] text-white font-semibold shadow-sm hover:bg-[var(--color-primary-deep)] transition-colors flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    {{ __('Ke Landing Page') }}
                </a>
            @endauth
        </div>
    </div>
</div>
</body>
</html>
