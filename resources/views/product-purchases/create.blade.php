@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.procurement'))
@section('content')
<div class="mx-auto max-w-4xl" x-data="purchaseForm()">
    <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm">
        <form method="POST" action="{{ route('product-purchases.store') }}" class="space-y-8">@csrf
            @if(request('repair_item_id'))<input type="hidden" name="repair_item_id" value="{{ request('repair_item_id') }}">@endif

            {{-- Common Fields --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.purchase_date') }} *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="input-field"></div>
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Estimasi Tiba (Opsional)</label>
                    <input type="date" name="estimated_arrival_date" value="{{ old('estimated_arrival_date', request('estimated_arrival_date')) }}" class="input-field"></div>
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.notes') }}</label>
                    <input type="text" name="notes" value="{{ old('notes', request('notes')) }}" maxlength="500" class="input-field" placeholder="Catatan umum..."></div>
            </div>

            {{-- Items --}}
            <div class="border-t border-[var(--color-hairline-soft)] pt-4">
                <h4 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-3">Daftar Produk yang Dibeli</h4>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="font-bold text-gray-700" x-text="'Item #' + (idx + 1)"></h5>
                            <button type="button" @click="removeItem(idx)" class="rounded-full bg-red-50 px-3 py-1 text-xs text-[var(--color-critical)] hover:bg-red-100 font-bold">Hapus Item</button>
                        </div>
                        
                        <div class="grid grid-cols-12 gap-3 mb-4">
                            <div class="col-span-12 sm:col-span-5">
                                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1">Produk</label>
                                <select :name="'items['+idx+'][product_code]'" x-model="item.product_code" class="input-field">
                                    <option value="">{{ __('messages.select_product') }}</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->product_code }}">{{ $p->name }} ({{ $p->product_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1">Jumlah</label>
                                <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="item.quantity" min="1" required placeholder="Qty" class="input-field">
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1">Harga Satuan</label>
                                <input type="text" :name="'items['+idx+'][purchase_price]'" x-model="item.price" required placeholder="Harga" class="input-field input-rupiah">
                            </div>
                            
                            <template x-if="!item.product_code">
                                <div class="col-span-12">
                                    <input type="text" :name="'items['+idx+'][temp_product_name]'" x-model="item.temp_name"
                                        placeholder="Nama produk baru (min. 3 karakter)..."
                                        minlength="3" maxlength="100"
                                        class="input-field !py-2 text-xs border-dashed border-[var(--color-primary)] bg-indigo-50/30 placeholder-indigo-300">
                                    
                                    <label class="flex items-center gap-2 cursor-pointer mt-2 pl-1">
                                        <input type="checkbox" :name="'items['+idx+'][is_sparepart]'" value="1" x-model="item.is_sparepart" class="checkbox-field rounded border-gray-300 text-[var(--color-primary)] shadow-sm focus:border-[var(--color-primary)] focus:ring focus:ring-[var(--color-primary)] focus:ring-opacity-50 h-4 w-4">
                                        <span class="text-xs font-semibold text-[var(--color-ink)]">Tandai sebagai kategori Sparepart</span>
                                    </label>
                                </div>
                            </template>
                            
                            <div class="col-span-12">
                                <input type="text" :name="'items['+idx+'][notes]'" x-model="item.notes" maxlength="500" placeholder="Catatan produk (opsional: warna, ukuran, dsb)..." class="input-field !py-2 text-xs">
                            </div>
                        </div>

                        {{-- Source Selection --}}
                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <label class="block type-body-sm-bold text-[var(--color-ink)] mb-2">Sumber Pengadaan</label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="src in ['whatsapp', 'marketplace', 'offline', 'other']">
                                    <label class="cursor-pointer">
                                        <input type="radio" :name="'items['+idx+'][source]'" :value="src" x-model="item.source" class="sr-only">
                                        <div :class="item.source === src ? 'border-[2px] border-[#0143b5] bg-blue-50 text-[#0143b5]' : 'border-[1px] border-gray-300 bg-white text-gray-600'" class="rounded-lg px-3 py-1.5 text-xs font-bold transition-all" x-text="src === 'whatsapp' ? 'Supplier' : (src === 'marketplace' ? 'Marketplace' : (src === 'offline' ? 'Toko Fisik' : 'Lainnya'))"></div>
                                    </label>
                                </template>
                            </div>

                            {{-- Source Details --}}
                            <div x-show="item.source === 'whatsapp'" class="bg-green-50 rounded-lg p-3 border border-green-200">
                                <label class="block text-xs font-bold text-green-800 mb-1">Pilih Supplier</label>
                                <select :name="'items['+idx+'][supplier_code]'" x-model="item.supplier_code" x-bind:required="item.source === 'whatsapp'" class="input-field !py-1.5 !text-xs">
                                    <option value="">{{ __('messages.select_supplier') }}</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->supplier_code }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="item.source === 'marketplace'" class="bg-blue-50 rounded-lg p-3 border border-blue-200 space-y-2">
                                <div class="bg-white p-3 rounded-xl border border-blue-100 shadow-sm mb-3">
                                    <label class="block text-xs font-bold text-blue-800 mb-1">Paste Link Produk / Toko (Opsional)</label>
                                    <input type="text" x-model="item.marketplace_link" @input="processMarketplaceLink(idx)" class="input-field !text-xs !py-1.5" placeholder="Contoh: https://shopee.co.id/namatoko atau tokopedia.com/toko">
                                    <p class="text-[10px] text-blue-600 mt-1">Otomatis mengisi Nama Marketplace, Toko, dan Produk sementara (jika kosong).</p>
                                </div>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <div><label class="block text-xs font-bold text-blue-800 mb-1">Marketplace</label>
                                        <input type="text" :name="'items['+idx+'][marketplace_name]'" x-model="item.marketplace_name" maxlength="100" class="input-field !py-1.5 !text-xs" placeholder="Shopee, Tokopedia..."></div>
                                    <div><label class="block text-xs font-bold text-blue-800 mb-1">Nama Toko</label>
                                        <input type="text" :name="'items['+idx+'][marketplace_seller]'" x-model="item.marketplace_seller" maxlength="100" class="input-field !py-1.5 !text-xs"></div>
                                </div>
                                <div><label class="block text-xs font-bold text-blue-800 mb-1">{{ __('messages.additional_notes_optional') }}</label>
                                    <input type="text" :name="'items['+idx+'][marketplace_notes]'" x-model="item.marketplace_notes" maxlength="500" class="input-field !py-1.5 !text-xs"></div>
                            </div>

                            <div x-show="item.source === 'offline'" class="bg-orange-50 rounded-lg p-3 border border-orange-200 space-y-2">
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <div><label class="block text-xs font-bold text-orange-800 mb-1">Nama Toko</label>
                                        <input type="text" :name="'items['+idx+'][store_name]'" x-model="item.store_name" maxlength="100" class="input-field !py-1.5 !text-xs"></div>
                                    <div><label class="block text-xs font-bold text-orange-800 mb-1">Nomor Nota (Opsional)</label>
                                        <input type="text" :name="'items['+idx+'][receipt_number]'" x-model="item.receipt_number" maxlength="50" class="input-field !py-1.5 !text-xs"></div>
                                </div>
                            </div>

                            <div x-show="item.source === 'other'" class="bg-gray-100 rounded-lg p-3 border border-gray-300 space-y-2">
                                <div><label class="block text-xs font-bold text-gray-700 mb-1">Sumber Pengadaan</label>
                                    <input type="text" :name="'items['+idx+'][other_source]'" x-model="item.other_source" maxlength="100" class="input-field !py-1.5 !text-xs"></div>
                            </div>
                        </div>

                        <div class="mt-4 text-sm text-right text-[var(--color-slate)]" x-show="item.quantity && item.price">
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

            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-[var(--color-hairline-soft)] mt-6 w-full">
                <a href="{{ route('product-purchases.index') }}" class="btn-ghost w-full sm:flex-1 text-center order-2 sm:order-1 flex justify-center items-center"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
                <button type="submit" class="btn-primary w-full sm:flex-1 text-center order-1 sm:order-2 flex justify-center items-center">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function purchaseForm() {
    return {
        items: {!! json_encode(old('items', [
            [
                'product_code' => request('product_code', ''),
                'temp_name' => request('item_name', ''),
                'is_sparepart' => false,
                'notes' => request('notes', ''),
                'quantity' => request('quantity', 1),
                'price' => request('price', ''),
                'source' => 'whatsapp',
                'supplier_code' => '',
                'marketplace_name' => '',
                'marketplace_seller' => '',
                'marketplace_notes' => '',
                'store_name' => '',
                'receipt_number' => '',
                'other_source' => '',
                'marketplace_link' => ''
            ]
        ])) !!},
        addItem() { 
            let last = this.items.length > 0 ? this.items[this.items.length - 1] : null;
            this.items.push({ 
                product_code: '', temp_name: '', is_sparepart: false, notes: '', quantity: '', price: '',
                source: last ? last.source : 'whatsapp',
                supplier_code: last ? last.supplier_code : '',
                marketplace_name: last ? last.marketplace_name : '',
                marketplace_seller: last ? last.marketplace_seller : '',
                marketplace_notes: last ? last.marketplace_notes : '',
                store_name: last ? last.store_name : '',
                receipt_number: '',
                other_source: last ? last.other_source : '',
                marketplace_link: ''
            }); 
        },
        processMarketplaceLink(idx) {
            let item = this.items[idx];
            let url = (item.marketplace_link || '').trim();
            if(!url) return;
            
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
                
                if (hostname.includes('tokopedia.com')) {
                    market = 'Tokopedia';
                    if (parts.length > 0 && !['p','promo','discovery','search','cart'].includes(parts[0])) {
                        seller = parts[0];
                        if (parts.length > 1) {
                            product = parts[1].replace(/-/g, ' '); 
                        }
                    }
                }
                else if (hostname.includes('tokopedia.link')) { market = 'Tokopedia'; }
                else if (hostname.includes('shopee.co.id')) {
                    market = 'Shopee';
                    if(parts.length > 0 && !['buyer','search','mall','cart','user'].includes(parts[0])) {
                        let matchProduct = parts[0].match(/(.*?)-i\.\d+\.\d+/);
                        if (matchProduct) {
                            product = matchProduct[1].replace(/-/g, ' ');
                        } else {
                            seller = parts[0]; 
                        }
                    }
                }
                else if (hostname.includes('shp.ee')) { market = 'Shopee'; }
                else if (hostname.includes('lazada.co.id')) {
                    market = 'Lazada';
                    if (parts.length > 1 && parts[0] === 'products') {
                        let matchProduct = parts[1].match(/(.*?)-i\d+/);
                        if (matchProduct) {
                            product = matchProduct[1].replace(/-/g, ' ');
                        } else {
                            product = parts[1].replace(/\.html/g, '').replace(/-/g, ' ');
                        }
                    }
                }
                else if (hostname.includes('blibli.com')) {
                    market = 'Blibli';
                    if (parts.length > 0 && parts[0] === 'merchant' && parts.length > 1) {
                        seller = parts[1];
                    } else if (parts.length > 1 && parts[0] === 'p') {
                        product = parts[1].replace(/-/g, ' '); 
                    }
                }
                else if (hostname.includes('bukalapak.com')) {
                    market = 'Bukalapak';
                    if (parts.length > 1 && parts[0] === 'u') {
                        seller = parts[1];
                    } else if (parts.length > 1 && parts[0] === 'p') {
                        let productSlug = parts[parts.length - 1];
                        product = productSlug.replace(/^\w+-jual-/, '').replace(/-/g, ' ');
                    }
                }
                else if (hostname.includes('tiktok.com')) {
                    market = 'TikTok Shop';
                    if (parts.length > 0 && parts[0].startsWith('@')) {
                        seller = parts[0].substring(1);
                    }
                }
                
                if (market) item.marketplace_name = market;
                if (seller) item.marketplace_seller = seller;
                
                if (product) {
                    product = product.replace(/\b\w/g, l => l.toUpperCase());
                    if (!item.product_code && !item.temp_name) {
                        item.temp_name = product;
                    }
                }
            } catch(e) {}
        },
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
