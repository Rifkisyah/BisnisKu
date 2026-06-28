<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Katalog - {{ \App\Models\Setting::get('store_name', 'Nama Toko') }}</title>
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet"/>
@vite(['resources/css/app.css'])</head>
<body class="bg-[var(--color-surface-soft)] font-[Montserrat]">
<header class="bg-[var(--color-canvas)] shadow-sm border-b border-[var(--color-hairline-soft)]">
    <div class="mx-auto max-w-7xl px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">BK</div>
            <div><h1 class="text-lg font-bold text-[var(--color-ink-deep)]">{{ \App\Models\Setting::get('store_name', 'Nama Toko') }}</h1><p class="text-xs text-[var(--color-stone)]">Katalog Produk</p></div>
        </div>
        <a href="{{ route('login') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-[var(--color-charcoal)] hover:bg-gray-200 transition">Login</a>
    </div>
</header>
<main class="mx-auto max-w-7xl px-4 py-8">
    <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="input-field !w-auto w-64 focus:border-indigo-500 focus:ring-indigo-500">
        <select name="category" onchange="this.form.submit()" class="input-field !w-auto"><option value="">Semua Kategori</option>
            @foreach($categories as $c)<option value="{{ $c->category_code }}" {{ request('category') == $c->category_code ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select>
        <button type="submit" class="btn-primary !py-2.5 !px-5 transition">Cari</button>
    </form>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
        @forelse($products as $p)
        <div class="group rounded-xl bg-[var(--color-canvas)] shadow-sm ring-1 ring-gray-100 overflow-hidden hover:shadow-md hover:ring-indigo-200 transition-all">
            <div class="relative aspect-square bg-gray-100">
                @if($p->image)<img src="{{ asset('storage/'.$p->image) }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else<div class="flex h-full items-center justify-center text-4xl text-gray-300">📱</div>@endif
                @if(in_array($p->product_code, $bestSellerCodes))<span class="absolute top-2 left-2 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-bold text-white shadow">🔥 Terlaris</span>@endif
                @if($p->isOutOfStock())<span class="absolute top-2 right-2 rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">Habis</span>@endif
            </div>
            <div class="p-3">
                <p class="text-xs text-[var(--color-stone)]">{{ $p->category->name }}</p>
                <h3 class="mt-0.5 type-subtitle-lg text-[var(--color-ink-deep)] line-clamp-2">{{ $p->name }}</h3>
                <p class="mt-1.5 text-base font-bold text-[var(--color-primary)]">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs {{ $p->isOutOfStock() ? 'text-red-500' : 'text-[var(--color-success)]' }}">{{ $p->isOutOfStock() ? 'Stok Habis' : 'Tersedia' }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center text-[var(--color-stone)]">Tidak ada produk ditemukan.</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</main>
<footer class="border-t border-[var(--color-hairline-soft)] bg-[var(--color-canvas)] py-6 text-center text-sm text-[var(--color-stone)]">© {{ date('Y') }} {{ \App\Models\Setting::get('store_name', 'Nama Toko') }}. All rights reserved.</footer>
</body></html>
