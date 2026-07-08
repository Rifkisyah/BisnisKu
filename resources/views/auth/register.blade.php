<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Sign up') }} - {{ $globalShopName }}</title>
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
<div class="flex min-h-screen">
    {{-- Left Panel — Brand showcase --}}
    <div class="hidden w-1/2 lg:flex items-center justify-center bg-[var(--color-surface-soft)] p-16">
        <div class="text-center max-w-lg">
            <div class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-[var(--radius-xxxl)] bg-[var(--color-primary)] text-white shadow-lg">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
            <h1 class="type-display text-[var(--color-ink-deep)]">{{ __('Join BisnisKu') }}</h1>
            <p class="mt-4 type-subtitle-md text-[var(--color-slate)]">{{ __('Start managing your business effectively today. Setting up takes less than a minute.') }}</p>
            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div class="card bg-[var(--color-canvas)] p-6 rounded-[var(--radius-xl)] shadow-sm">
                    <svg class="w-8 h-8 mx-auto mb-3 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p class="type-body-sm-bold text-[var(--color-ink)]">{{ __('Smart POS') }}</p>
                </div>
                <div class="card bg-[var(--color-canvas)] p-6 rounded-[var(--radius-xl)] shadow-sm">
                    <svg class="w-8 h-8 mx-auto mb-3 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="type-body-sm-bold text-[var(--color-ink)]">{{ __('Service') }}</p>
                </div>
                <div class="card bg-[var(--color-canvas)] p-6 rounded-[var(--radius-xl)] shadow-sm">
                    <svg class="w-8 h-8 mx-auto mb-3 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="type-body-sm-bold text-[var(--color-ink)]">{{ __('Analytics') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel — Register Form --}}
    <div class="flex w-full items-center justify-center px-6 lg:w-1/2 relative py-12">
        <div class="absolute top-8 right-8 flex items-center gap-4">
            <form action="{{ route('locale.switch') }}" method="POST" class="flex items-center bg-[var(--color-surface-soft)] rounded-full p-1 border border-[var(--color-hairline-soft)]">
                @csrf
                <button type="submit" name="locale" value="en" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">EN</button>
                <button type="submit" name="locale" value="id" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'id' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">ID</button>
            </form>
            <button @click="theme = theme === 'light' ? 'dark' : 'light'" class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-ink)] hover:bg-[var(--color-hairline-soft)] transition-all">
                <svg x-show="theme === 'light'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-cloak x-show="theme === 'dark'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
            <a href="{{ route('home') }}" class="type-body-sm-bold text-[var(--color-slate)] hover:text-[var(--color-ink)] transition-colors hidden sm:inline-block">{{ __('Back to home') }}</a>
        </div>
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center lg:text-left">
                <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-[var(--radius-xxxl)] bg-[var(--color-primary)] text-white shadow-lg lg:hidden">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <h2 class="type-heading-lg text-[var(--color-ink-deep)]">{{ __('Create an account') }}</h2>
                <p class="mt-2 type-body-md text-[var(--color-slate)]">{{ __('Start managing your business right away.') }}</p>
            </div>

            @if($errors->any())
            <div class="mb-6 rounded-[var(--radius-lg)] border border-[var(--color-critical-strong)] bg-[var(--color-canvas)] p-4 shadow-[0_1px_2px_rgba(208,2,27,0.1)]">
                <ul class="list-disc pl-4 type-body-sm text-[var(--color-critical)]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-5"
                  x-data="{
                    storeName: '{{ old('store_name') }}',
                    storeSlug: '{{ old('store_slug') }}',
                    slugAvailable: null,
                    checkingSlug: false,
                    generateSlug(name) {
                        return name.toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .trim('-');
                    },
                    async checkSlug(slug) {
                        if (!slug) { this.slugAvailable = null; return; }
                        this.checkingSlug = true;
                        try {
                            const res = await fetch(`/api/check-slug?name=${encodeURIComponent(slug)}`);
                            const data = await res.json();
                            this.slugAvailable = data.available;
                        } catch(e) { this.slugAvailable = null; }
                        this.checkingSlug = false;
                    }
                  }"
                  @submit.prevent="storeSlug = storeSlug || generateSlug(storeName); $el.submit()">
                @csrf

                {{-- Section: Store Info --}}
                <p class="type-body-sm-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('Informasi Toko') }}</p>

                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">{{ __('Nama Toko') }} <span class="text-[var(--color-critical)]">*</span></label>
                    <input type="text" name="store_name" id="store_name"
                           x-model="storeName"
                           @input.debounce.400ms="storeSlug = generateSlug(storeName); checkSlug(storeSlug)"
                           value="{{ old('store_name') }}" required autofocus
                           class="w-full h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-[var(--radius-lg)] px-4 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                           placeholder="Armal Cellular">
                </div>

                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">
                        {{ __('URL Katalog') }}
                        <span class="text-[var(--color-slate)] font-normal">(opsional, auto-generate)</span>
                    </label>
                    <div class="flex items-center gap-0">
                        <span class="inline-flex items-center h-11 px-3 text-xs text-[var(--color-slate)] bg-[var(--color-surface-soft)] border border-r-0 border-[var(--color-hairline)] rounded-l-[var(--radius-lg)]">bisnisku.app/store/</span>
                        <input type="text" name="store_slug" id="store_slug"
                               x-model="storeSlug"
                               @input.debounce.500ms="checkSlug(storeSlug)"
                               value="{{ old('store_slug') }}"
                               class="flex-1 h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-r-[var(--radius-lg)] px-4 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                               placeholder="armal-cellular">
                    </div>
                    <div class="mt-1 h-5">
                        <p x-show="checkingSlug" class="type-caption text-[var(--color-slate)]">Memeriksa ketersediaan...</p>
                        <p x-show="!checkingSlug && slugAvailable === true" class="type-caption text-[var(--color-success)]">✓ URL Katalog Toko tersedia</p>
                        <p x-show="!checkingSlug && slugAvailable === false" class="type-caption text-[var(--color-critical)]">✗ URL Katalog Toko telah digunakan, coba yang lain</p>
                    </div>
                </div>

                <hr class="border-[var(--color-hairline-soft)]">

                {{-- Section: Owner Info --}}
                <p class="type-body-sm-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('Informasi Pemilik') }}</p>

                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">{{ __('Nama Lengkap') }} <span class="text-[var(--color-critical)]">*</span></label>
                    <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}" required
                           class="w-full h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-[var(--radius-lg)] px-4 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                           placeholder="John Doe">
                </div>

                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">{{ __('Email') }} <span class="text-[var(--color-critical)]">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-[var(--radius-lg)] px-4 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                           placeholder="name@company.com">
                </div>

                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">{{ __('Nomor HP / WhatsApp') }}</label>
                    <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                           class="w-full h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-[var(--radius-lg)] px-4 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                           placeholder="08xxxxxxxxxx">
                </div>

                <div x-data="{ show: false }">
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">{{ __('Password') }} <span class="text-[var(--color-critical)]">*</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                               class="w-full h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-[var(--radius-lg)] px-4 pr-10 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                               placeholder="Min. 8 karakter">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-slate)] hover:text-[var(--color-ink)] focus:outline-none">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                </div>
                <div x-data="{ show: false }">
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">{{ __('Konfirmasi Password') }} <span class="text-[var(--color-critical)]">*</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                               class="w-full h-11 bg-[var(--color-canvas)] text-[var(--color-ink)] border border-[var(--color-hairline)] rounded-[var(--radius-lg)] px-4 pr-10 type-body-sm transition-colors focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-slate)] hover:text-[var(--color-ink)] focus:outline-none">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btn-register" class="btn-buy w-full shadow-sm hover:-translate-y-0.5 transition-transform mt-2">
                    {{ __('Daftarkan Toko') }}
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="type-body-sm text-[var(--color-slate)]">{{ __('Sudah punya akun?') }} <a href="{{ route('login') }}" class="text-[var(--color-primary)] font-bold hover:underline">{{ __('Masuk') }}</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

