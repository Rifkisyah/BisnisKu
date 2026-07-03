<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" 
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" 
      x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(darkMode) document.documentElement.classList.add('dark');"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ \App\Models\Setting::get('store_name', 'BisnisKu') }}</title>
    
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
            font-size: 16px;
            letter-spacing: -0.14px;
            padding: 16px 32px;
            transition: background-color 0.2s ease;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 100%;
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

        /* Product Gallery */
        .gallery-container {
            border-radius: 40px;
            background-color: var(--color-surface-soft);
            overflow: hidden;
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
                <h1 class="font-meta-display font-medium text-xl hidden sm:block">{{ \App\Models\Setting::get('store_name', 'Nama Toko') }}</h1>
            </a>
            
            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode" class="toggle-btn" title="Toggle Dark Mode">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <!-- Locale Toggle (POS Style) -->
                <form action="{{ route('locale.switch') }}" method="POST" class="flex items-center bg-[var(--color-surface-soft)] rounded-full p-1 border border-[var(--color-hairline)]" style="height: 34px;">
                    @csrf
                    <button type="submit" name="locale" value="id" class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}" style="line-height:1;">ID</button>
                    <button type="submit" name="locale" value="en" class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}" style="line-height:1;">EN</button>
                </form>

                @auth
                <a href="{{ route('dashboard') }}" class="btn-meta-outline text-sm hidden sm:block">
                    Dashboard
                </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Product Detail Split Layout -->
    <main class="flex-grow max-w-[1400px] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 relative">
            
            <!-- Left: Hero Gallery (50%) -->
            <div class="w-full lg:w-[50%] flex justify-center lg:justify-end pr-0 lg:pr-10">
                <div class="w-full max-w-[450px]">
                    <a href="{{ route('catalog.store.index', ['store' => app('current_store')->slug]) }}" class="inline-flex items-center gap-2 mb-8" style="color: var(--color-slate); font-weight: 600; font-size: 14px;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        {{ app()->getLocale() == 'id' ? 'Kembali ke Katalog' : 'Back to Catalog' }}
                    </a>

                    <div class="gallery-container aspect-square w-full relative" style="border-radius: 32px;">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <span style="color: var(--color-slate);" class="font-medium text-lg">No Image Available</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Sticky Purchase Rail (50%) -->
            <div class="w-full lg:w-[50%] relative pl-0 lg:pl-10">
                <div class="lg:sticky lg:top-32 max-w-[420px] mx-auto lg:mx-0 pt-4 lg:pt-12 pb-16">
                    <span style="color: var(--color-slate);" class="text-sm font-semibold uppercase tracking-widest mb-4 block">
                        {{ $product->category->name }}
                    </span>
                    
                    <h1 class="font-meta-display text-4xl sm:text-5xl font-medium leading-[1.1] mb-6">
                        {{ $product->name }}
                    </h1>
                    
                    <div class="mb-4">
                        <span class="text-3xl font-bold">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="mb-8 flex items-center gap-2">
                        <span style="color: var(--color-slate); font-size: 14px; font-weight: 500;">
                            {{ app()->getLocale() == 'id' ? 'Terjual:' : 'Sold:' }} <strong style="color: var(--color-ink-deep);">{{ $soldQty ?? 0 }}</strong>
                        </span>
                    </div>

                    <p class="text-lg mb-10" style="color: var(--color-ink); line-height: 1.6;">
                        {{ $product->description ?: (app()->getLocale() == 'id' ? 'Tidak ada deskripsi tersedia untuk produk ini.' : 'No description available for this product.') }}
                    </p>

                    <div class="space-y-6 pt-8" style="border-top: 1px solid var(--color-hairline);">
                        @if($product->isOutOfStock())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-start gap-3">
                                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <div>
                                    <h4 class="font-bold text-sm">{{ app()->getLocale() == 'id' ? 'Stok Habis' : 'Out of Stock' }}</h4>
                                    <p class="text-sm mt-1 opacity-90">{{ app()->getLocale() == 'id' ? 'Produk ini sedang tidak tersedia saat ini.' : 'This product is currently unavailable.' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-start gap-3">
                                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <div>
                                    <h4 class="font-bold text-sm">{{ app()->getLocale() == 'id' ? 'Stok Tersedia' : 'In Stock' }}</h4>
                                    <p class="text-sm mt-1 opacity-90">{{ app()->getLocale() == 'id' ? 'Produk ini tersedia dan dapat dibeli di toko kami.' : 'This product is available at our store.' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products (Bottom Section) -->
        @if($relatedProducts->count() > 0)
        <div class="mt-32 pt-16" style="border-top: 1px solid var(--color-hairline);">
            <h3 class="font-meta-display text-3xl font-medium mb-12 text-center">{{ app()->getLocale() == 'id' ? 'Mungkin Anda juga suka' : 'You might also like' }}</h3>
            
            <div class="grid grid-cols-2 gap-x-6 gap-y-12 sm:grid-cols-4">
                @foreach($relatedProducts as $rp)
                <a href="{{ route('catalog.show', $rp->product_code) }}" class="flex flex-col group cursor-pointer">
                    <div class="relative w-full aspect-square mb-6" style="border-radius: 24px; background-color: var(--color-surface-soft); overflow: hidden;">
                        @if($rp->image)
                            <img src="{{ asset('storage/'.$rp->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <span style="color: var(--color-slate);" class="font-medium text-xs">No Image</span>
                            </div>
                        @endif
                    </div>
                    <h4 class="font-meta-display text-lg font-medium leading-snug mb-1">
                        {{ $rp->name }}
                    </h4>
                    <span class="text-sm font-bold">Rp {{ number_format($rp->selling_price, 0, ',', '.') }}</span>
                </a>
                @endforeach
            </div>
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
