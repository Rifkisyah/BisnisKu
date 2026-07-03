@extends('layouts.app')
@section('page-title', __('messages.pos'))
@section('content')
<div x-data="cashierSystem()" class="flex flex-col lg:flex-row gap-4 lg:gap-6 h-[calc(100vh-8rem)] lg:h-[calc(100vh-8rem)]">
    {{-- Product Grid --}}
    <div class="flex-1 flex flex-col min-w-0">
        <div class="flex flex-col sm:flex-row gap-3 mb-4 w-full">
            <input type="text" x-model="search" placeholder="{{ __('messages.search') }}..." class="input-field w-full" style="flex: 1 1 auto; min-width: 0;">
            <select x-model="categoryFilter" class="input-field w-full filter-w-responsive">
                <option value="">{{ __('messages.all') }}</option>
                @foreach($categories as $c)<option value="{{ $c->category_code }}">{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 content-start">
            <template x-for="product in filteredProducts" :key="product.product_code">
                <button @click="addToCart(product)" class="card p-4 text-left transition-all duration-150 hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md group"
                        :class="product.stock <= 0 ? 'opacity-50 pointer-events-none' : ''">
                    <div class="aspect-square rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)] mb-3 flex items-center justify-center">
                        <span class="aspect-square bg-gray-100 overflow-hidden"><img src="https://placehold.co/600x400?text=No+Image" class="w-full h-full object-cover"></span>
                    </div>
                    <p class="type-body-sm font-medium text-[var(--color-ink)] line-clamp-2" x-text="product.name"></p>
                    <p class="mt-1 type-subtitle-lg text-[var(--color-primary)]" x-text="formatRupiah(product.selling_price)"></p>
                    <p class="mt-1 type-caption text-[var(--color-steel)]">Stok: <span x-text="product.stock"></span></p>
                </button>
            </template>
        </div>
    </div>

    {{-- Mobile Overlay --}}
    <div x-show="mobileCartOpen" @click="mobileCartOpen = false" x-cloak class="fixed inset-0 bg-black/50 z-[60] lg:hidden transition-opacity"></div>

    {{-- Cart Sidebar --}}
    <div :class="mobileCartOpen ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'" 
         @click.outside="mobileCartOpen = false"
         class="fixed inset-x-0 bottom-0 z-[70] transition-transform duration-300 lg:static w-full lg:w-96 shrink-0 flex flex-col bg-[var(--color-canvas)] lg:bg-transparent shadow-[0_-10px_40px_rgba(0,0,0,0.1)] lg:shadow-none h-[85vh] lg:h-full rounded-t-[32px] lg:rounded-none lg:border-l border-[var(--color-hairline-soft)]">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex justify-between items-center bg-white lg:bg-transparent rounded-t-[32px] lg:rounded-none shrink-0">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]"> {{ __('messages.cart') }}</h3>
            <button @click="mobileCartOpen = false" class="lg:hidden w-8 h-8 flex items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-slate)] hover:bg-gray-200">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-3">
            <template x-if="cart.length === 0">
                <p class="text-center type-body-sm text-[var(--color-stone)] py-12">{{ __('messages.cart_empty') }}</p>
            </template>
            <template x-for="(item, idx) in cart" :key="idx">
                <div class="flex items-start gap-3 p-3 rounded-[var(--radius-xl)] bg-[var(--color-surface-soft)]">
                    <div class="flex-1 min-w-0">
                        <p class="type-body-sm font-medium text-[var(--color-ink)] truncate" x-text="item.name"></p>
                        <p class="type-caption text-[var(--color-steel)]" x-text="formatRupiah(item.selling_price)"></p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button @click="decrementQty(idx)" class="w-7 h-7 rounded-full bg-[var(--color-canvas)] border border-[var(--color-hairline)] flex items-center justify-center type-body-sm font-bold">-</button>
                        <span class="w-8 text-center type-body-sm font-bold" x-text="item.qty"></span>
                        <button @click="incrementQty(idx)" class="w-7 h-7 rounded-full bg-[var(--color-canvas)] border border-[var(--color-hairline)] flex items-center justify-center type-body-sm font-bold">+</button>
                    </div>
                    <button @click="removeFromCart(idx)" class="w-7 h-7 rounded-full flex items-center justify-center text-[var(--color-critical)] hover:bg-[var(--color-critical)]/10 type-body-sm">✕</button>
                </div>
            </template>
        </div>
        <div class="p-5 border-t border-[var(--color-hairline-soft)] space-y-3">
            <div class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.subtotal') }}</span><span class="font-medium" x-text="formatRupiah(subtotal)"></span></div>
            <div>
                <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.discount') }} (%)</label>
                <div class="relative">
                    <input type="number" x-model.number="discountPercent" min="0" max="100" class="input-field !h-9 mt-1 pr-8">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--color-slate)] mt-0.5">%</span>
                </div>
            </div>
            <div x-show="discountPercent > 0" class="flex justify-between type-body-sm text-[var(--color-critical)]">
                <span>Diskon (<span x-text="discountPercent"></span>%)</span>
                <span x-text="'-' + formatRupiah(discountAmount)"></span>
            </div>
            <div class="flex justify-between type-subtitle-lg"><span>{{ __('messages.total') }}</span><span class="text-[var(--color-primary)]" x-text="formatRupiah(total)"></span></div>
            <!-- Debt Checkbox -->
            <label class="flex items-center gap-2 cursor-pointer mt-4 p-3 bg-[var(--color-surface-soft)] rounded-[var(--radius-md)] border border-[var(--color-hairline-soft)]">
                <input type="checkbox" x-model="isDebt" @change="if(isDebt) { amountPaid = 0; paymentMethod = 'cash'; }" class="w-4 h-4 text-[var(--color-primary)] rounded focus:ring-[var(--color-primary)]">
                <span class="type-caption-bold text-[var(--color-ink)]">{{ __('messages.debt_record') }}</span>
            </label>

            <!-- Conditional Debt Inputs -->
            <template x-if="isDebt">
                <div class="space-y-3 mt-3 border-l-2 border-[var(--color-primary)] pl-3">
                    <div>
                        <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.customer_name') }} *</label>
                        <input type="text" x-model="debtorName" class="input-field !h-9 mt-1" placeholder="Nama Pelanggan">
                    </div>
                    <div>
                        <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.contact') }}</label>
                        <input type="text" x-model="debtorContact" class="input-field !h-9 mt-1" placeholder="08...">
                    </div>
                    <div>
                        <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.due_date') }}</label>
                        <input type="date" x-model="dueDate" class="input-field !h-9 mt-1">
                    </div>
                </div>
            </template>

            <div x-show="!isDebt" class="space-y-3 mt-3">
                <div>
                    <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.payment_method') }}</label>
                    <select x-model="paymentMethod" class="input-field !h-9 mt-1">
                        <option value="cash">{{ __('messages.cash') }}</option>
                        <option value="qris">QRIS (DANA / E-Wallet)</option>
                    </select>
                </div>
                
                <div x-show="paymentMethod === 'qris'" class="p-3 bg-blue-50 border border-blue-200 rounded-[var(--radius-md)] mt-2 mb-2 text-center" x-cloak>
                    <p class="type-caption text-blue-800 mb-2"><b>Scan QRIS DANA</b></p>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" alt="QRIS" class="w-32 h-32 mx-auto rounded-lg shadow-sm border border-blue-100">
                    <p class="type-caption text-blue-800 mt-2 text-[10px]">Silakan scan menggunakan DANA atau e-wallet lainnya.</p>
                </div>                
                <div x-show="paymentMethod === 'qris'" class="p-3 bg-purple-50 border border-purple-200 rounded-[var(--radius-md)] mt-2">
                    <p class="type-caption text-purple-800">{{ __('messages.qris_system_info') }}</p>
                </div>

                <div>
                    <label class="type-caption-bold text-[var(--color-slate)]">No. WA Pelanggan (Opsional)</label>
                    <input type="text" x-model="customerContact" class="input-field !h-9 mt-1" placeholder="08..." inputmode="numeric">
                </div>
                
                <div>
                    <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.amount_paid') }}</label>
                    <input type="text" inputmode="numeric" class="input-field !h-9 mt-1 input-rupiah" :value="formatInput(amountPaid)" @input="amountPaid = parseInput($event.target.value)">
                </div>
            </div>
            <div x-show="!isDebt && change > 0" class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.change') }}</span><span class="font-bold text-[var(--color-success)]" x-text="formatRupiah(change)"></span></div>
            <div x-show="isDebt" class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.debt') }}</span><span class="font-bold text-[var(--color-critical)]" x-text="formatRupiah(total)"></span></div>
            <button @click="processCheckout()" :disabled="cart.length === 0 || (!isDebt && amountPaid < total) || (isDebt && !debtorName.trim())"
                    class="btn-buy w-full !py-3.5 disabled:opacity-40 disabled:cursor-not-allowed">
                {{ __('messages.process_payment') }}
            </button>
        </div>
    </div>

    {{-- Mobile Bottom Bar --}}
    <div x-show="!mobileCartOpen && !mobileSidebar" class="fixed bottom-0 inset-x-0 z-[50] p-4 bg-white border-t border-[var(--color-hairline-soft)] shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.1)] lg:hidden flex justify-between items-center" x-transition>
        <div>
            <p class="type-caption-bold text-[var(--color-slate)] mb-0.5">Total <span x-text="cart.reduce((sum, item) => sum + item.qty, 0)"></span> Item</p>
            <p class="type-subtitle-lg text-[var(--color-primary)]" x-text="formatRupiah(total)"></p>
        </div>
        <button @click="mobileCartOpen = true" class="btn-primary !py-2.5 px-6 shadow-md rounded-[var(--radius-xl)] font-bold">
            Lihat Keranjang
        </button>
    </div>

    {{-- QRIS Payment Modal --}}
    <div x-show="showQris" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-[var(--color-ink-deep)]/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-md rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-none border border-[var(--color-hairline-soft)] text-center">
            
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-2">{{ __('messages.qris_payment') }}</h3>
            <p class="type-caption-bold text-[var(--color-primary)] mb-6 text-lg" x-text="formatRupiah(receiptTotal)"></p>
            
            <div class="bg-white p-4 rounded-xl inline-block border border-gray-200 mb-6 shadow-sm">
                <img :src="qrisData?.qris_url" alt="QRIS" class="w-48 h-48 object-cover">
            </div>

            <div x-show="qrisData?.qris_mode === 'manual'" class="space-y-3">
                <p class="type-caption text-[var(--color-slate)] mb-4">{{ __('messages.qris_manual_info') }}</p>
                <button @click="confirmManualQris()" :disabled="isLoading" class="btn-primary w-full !py-3">
                    <span x-text="isLoading ? 'Memproses...' : 'Konfirmasi Pembayaran (Lunas)'"></span>
                </button>
            </div>

            <div x-show="qrisData?.qris_mode === 'dynamic'" class="space-y-3">
                <p class="type-caption text-[var(--color-slate)] mb-4">{!! __('messages.qris_dynamic_info') !!}</p>
                <button @click="checkDynamicQris()" :disabled="isLoading" class="btn-primary w-full !py-3">
                    <span x-text="isLoading ? 'Mengecek...' : 'Cek Status Pembayaran'"></span>
                </button>
            </div>

            <button @click="cancelQris()" :disabled="isLoading" class="btn-ghost w-full !py-3 mt-3 text-[var(--color-critical)]">{{ __('messages.cancel_payment') }}</button>
        </div>
    </div>

    {{-- Receipt Modal --}}
    <div x-show="showReceipt" x-cloak class="fixed inset-0 z-[95] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-[var(--color-ink-deep)]/60 backdrop-blur-sm" @click="showReceipt = false; resetCart()"></div>
        <div class="relative w-full max-w-md rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-none border border-[var(--color-hairline-soft)]" x-transition>
            
            <!-- Success Header -->
            <div class="flex flex-col items-center mb-6">
                <div class="w-14 h-14 rounded-full bg-[var(--color-success)]/10 text-[var(--color-success)] flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="type-heading-sm text-center text-[var(--color-ink-deep)]">{{ __('messages.transaction_success') }}</h3>
                <p class="type-body-sm text-[var(--color-slate)] mt-1 font-medium" x-text="'# ' + receiptCode"></p>
            </div>

            <!-- Receipt Details -->
            <div class="bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] p-5 mb-6">
                <!-- Items list -->
                <div class="space-y-3 mb-4 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="item in cart" :key="item.product_code">
                        <div class="flex justify-between type-body-sm">
                            <div class="flex-1 pr-4">
                                <p class="text-[var(--color-ink)] font-medium line-clamp-1" x-text="item.name"></p>
                                <p class="text-[var(--color-slate)]" x-text="item.qty + ' x ' + formatRupiah(item.selling_price)"></p>
                            </div>
                            <div class="text-[var(--color-ink)] font-medium text-right whitespace-nowrap" x-text="formatRupiah(item.qty * item.selling_price)"></div>
                        </div>
                    </template>
                </div>
                
                <!-- Divider -->
                <div class="h-px w-full border-t border-dashed border-[var(--color-hairline-soft)] mb-4"></div>
                
                <!-- Totals -->
                <div class="space-y-2 type-body-sm">
                    <div class="flex justify-between">
                        <span class="text-[var(--color-slate)]">{{ __('messages.subtotal') }}</span>
                        <span class="text-[var(--color-ink)] font-medium" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between" x-show="discountPercent > 0">
                        <span class="text-[var(--color-slate)]">Diskon (<span x-text="discountPercent"></span>%)</span>
                        <span class="text-[var(--color-critical)] font-medium" x-text="'-' + formatRupiah(discountAmount)"></span>
                    </div>
                    <div class="flex justify-between type-subtitle-lg pt-3 mt-1 border-t border-[var(--color-hairline-soft)]">
                        <span class="text-[var(--color-ink-deep)]">{{ __('messages.total') }}</span>
                        <span class="text-[var(--color-primary)]" x-text="formatRupiah(receiptTotal)"></span>
                    </div>
                    <div class="flex justify-between pt-3 mt-1">
                        <span class="text-[var(--color-slate)]">{{ __('messages.amount_paid') }} (<span class="uppercase" x-text="paymentMethod"></span>)</span>
                        <span class="text-[var(--color-ink)] font-medium" x-text="formatRupiah(amountPaid)"></span>
                    </div>
                    <div class="flex justify-between" x-show="change > 0">
                        <span class="text-[var(--color-slate)]">{{ __('messages.change') }}</span>
                        <span class="text-[var(--color-success)] font-bold" x-text="formatRupiah(change)"></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                    <button @click="showReceipt = false; resetCart()" class="btn-ghost flex-1 !py-3">{{ __('messages.close') }}</button>
                    <a :href="receiptUrl" target="_blank" class="btn-primary flex-1 !py-3 text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        {{ __('messages.print_receipt') }}
                    </a>
                </div>
                <template x-if="waSent">
                    <div class="bg-green-100 text-green-800 rounded-[var(--radius-xl)] font-bold type-body-sm w-full !py-3 text-center flex items-center justify-center gap-2 border border-green-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Struk Otomatis Terkirim ke WA
                    </div>
                </template>
                <template x-if="!waSent">
                    <a :href="'https://wa.me/' + ((isDebt ? debtorContact : customerContact).replace(/\D/g, '')) + '?text=' + encodeURIComponent('Halo, ini struk belanja Anda di {{ \App\Models\Setting::get('store_name', 'Toko Kami') }}: ' + receiptUrl)" target="_blank" class="bg-green-500 text-white hover:bg-green-600 rounded-[var(--radius-xl)] font-bold type-body-sm w-full !py-3 text-center flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12C2 13.91 2.54 15.7 3.46 17.2L2.09 21.9L6.96 20.62C8.47 21.5 10.2 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM17.15 15.86C16.92 16.51 15.93 17.07 15.22 17.21C14.65 17.32 13.88 17.43 11.23 16.33C7.83 14.93 5.6 11.45 5.43 11.23C5.26 11 4.02 9.35 4.02 7.64C4.02 5.92 4.9 5.08 5.25 4.73C5.55 4.43 6.03 4.3 6.46 4.3C6.6 4.3 6.72 4.31 6.83 4.31C7.14 4.33 7.3 4.34 7.53 4.88C7.81 5.57 8.5 7.25 8.58 7.42C8.67 7.59 8.78 7.82 8.65 8.08C8.52 8.35 8.44 8.5 8.24 8.74C8.04 8.98 7.82 9.17 7.65 9.4C7.45 9.61 7.23 9.84 7.46 10.23C7.69 10.61 8.5 11.93 9.7 13.01C11.25 14.4 12.53 14.84 12.95 15.02C13.37 15.2 13.86 15.17 14.15 14.86C14.52 14.45 15.02 13.73 15.51 12.98C15.86 12.44 16.27 12.51 16.66 12.65C17.05 12.79 19.11 13.81 19.5 14.01C19.9 14.21 20.16 14.31 20.26 14.48C20.35 14.66 20.35 15.21 17.15 15.86Z"/></svg>
                        <span x-text="waError ? 'Gagal Kirim (Kirim Manual)' : 'Kirim WA (Manual)'"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function cashierSystem() {
    return {
        products: @json($products),
        mobileCartOpen: false,
        cart: [], search: '', categoryFilter: '', discountPercent: 0, paymentMethod: 'cash', amountPaid: 0, debtorName: '', debtorContact: '', customerContact: '', dueDate: '', isDebt: false,
        showReceipt: false, receiptCode: '', receiptTotal: 0, receiptUrl: '', waSent: false, waError: null,
        showQris: false, qrisData: null, isLoading: false,
        get filteredProducts() {
            return this.products.filter(p => {
                const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) || p.product_code.toLowerCase().includes(this.search.toLowerCase());
                const matchCategory = !this.categoryFilter || p.category_code == this.categoryFilter;
                return matchSearch && matchCategory;
            });
        },
        get subtotal() { return this.cart.reduce((s, i) => s + (i.selling_price * i.qty), 0); },
        get discountAmount() { return (this.subtotal * (this.discountPercent || 0)) / 100; },
        get total() { return Math.max(0, this.subtotal - this.discountAmount); },
        get change() { return Math.max(0, this.amountPaid - this.total); },
        formatInput(val) { if (!val) return ''; return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); },
        parseInput(val) { if (!val) return 0; return parseInt(val.toString().replace(/\./g, '')) || 0; },
        addToCart(product) {
            const existing = this.cart.find(i => i.product_code === product.product_code);
            if (existing) { if (existing.qty < product.stock) existing.qty++; }
            else this.cart.push({ ...product, qty: 1 });
        },
        incrementQty(idx) { const item = this.cart[idx]; const product = this.products.find(p => p.product_code === item.product_code); if (item.qty < product.stock) item.qty++; },
        decrementQty(idx) { if (this.cart[idx].qty > 1) this.cart[idx].qty--; else this.cart.splice(idx, 1); },
        removeFromCart(idx) { this.cart.splice(idx, 1); },
        resetCart() { this.cart = []; this.discountPercent = 0; this.amountPaid = 0; this.debtorName = ''; this.debtorContact = ''; this.customerContact = ''; this.dueDate = ''; this.isDebt = false; this.mobileCartOpen = false; },
        async processCheckout() {
            const res = await fetch('{{ route("cashier.checkout") }}', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ items: this.cart.map(i => ({ product_code: i.product_code, quantity: i.qty, unit_price: i.selling_price })), discount: this.discountAmount, payment_method: this.isDebt ? 'debt' : this.paymentMethod, amount_paid: this.isDebt ? 0 : this.amountPaid, debtor_name: this.debtorName, debtor_contact: this.debtorContact, customer_contact: this.customerContact, due_date: this.dueDate })
            });
            try {
                const data = await res.json();
                if (res.ok) { 
                    this.receiptCode = data.transaction_code; this.receiptTotal = data.total; this.receiptUrl = data.receipt_url; 
                    this.waSent = data.wa_sent || false; this.waError = data.wa_error || null;
                    
                    if (data.qris_data) {
                        this.qrisData = data.qris_data;
                        this.showQris = true;
                    } else {
                        this.showReceipt = true;
                        this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                    }
                } else { 
                    alert(data.message || 'Error processing transaction'); 
                }
            } catch (e) {
                alert('Terjadi kesalahan pada server');
            }
        },
        async confirmManualQris() {
            if(!this.qrisData) return;
            this.isLoading = true;
            try {
                const res = await fetch(`/payments/${this.qrisData.payment_code}/confirm-manual`, {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if(res.ok) {
                    this.showQris = false;
                    this.showReceipt = true;
                    this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                } else {
                    alert(data.message || 'Gagal mengkonfirmasi pembayaran');
                }
            } catch(e) { alert('Terjadi kesalahan'); }
            finally { this.isLoading = false; }
        },
        async checkDynamicQris() {
            if(!this.qrisData) return;
            this.isLoading = true;
            try {
                const res = await fetch(`/payments/${this.qrisData.payment_code}/check-status`, {
                    method: 'GET', headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if(data.status === 'paid') {
                    this.showQris = false;
                    this.showReceipt = true;
                    this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                } else {
                    alert(data.message || 'Pembayaran masih tertunda');
                }
            } catch(e) { alert('Terjadi kesalahan'); }
            finally { this.isLoading = false; }
        },
        async cancelQris() {
            if(!this.qrisData) return;
            if(!confirm('Yakin ingin membatalkan pembayaran ini?')) return;
            this.isLoading = true;
            try {
                const res = await fetch(`/payments/${this.qrisData.payment_code}/cancel`, {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if(res.ok) {
                    this.showQris = false;
                    // Dont show receipt, don't deduct stock, just alert
                    alert('Pembayaran dibatalkan');
                    // Reset modal 
                    this.showQris = false;
                } else {
                    alert('Gagal membatalkan pembayaran');
                }
            } catch(e) { alert('Terjadi kesalahan'); }
            finally { this.isLoading = false; }
        }
    }
}
</script>
@endpush
