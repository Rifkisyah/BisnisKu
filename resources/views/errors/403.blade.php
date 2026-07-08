<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Akses Ditolak</title>
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
    </style>
</head>
<body class="h-full bg-[var(--color-canvas)] transition-colors duration-300">
<div class="flex min-h-screen items-center justify-center p-6">
    <div class="text-center max-w-lg">
        <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-[var(--color-critical-soft)] text-[var(--color-critical)] shadow-sm">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-[var(--color-ink-deep)] mb-4">403</h1>
        <h2 class="text-2xl font-bold text-[var(--color-ink-deep)] mb-4">Akses Ditolak</h2>
        <p class="text-[var(--color-slate)] mb-8 leading-relaxed">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini atau melakukan aksi tersebut. Jika menurut Anda ini adalah sebuah kesalahan, silakan hubungi administrator.
        </p>
        <div class="flex justify-center gap-4">
            <button onclick="window.history.back()" class="px-6 py-3 rounded-xl border border-[var(--color-hairline)] bg-[var(--color-surface)] text-[var(--color-ink)] font-semibold hover:bg-[var(--color-surface-soft)] transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </button>
            <a href="{{ url('/') }}" class="px-6 py-3 rounded-xl bg-[var(--color-primary)] text-white font-semibold shadow-sm hover:-translate-y-0.5 transition-transform flex items-center justify-center">
                Ke Beranda
            </a>
        </div>
    </div>
</div>
</body>
</html>
