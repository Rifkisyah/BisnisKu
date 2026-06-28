@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.procurement'))
@section('content')
<div class="mx-auto max-w-4xl" x-data="purchaseForm()">
    <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm">
        <form method="POST" action="{{ route('product-purchases.store') }}" class="space-y-8">@csrf
            @if(request('repair_item_id'))<input type="hidden" name="repair_item_id" value="{{ request('repair_item_id') }}">@endif

            {{-- Step 1: Sumber Pengadaan --}}
            <div>
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-3">Sumber Pengadaan</h3>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach(['whatsapp' => ['Supplier', 'hover:bg-green-50'], 'marketplace' => ['Marketplace', 'hover:bg-blue-50'], 'offline' => ['Toko Offline', 'hover:bg-orange-50'], 'other' => ['Lainnya', 'hover:bg-gray-50']] as $src => [$label, $hoverClass])
                    <label class="cursor-pointer">
                        <input type="radio" name="source" value="{{ $src }}" x-model="source" class="sr-only" {{ old('source', 'other') === $src ? 'checked' : '' }}>
                        <div :class="source === '{{ $src }}' ? 'border-[2px] border-[#0143b5] bg-blue-50/20 text-[#0143b5]' : 'border-[1px] border-[rgba(10,19,23,0.12)] text-gray-600'" class="rounded-xl px-3 py-4 text-center text-sm font-bold transition-all {{ $hoverClass }}">{{ $label }}</div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Common Fields --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Tanggal Pembelian *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="input-field"></div>
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Estimasi Tiba (Opsional)</label>
                    <input type="date" name="estimated_arrival_date" value="{{ old('estimated_arrival_date', request('estimated_arrival_date')) }}" class="input-field"></div>
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.notes') }}</label>
                    <input type="text" name="notes" value="{{ old('notes', request('notes')) }}" maxlength="500" class="input-field" placeholder="Catatan umum..."></div>
            </div>

            {{-- WhatsApp Source Fields --}}
            <div x-show="source === 'whatsapp'" x-cloak class="rounded-xl bg-green-50 border border-green-200 p-4 space-y-3">
                <h4 class="type-subtitle font-semibold text-green-800"> Detail Supplier</h4>
                <p class="text-xs text-green-700">Pilih supplier. Pesan WhatsApp akan digenerate otomatis berdasarkan item yang dipilih. Anda bisa mengirim langsung atau menyalin teksnya.</p>
                <div><label class="block type-body-sm-bold text-green-700 mb-1">Pilih Supplier</label>
                    <select name="supplier_code" x-bind:required="source === 'whatsapp'" class="input-field">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->supplier_code }}" {{ old('supplier_code') === $s->supplier_code ? 'selected' : '' }}>
                            {{ $s->name }} {{ $s->contact ? "({$s->contact})" : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Marketplace Source Fields --}}
            <div x-show="source === 'marketplace'" x-cloak class="rounded-xl bg-blue-50 border border-blue-200 p-4 space-y-4">
                <h4 class="type-subtitle font-semibold text-blue-800 flex items-center gap-2">
                    Detail Marketplace 
                    <span class="text-[10px] bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full font-bold uppercase">Auto-Detect Link</span>
                </h4>

                {{-- Helper Link Input --}}
                <div class="bg-[var(--color-canvas)] p-3 rounded-xl border border-blue-100 shadow-sm">
                    <label class="block text-xs font-bold text-blue-800 mb-1">Paste Link Produk / Toko (Opsional)</label>
                    <input type="text" x-model="marketplace_link" @input="processMarketplaceLink" class="input-field !text-xs !py-1.5" placeholder="Contoh: https://shopee.co.id/namatoko atau tokopedia.com/toko">
                    <p class="text-[10px] text-blue-600 mt-1">Sistem akan mencoba mengisi otomatis Nama Marketplace & Toko berdasarkan link yang Anda masukkan.</p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div><label class="block type-body-sm-bold text-blue-700 mb-1">Nama Marketplace</label>
                        <input type="text" x-ref="marketplace_name" name="marketplace_name" value="{{ old('marketplace_name') }}" maxlength="100" class="input-field" placeholder="Shopee, Tokopedia, Lazada..."></div>
                    <div><label class="block type-body-sm-bold text-blue-700 mb-1">Nama Seller / Toko</label>
                        <input type="text" x-ref="marketplace_seller" name="marketplace_seller" value="{{ old('marketplace_seller') }}" maxlength="100" class="input-field"></div>
                </div>
            </div>

            {{-- Offline Store Fields --}}
            <div x-show="source === 'offline'" x-cloak class="rounded-xl bg-orange-50 border border-orange-200 p-4 space-y-3">
                <h4 class="type-subtitle font-semibold text-orange-800">Detail Toko Offline</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div><label class="block type-body-sm-bold text-orange-700 mb-1">Nama Toko</label>
                        <input type="text" name="store_name" value="{{ old('store_name') }}" maxlength="100" class="input-field"></div>
                    <div><label class="block type-body-sm-bold text-orange-700 mb-1">Nomor Nota / Struk</label>
                        <input type="text" name="receipt_number" value="{{ old('receipt_number') }}" maxlength="50" class="input-field"></div>
                    <div class="sm:col-span-2"><label class="block type-body-sm-bold text-orange-700 mb-1">Catatan</label>
                        <input type="text" name="offline_notes" value="{{ old('offline_notes') }}" maxlength="500" class="input-field"></div>
                </div>
            </div>

            {{-- Other Source Fields --}}
            <div x-show="source === 'other'" x-cloak class="rounded-xl bg-gray-50 border border-gray-200 p-4 space-y-3">
                <h4 class="type-subtitle font-semibold text-gray-700">Sumber Lainnya</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div><label class="block type-body-sm-bold text-gray-700 mb-1">Sumber</label>
                        <input type="text" name="other_source" value="{{ old('other_source') }}" minlength="2" maxlength="100" class="input-field" placeholder="Nama sumber pengadaan..."></div>
                    <div><label class="block type-body-sm-bold text-gray-700 mb-1">Keterangan</label>
                        <input type="text" name="other_notes" value="{{ old('other_notes') }}" maxlength="500" class="input-field"></div>
                </div>
            </div>

            {{-- Items --}}
            <div class="border-t border-[var(--color-hairline-soft)] pt-4">
                <h4 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-3">Daftar Produk yang Dibeli</h4>
                <div class="mb-2 grid grid-cols-12 gap-2 text-xs font-medium text-[var(--color-slate)] uppercase">
                    <div class="col-span-5">Produk</div><div class="col-span-2">Jumlah</div><div class="col-span-3">Harga Beli</div><div class="col-span-2"></div>
                </div>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="grid grid-cols-12 gap-2 mb-3 items-start border-b border-[var(--color-hairline)] pb-3 last:border-0">
                        <div class="col-span-5">
                            <select :name="'items['+idx+'][product_code]'" x-model="item.product_code" class="input-field">
                                <option value="">-- Pilih produk --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->product_code }}">{{ $p->name }} ({{ $p->product_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="item.quantity" min="1" required placeholder="Qty" class="input-field">
                        </div>
                        <div class="col-span-3">
                            <input type="text" :name="'items['+idx+'][purchase_price]'" x-model="item.price" required placeholder="Harga" class="input-field input-rupiah">
                        </div>
                        <div class="col-span-2">
                            <button type="button" @click="removeItem(idx)" class="w-full rounded-full bg-red-50 px-2 py-2 text-sm text-[var(--color-critical)] hover:bg-red-100 h-[42px] font-bold">✕</button>
                        </div>

                        <template x-if="!item.product_code">
                            <div class="col-span-12">
                                <input type="text" :name="'items['+idx+'][temp_product_name]'" x-model="item.temp_name"
                                    placeholder="Nama produk baru (min. 3 karakter)..."
                                    minlength="3" maxlength="100"
                                    class="input-field !py-2 text-xs border-dashed border-[var(--color-primary)] bg-indigo-50/30 placeholder-indigo-300">
                                <p class="mt-1 text-xs text-gray-400">3–100 karakter</p>
                            </div>
                        </template>
                        
                        <div class="col-span-12">
                            <input type="text" :name="'items['+idx+'][notes]'" x-model="item.notes" maxlength="500" placeholder="Catatan produk (opsional: warna, ukuran, garansi, dsb)..." class="input-field !py-2 text-xs text-[var(--color-ink-soft)] bg-[var(--color-surface-soft)]">
                        </div>

                        <div class="col-span-12 text-xs text-right text-[var(--color-slate)]" x-show="item.quantity && item.price">
                            Subtotal: <strong class="text-[var(--color-ink-deep)]" x-text="formatRupiah(getSubtotal(item))"></strong>
                        </div>
                    </div>
                </template>
                <button type="button" @click="addItem()" class="rounded-full border border-[rgba(10,19,23,0.12)] px-4 py-3 text-sm text-gray-700 font-bold hover:bg-gray-50 w-full transition-colors">
                    + {{ __('messages.add_item') }}
                </button>
                <div class="mt-4 text-right text-sm font-semibold text-[var(--color-ink-deep)]" x-show="items.length > 0">
                    Total: <span class="text-[var(--color-primary)]" x-text="formatRupiah(getTotal())"></span>
                </div>
            </div>

            <div class="flex gap-4 pt-6 border-t border-[var(--color-hairline-soft)] justify-center">
                <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="rounded-full border-[2px] border-[rgba(10,19,23,0.12)] px-8 py-3.5 text-[14px] font-bold text-gray-700 hover:border-gray-400 transition-colors flex justify-center items-center"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
                <button type="submit" class="rounded-full bg-gray-900 px-8 py-3.5 text-[14px] font-bold text-white hover:bg-gray-800 transition-colors">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function purchaseForm() {
    return {
        source: '{{ old('source', request('source', 'other')) }}',
        marketplace_link: '',
        processMarketplaceLink() {
            let url = this.marketplace_link.trim();
            if(!url) return;
            
            // Auto add https:// if missing so URL parser works
            if(!url.startsWith('http://') && !url.startsWith('https://')) {
                url = 'https://' + url;
            }
            
            try {
                let parsed = new URL(url);
                let hostname = parsed.hostname.toLowerCase();
                let path = decodeURIComponent(parsed.pathname);
                let parts = path.split('/').filter(p => p);
                
                let market = '';
                let seller = '';
                let product = '';
                
                // Tokopedia
                if (hostname.includes('tokopedia.com')) {
                    market = 'Tokopedia';
                    if (parts.length > 0 && !['p','promo','discovery','search','cart'].includes(parts[0])) {
                        seller = parts[0];
                        if (parts.length > 1) {
                            product = parts[1].replace(/-/g, ' '); // namatoko/nama-produk
                        }
                    }
                }
                else if (hostname.includes('tokopedia.link')) {
                    market = 'Tokopedia';
                }
                // Shopee
                else if (hostname.includes('shopee.co.id')) {
                    market = 'Shopee';
                    if(parts.length > 0 && !['buyer','search','mall','cart','user'].includes(parts[0])) {
                        // Check if it's a product link like /Nama-Produk-i.111.222
                        let matchProduct = parts[0].match(/(.*?)-i\.\d+\.\d+/);
                        if (matchProduct) {
                            product = matchProduct[1].replace(/-/g, ' ');
                        } else {
                            seller = parts[0]; // Assume shop name if no -i. match
                        }
                    }
                }
                else if (hostname.includes('shp.ee')) {
                    market = 'Shopee';
                }
                // Lazada
                else if (hostname.includes('lazada.co.id')) {
                    market = 'Lazada';
                    if (parts.length > 1 && parts[0] === 'products') {
                        // /products/nama-produk-i12345-s6789.html
                        let matchProduct = parts[1].match(/(.*?)-i\d+/);
                        if (matchProduct) {
                            product = matchProduct[1].replace(/-/g, ' ');
                        } else {
                            product = parts[1].replace(/\.html/g, '').replace(/-/g, ' ');
                        }
                    }
                }
                // Blibli
                else if (hostname.includes('blibli.com')) {
                    market = 'Blibli';
                    if (parts.length > 0 && parts[0] === 'merchant' && parts.length > 1) {
                        seller = parts[1];
                    } else if (parts.length > 1 && parts[0] === 'p') {
                        product = parts[1].replace(/-/g, ' '); // /p/nama-produk/ps--...
                    }
                }
                // Bukalapak
                else if (hostname.includes('bukalapak.com')) {
                    market = 'Bukalapak';
                    if (parts.length > 1 && parts[0] === 'u') {
                        seller = parts[1];
                    } else if (parts.length > 1 && parts[0] === 'p') {
                        // /p/kategori/id-nama-produk
                        let productSlug = parts[parts.length - 1];
                        // remove leading id part if exists like 12345-jual-nama-produk -> jual-nama-produk
                        product = productSlug.replace(/^\w+-jual-/, '').replace(/-/g, ' ');
                    }
                }
                // TikTok Shop
                else if (hostname.includes('tiktok.com')) {
                    market = 'TikTok Shop';
                    if (parts.length > 0 && parts[0].startsWith('@')) {
                        seller = parts[0].substring(1);
                    }
                }
                
                // Update refs
                if (market && this.$refs.marketplace_name) this.$refs.marketplace_name.value = market;
                if (seller && this.$refs.marketplace_seller) this.$refs.marketplace_seller.value = seller;
                
                // Capitalize first letter of each word for product
                if (product) {
                    product = product.replace(/\b\w/g, l => l.toUpperCase());
                    
                    // Put it into the first item if it's empty
                    if (this.items.length > 0) {
                        if (!this.items[0].product_code && !this.items[0].temp_name) {
                            this.items[0].temp_name = product;
                        }
                    }
                }
                
            } catch(e) {
                // Ignore parsing errors
            }
        },
        items: [{ product_code: '{{ old('items.0.product_code', request('product_code', '')) }}', temp_name: '{!! addslashes(old('items.0.temp_product_name', request('item_name', ''))) !!}', notes: '{{ old('items.0.notes', request('notes', '')) }}', quantity: {{ old('items.0.quantity', request('quantity', 1)) }}, price: '{{ old('items.0.purchase_price', request('price', '')) }}' }],
        addItem() { this.items.push({ product_code: '', temp_name: '', notes: '', quantity: '', price: '' }); },
        removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); },
        getSubtotal(item) {
            let price = parseInt((item.price || '0').toString().replace(/[^0-9]/g, '')) || 0;
            return item.quantity * price;
        },
        getTotal() {
            return this.items.reduce((sum, item) => sum + this.getSubtotal(item), 0);
        }
    }
}
</script>
@endpush
