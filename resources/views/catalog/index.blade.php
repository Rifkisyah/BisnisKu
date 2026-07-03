<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" 
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" 
      x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(darkMode) document.documentElement.classList.add('dark');"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - {{ \App\Models\Setting::get('store_name', 'BisnisKu') }}</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --color-primary-meta: #0064e0;
            --color-primary-deep: #0056c2;
            --color-canvas: #ffffff;
            --color-surface-soft: #f5f6f7;
            --color-ink-deep: #1c2b33;
            --color-ink: #334155;
            --color-charcoal: #475569;
            --color-slate: #64748b;
            --color-hairline: #e2e8f0;
            --color-warning: #f59e0b;
        }

        html.dark {
            --color-canvas: #111827;
            --color-surface-soft: #1f2937;
            --color-ink-deep: #f9fafb;
            --color-ink: #d1d5db;
            --color-charcoal: #9ca3af;
            --color-slate: #6b7280;
            --color-hairline: #374151;
            --color-primary-meta: #3b82f6;
            --color-primary-deep: #60a5fa;
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            background-color: var(--color-canvas); 
            color: var(--color-ink-deep);
            letter-spacing: -0.14px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        h1, h2, h3, h4, h5, h6, .font-meta-display { 
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            letter-spacing: -0.02em;
        }

        .header-meta {
            background-color: var(--color-canvas);
            border-bottom: 1px solid var(--color-hairline);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-meta {
            background-color: var(--color-primary-meta);
            color: white;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: -0.14px;
            padding: 12px 24px;
            transition: background-color 0.2s ease;
        }
        .btn-meta:hover {
            background-color: var(--color-primary-deep);
        }

        .btn-meta-outline {
            background-color: transparent;
            color: var(--color-ink-deep);
            border: 1px solid var(--color-hairline);
            border-radius: 9999px;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 24px;
            transition: all 0.2s ease;
        }
        .btn-meta-outline:hover {
            background-color: var(--color-surface-soft);
        }

        .input-meta {
            background-color: var(--color-canvas);
            border: 1px solid var(--color-hairline);
            border-radius: 9999px;
            padding: 14px 24px;
            font-size: 16px;
            color: var(--color-ink-deep);
            width: 100%;
            outline: none;
            transition: border-color 0.2s, background-color 0.3s ease, color 0.3s ease;
        }
        .input-meta:focus {
            border-color: var(--color-primary-meta);
        }

        /* Feature Cards */
        .card-meta {
            background: transparent;
            border: none;
            box-shadow: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        
        .card-meta-img {
            border-radius: 32px;
            background-color: var(--color-surface-soft);
            overflow: hidden;
            aspect-ratio: 1/1;
            transition: all 0.3s ease;
        }

        .card-meta:hover .card-meta-img {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.15);
        }

        /* Toggles */
        .toggle-btn {
            background: var(--color-surface-soft);
            color: var(--color-ink-deep);
            border: 1px solid var(--color-hairline);
            padding: 8px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .toggle-btn:hover {
            border-color: var(--color-slate);
        }
        
        /* Language Pill like POS */
        .lang-pill {
            display: flex;
            align-items: center;
            background-color: var(--color-surface-soft);
            border-radius: 9999px;
            padding: 4px;
            border: 1px solid var(--color-hairline);
        }
        .lang-pill-btn {
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--color-slate);
        }
        .lang-pill-btn:hover {
            color: var(--color-ink);
        }
        .lang-pill-btn.active {
            background-color: var(--color-ink-deep);
            color: var(--color-canvas);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Custom Dropdown */
        .dropdown-menu {
            background-color: var(--color-canvas);
            border: 1px solid var(--color-hairline);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            overflow: hidden;
            z-index: 50;
        }
        .dropdown-item {
            padding: 12px 20px;
            color: var(--color-ink-deep);
            font-size: 15px;
            transition: background-color 0.2s;
            display: block;
        }
        .dropdown-item:hover {
            background-color: var(--color-surface-soft);
        }
        .dropdown-item.active {
            font-weight: 600;
            color: var(--color-primary-meta);
            background-color: var(--color-surface-soft);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">

    <!-- Header -->
    <header class="header-meta sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('catalog.store.index', ['store' => app('current_store')->slug]) }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full flex items-center justify-center font-meta-display font-bold text-lg" style="background-color: var(--color-ink-deep); color: var(--color-canvas);">
                    {{ substr(\App\Models\Setting::get('store_name', 'B'), 0, 1) }}
                </div>
                <h1 class="font-meta-display font-medium text-xl">{{ \App\Models\Setting::get('store_name', 'Nama Toko') }}</h1>
            </a>
            
            <div class="flex items-center gap-4">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode" class="toggle-btn" title="Toggle Dark Mode">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <!-- Locale Toggle (POS Style) -->
                <form action="{{ route('locale.switch') }}" method="POST" class="lang-pill">
                    @csrf
                    <button type="submit" name="locale" value="id" class="lang-pill-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}">ID</button>
                    <button type="submit" name="locale" value="en" class="lang-pill-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</button>
                </form>

                @auth
                <a href="{{ route('dashboard') }}" class="btn-meta-outline text-sm hidden sm:block">
                    Dashboard
                </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 px-6 max-w-7xl mx-auto text-center">
        <h2 class="font-meta-display font-medium text-5xl sm:text-6xl mb-6 leading-tight max-w-4xl mx-auto">
            {{ app()->getLocale() == 'id' ? 'Temukan koleksi produk terbaru kami.' : 'Discover our latest product collection.' }}
        </h2>
        <p class="text-[var(--color-slate)] text-lg sm:text-xl font-light max-w-2xl mx-auto mb-12">
            {{ app()->getLocale() == 'id' ? 'Jelajahi produk berkualitas tinggi yang tersedia hari ini.' : 'Explore high quality products available today.' }}
        </p>
        
        <!-- Live Search & Custom Dropdown Form -->
        <div class="max-w-3xl mx-auto">
            <form x-ref="searchForm" action="{{ route('catalog.store.index', ['store' => app('current_store')->slug]) }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        @input.debounce.500ms="$refs.searchForm.submit()"
                        placeholder="{{ app()->getLocale() == 'id' ? 'Cari produk...' : 'Search products...' }}" 
                        class="input-meta pl-6 pr-4 py-4 w-full">
                </div>
                
                <!-- Custom Alpine Dropdown -->
                <div class="relative w-full sm:w-64 text-left" x-data="{ open: false, selected: '{{ request('category') }}' }">
                    <input type="hidden" name="category" x-model="selected">
                    
                    <button type="button" @click="open = !open" @click.away="open = false" 
                        class="input-meta py-4 px-6 flex items-center justify-between cursor-pointer w-full"
                        style="height: 100%;">
                        <span class="truncate">
                            @if(request('category'))
                                {{ $categories->firstWhere('category_code', request('category'))->name ?? (app()->getLocale() == 'id' ? 'Kategori' : 'Category') }}
                            @else
                                {{ app()->getLocale() == 'id' ? 'Semua Kategori' : 'All Categories' }}
                            @endif
                        </span>
                        <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': open }" style="color: var(--color-slate);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu (drops DOWNwards) -->
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 mt-2 w-full dropdown-menu top-full left-0 origin-top">
                        <div class="py-2 max-h-60 overflow-auto">
                            <a href="#" @click.prevent="selected = ''; open = false; $nextTick(() => $refs.searchForm.submit())" 
                               class="dropdown-item {{ !request('category') ? 'active' : '' }}">
                                {{ app()->getLocale() == 'id' ? 'Semua Kategori' : 'All Categories' }}
                            </a>
                            @foreach($categories as $c)
                                <a href="#" @click.prevent="selected = '{{ $c->category_code }}'; open = false; $nextTick(() => $refs.searchForm.submit())" 
                                   class="dropdown-item {{ request('category') == $c->category_code ? 'active' : '' }}">
                                    {{ $c->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-6 py-6 w-full">
        
        <!-- Popular Products Section (if not searching/filtering) -->
        @if(!request('search') && !request('category') && isset($topSellers) && $topSellers->count() > 0)
        <div class="mb-20">
            <h3 class="font-meta-display text-3xl font-medium mb-8">
                {{ app()->getLocale() == 'id' ? 'Produk Terpopuler' : 'Popular Products' }}
            </h3>
            <div class="grid grid-cols-2 gap-x-6 gap-y-12 sm:grid-cols-4">
                @foreach($topSellers->take(4) as $p)
                <a href="{{ route('catalog.store.show', ['store' => app('current_store')->slug, 'product' => $p->product_code]) }}" class="card-meta group">
                    <div class="relative card-meta-img mb-5">
                        @if($p->image)
                            <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <span style="color: var(--color-slate);" class="font-medium text-sm">No Image</span>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span style="background-color: var(--color-ink-deep); color: var(--color-canvas);" class="text-[12px] font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-[#f59e0b]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-1.245-1.134-1.772a2.53 2.53 0 01-.067-.565c.003-.3.023-.6.04-.848.016-.237.031-.444.031-.611v-.01z" clip-rule="evenodd"/></svg>
                                {{ app()->getLocale() == 'id' ? 'Terlaris' : 'Best Seller' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span style="color: var(--color-slate);" class="text-xs font-normal mb-1 block">{{ $p->category->name }}</span>
                        <h4 class="font-meta-display text-lg font-medium leading-snug mb-1">{{ $p->name }}</h4>
                        <span class="text-md font-bold">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            
            <div class="mt-16 w-full" style="height: 1px; background-color: var(--color-hairline);"></div>
        </div>
        @endif

        <!-- All Products List -->
        @if(request('search') || request('category'))
            <div class="mb-10 flex items-center justify-between">
                <h3 class="font-meta-display text-2xl font-medium">
                    {{ app()->getLocale() == 'id' ? 'Hasil Pencarian' : 'Search Results' }}
                </h3>
                <a href="{{ route('catalog.store.index', ['store' => app('current_store')->slug]) }}" class="text-sm font-bold text-[var(--color-primary-meta)] hover:underline">Reset</a>
            </div>
        @else
            <div class="mb-10">
                <h3 class="font-meta-display text-3xl font-medium">{{ app()->getLocale() == 'id' ? 'Semua Produk' : 'All Products' }}</h3>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-x-6 gap-y-12 sm:grid-cols-3 lg:grid-cols-4">
            @forelse($products as $p)
                <a href="{{ route('catalog.store.show', ['store' => app('current_store')->slug, 'product' => $p->product_code]) }}" class="card-meta group">
                    <div class="relative card-meta-img mb-5">
                    @if($p->image)
                        <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <span style="color: var(--color-slate);" class="font-medium text-sm">No Image</span>
                        </div>
                    @endif
                    
                    @if($p->isOutOfStock())
                        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm flex items-center justify-center">
                            <span class="bg-red-600 text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm">
                                {{ app()->getLocale() == 'id' ? 'Habis' : 'Out of Stock' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col flex-grow">
                    <span style="color: var(--color-slate);" class="text-sm font-normal mb-2 block">{{ $p->category->name }}</span>
                    <h4 class="font-meta-display text-xl font-medium leading-snug mb-2">
                        {{ $p->name }}
                    </h4>
                    
                    <div class="mt-auto pt-2">
                        <span class="text-[17px] font-bold">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                <h3 class="font-meta-display text-2xl font-medium mb-3">Produk Tidak Ditemukan</h3>
                <p style="color: var(--color-slate);" class="max-w-md">Silakan coba kata kunci lain atau ubah filter kategori Anda.</p>
                <a href="{{ route('catalog.store.index', ['store' => app('current_store')->slug]) }}" class="mt-8 btn-meta-outline">
                    Lihat Semua Produk
                </a>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
        <div class="mt-20 pt-8 flex justify-center" style="border-top: 1px solid var(--color-hairline);">
            {{ $products->links() }}
        </div>
        @endif
        
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-16 header-meta">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full flex items-center justify-center font-meta-display font-bold text-sm" style="background-color: var(--color-ink-deep); color: var(--color-canvas);">
                    {{ substr(\App\Models\Setting::get('store_name', 'B'), 0, 1) }}
                </div>
                <span class="font-meta-display font-medium">{{ \App\Models\Setting::get('store_name', 'Nama Toko') }}</span>
            </div>
            
            <div class="text-sm" style="color: var(--color-slate);">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('store_name', 'Nama Toko') }}. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
