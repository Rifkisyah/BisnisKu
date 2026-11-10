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
                        <span class="aspect-square bg-gray-100 overflow-hidden"><img :src="product.image ? '{{ asset('storage') }}/' + product.image : 'https://placehold.co/600x400?text=No+Image'" class="w-full h-full object-cover"></span>
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
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex justify-between items-center bg-[var(--color-canvas)] lg:bg-transparent rounded-t-[32px] lg:rounded-none shrink-0">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]"> {{ __('messages.cart') }}</h3>
            <button @click="mobileCartOpen = false" class="lg:hidden w-8 h-8 flex items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-slate)] hover:bg-[var(--color-surface-muted)]">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-3 lg:min-h-[20vh]">
            <div x-show="cart.length === 0" class="text-center type-body-sm text-[var(--color-stone)] py-12">
                {{ __('messages.cart_empty') }}
            </div>
            <template x-for="(item, idx) in cart" :key="item.product_code + '_' + idx">
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
        <div class="p-5 border-t border-[var(--color-hairline-soft)] lg:flex lg:flex-col shrink-0 lg:max-h-[55vh] space-y-3 lg:space-y-0">
            <div class="lg:overflow-y-auto lg:space-y-3 lg:pr-2 lg:custom-scrollbar lg:flex-1 space-y-3">
                <div class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.subtotal') }}</span><span class="font-medium" x-text="formatRupiah(subtotal)"></span></div>
                <div>
                    <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.discount') }} (%)</label>
                    <div class="relative">
                        <input type="number" x-model.number="discountPercent" @input="if(discountPercent > 100) discountPercent = 100; if(discountPercent < 0) discountPercent = 0;" min="0" max="100" class="input-field !h-9 mt-1 pr-8">
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
                            <option value="qris">Digital Payment (Transfer & QRIS)</option>
                        </select>
                    </div>
                    
                    @if($paymentSetting->qris_mode === 'dynamic')
                    {{-- Mode Dinamis: Midtrans Snap aktif --}}
                    <div x-show="paymentMethod === 'qris'" class="p-3 bg-blue-50 border border-blue-200 rounded-[var(--radius-md)] mt-2 mb-2" x-cloak>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="type-caption-bold text-blue-800">Pembayaran Digital Aktif</p>
                                <p class="type-caption text-blue-700 mt-0.5">Popup pembayaran (Midtrans) akan muncul setelah klik <strong>Proses Pembayaran</strong>. Mendukung Transfer Bank (Virtual Account) dan QRIS (Gopay/DANA/OVO/dll).</p>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Mode Manual: tampilkan gambar QRIS statis yang diupload --}}
                    <div x-show="paymentMethod === 'qris'" class="p-3 bg-blue-50 border border-blue-200 rounded-[var(--radius-md)] mt-2 mb-2 text-center" x-cloak>
                        <p class="type-caption text-blue-800 mb-2"><b>Scan QRIS</b></p>
                        <img src="{{ !empty($paymentSetting->manual_qris_image) ? asset($paymentSetting->manual_qris_image) : 'https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg' }}" alt="QRIS" class="w-32 h-32 mx-auto rounded-lg shadow-sm border border-blue-100 object-cover">
                        @if(empty($paymentSetting->manual_qris_image))
                            <p class="type-caption text-red-600 mt-2 text-[10px]">* Gambar QRIS belum diatur di Pengaturan Pembayaran.</p>
                        @endif
                        <p class="type-caption text-blue-800 mt-2 text-[10px]">Silakan scan menggunakan aplikasi E-Wallet pelanggan.</p>
                    </div>
                    @endif
                    
                    <div x-show="paymentMethod === 'qris'" class="p-3 bg-purple-50 border border-purple-200 rounded-[var(--radius-md)] mt-2" x-cloak>
                        <p class="type-caption text-purple-800">{{ __('messages.qris_system_info') }}</p>
                    </div>


                    <div x-show="paymentMethod === 'cash'">
                        <label class="type-caption-bold text-[var(--color-slate)]">{{ __('messages.amount_paid') }}</label>
                        <input type="text" inputmode="numeric" class="input-field !h-9 mt-1 input-rupiah" :value="formatInput(amountPaid)" @input="amountPaid = parseInput($event.target.value)">
                    </div>
                </div>
                <div x-show="!isDebt && paymentMethod === 'cash' && change >= 0" class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.change') }}</span><span class="font-bold text-[var(--color-success)]" x-text="formatRupiah(change)"></span></div>
                <div x-show="isDebt" class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.debt') }}</span><span class="font-bold text-[var(--color-critical)]" x-text="formatRupiah(total)"></span></div>
            </div>
            
            <div class="lg:pt-3 lg:border-t lg:border-[var(--color-hairline-soft)] lg:mt-1 shrink-0">
                <button @click="processCheckout()" :disabled="cart.length === 0 || (!isDebt && paymentMethod === 'cash' && amountPaid < total) || (isDebt && !debtorName.trim())"
                        class="btn-buy w-full !py-3.5 disabled:opacity-40 disabled:cursor-not-allowed">
                    {{ __('messages.process_payment') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Bottom Bar --}}
    <div x-show="!mobileCartOpen && !mobileSidebar" class="fixed bottom-0 inset-x-0 z-[50] p-4 bg-[var(--color-canvas)] border-t border-[var(--color-hairline-soft)] shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.1)] lg:hidden flex justify-between items-center" x-transition>
        <div>
            <p class="type-caption-bold text-[var(--color-slate)] mb-0.5">Total <span x-text="cart.reduce((sum, item) => sum + item.qty, 0)"></span> Item</p>
            <p class="type-subtitle-lg text-[var(--color-primary)]" x-text="formatRupiah(total)"></p>
        </div>
        <button @click="mobileCartOpen = true" class="btn-primary !py-2.5 px-6 shadow-md rounded-[var(--radius-xl)] font-bold">
            Lihat Keranjang
        </button>
    </div>

    {{-- QRIS Payment Modal --}}
    <div x-show="showQris" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         x-init="$watch('showQris', val => { if (val && qrisData?.qris_mode === 'dynamic') startQrisPolling(); else stopQrisPolling(); })">
        <div class="fixed inset-0 bg-[var(--color-ink-deep)]/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-md rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-none border border-[var(--color-hairline-soft)] text-center">

            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-1">{{ __('messages.qris_payment') }}</h3>
            <p class="type-caption-bold text-[var(--color-primary)] mb-4 text-lg" x-text="formatRupiah(receiptTotal)"></p>

            {{-- Countdown Timer (hanya untuk dynamic) --}}
            <div x-show="qrisData?.qris_mode === 'dynamic' && qrisCountdown > 0"
                 class="mb-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium"
                 :class="qrisCountdown < 120 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="'Berlaku: ' + Math.floor(qrisCountdown/60) + ':' + String(Math.floor(qrisCountdown % 60)).padStart(2,'0')"></span>
            </div>

            {{-- QRIS Image --}}
            <div class="bg-white p-4 rounded-xl inline-block border border-gray-200 mb-6 shadow-sm relative">
                <img :src="qrisData?.qris_url" alt="QRIS" class="w-52 h-52 object-contain"
                     :class="qrisPollingStatus === 'checking' ? 'opacity-60' : ''">
                {{-- Scanning overlay --}}
                <div x-show="qrisPollingStatus === 'checking'"
                     class="absolute inset-0 flex items-center justify-center rounded-xl bg-white/70">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="type-caption text-[var(--color-slate)] text-xs">Mengecek pembayaran...</p>
                    </div>
                </div>
            </div>

            {{-- Auto-polling status --}}
            <div x-show="qrisData?.qris_mode === 'dynamic'" class="mb-4">
                <p class="type-caption text-[var(--color-slate)]">
                    Scan QR di atas menggunakan aplikasi dompet digital / m-banking apapun yang mendukung QRIS.
                    Pembayaran akan terkonfirmasi otomatis.
                </p>
                <div x-show="qrisPollingActive" class="mt-2 inline-flex items-center gap-1.5 text-xs text-[var(--color-primary)]">
                    <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-pulse"></span>
                    <span>Polling otomatis setiap 5 detik...</span>
                </div>
            </div>

            <div x-show="qrisData?.qris_mode === 'manual'" class="space-y-3">
                <p class="type-caption text-[var(--color-slate)] mb-4">{{ __('messages.qris_manual_info') }}</p>
                <button @click="confirmManualQris()" :disabled="isLoading" class="btn-primary w-full !py-3">
                    <span x-text="isLoading ? 'Memproses...' : 'Konfirmasi Pembayaran (Lunas)'"></span>
                </button>
            </div>

            <div x-show="qrisData?.qris_mode === 'dynamic'" class="space-y-3 mt-2">
                <button @click="checkDynamicQris()" :disabled="isLoading || qrisPollingActive" class="btn-ghost w-full !py-2.5 text-sm">
                    <span x-text="isLoading ? 'Mengecek...' : 'Cek Status Manual'"></span>
                </button>
            </div>

            <button @click="cancelQris()" :disabled="isLoading" class="btn-ghost w-full !py-3 mt-2 text-[var(--color-critical)]">{{ __('messages.cancel_payment') }}</button>
        </div>
    </div>

    {{-- Cancel Confirmation Modal --}}
    <div x-show="showCancelConfirm" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-[var(--color-ink-deep)]/60 backdrop-blur-sm" @click="showCancelConfirm = false"></div>
        <div class="relative w-full max-w-sm rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-none border border-[var(--color-hairline-soft)] text-center" x-transition>
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-2">Batalkan Pembayaran?</h3>
            <p class="type-caption text-[var(--color-slate)] mb-6">Apakah kamu yakin ingin membatalkan transaksi ini?</p>
            
            <div class="flex gap-3">
                <button @click="showCancelConfirm = false" :disabled="isLoading" class="btn-ghost flex-1 !py-3">Tutup</button>
                <button @click="processCancelQris()" :disabled="isLoading" class="btn-primary !bg-[var(--color-critical)] !text-white flex-1 !py-3 border-none hover:opacity-90 shadow-md">
                    <span x-text="isLoading ? 'Memproses...' : 'Ya, Batalkan'"></span>
                </button>
            </div>
        </div>
    </div>
    {{-- Receipt Modal --}}
    <div x-show="showReceipt" x-cloak class="fixed inset-0 z-[95] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-[var(--color-ink-deep)]/60 backdrop-blur-sm" @click="showReceipt = false; resetCart()"></div>
        <div class="relative w-full max-w-md rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-none border border-[var(--color-hairline-soft)]" x-transition>
            
            <!-- Dynamic Header based on status -->
            <div class="flex flex-col items-center mb-6">
                <!-- Success Icon -->
                <div x-show="receiptStatus === 'success'" class="w-14 h-14 rounded-full bg-[var(--color-success)]/10 text-[var(--color-success)] flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <!-- Pending Icon -->
                <div x-show="receiptStatus === 'pending'" class="w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>

                <h3 class="type-heading-sm text-center text-[var(--color-ink-deep)]" x-text="receiptStatus === 'success' ? '{{ __('messages.transaction_success') }}' : 'Transaksi Tertunda'"></h3>
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
                    <div class="flex justify-between pt-3 mt-1" x-show="paymentMethod === 'cash'">
                        <span class="text-[var(--color-slate)]">{{ __('messages.amount_paid') }} (<span class="uppercase" x-text="paymentMethod"></span>)</span>
                        <span class="text-[var(--color-ink)] font-medium" x-text="formatRupiah(amountPaid)"></span>
                    </div>
                    <div class="flex justify-between" x-show="paymentMethod === 'cash' && change >= 0">
                        <span class="text-[var(--color-slate)]">{{ __('messages.change') }}</span>
                        <span class="text-[var(--color-success)] font-bold" x-text="formatRupiah(change)"></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                    <button @click="showReceipt = false; resetCart()" class="btn-ghost flex-1 !py-3">{{ __('messages.close') }}</button>
                    <a x-show="receiptStatus === 'success'" :href="receiptUrl" target="_blank" class="btn-primary flex-1 !py-3 text-center flex items-center justify-center gap-2">
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
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@if(!empty($paymentSetting->client_key))
<script src="{{ $paymentSetting->is_production ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $paymentSetting->client_key }}"></script>
@endif
<style>
/* SweetAlert2 overrides */
.swal2-custom-popup {
    border-radius: var(--radius-xxxl, 32px) !important;
    font-family: inherit !important;
    padding: 24px !important;
    background: var(--color-canvas) !important;
    color: var(--color-ink-deep) !important;
    border: 1px solid var(--color-hairline-soft) !important;
}
.swal2-custom-confirm {
    background-color: var(--color-primary, #2563EB) !important;
    color: white !important;
    border-radius: 100px !important;
    padding: 12px 28px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    border: none !important;
    letter-spacing: -0.14px !important;
    margin: 0 8px !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25) !important;
}
.swal2-custom-cancel {
    background-color: transparent !important;
    color: var(--color-ink-deep) !important;
    border-radius: 100px !important;
    padding: 12px 28px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    border: 1.5px solid var(--color-hairline) !important;
    letter-spacing: -0.14px !important;
    margin: 0 8px !important;
}
</style>
<script>
// Override default Swal for this page to match DESIGN.md
const StyledSwal = Swal.mixin({
    customClass: {
        popup: 'swal2-custom-popup',
        confirmButton: 'swal2-custom-confirm',
        cancelButton: 'swal2-custom-cancel'
    },
    buttonsStyling: false
});
// Override window.Swal so we don't have to change all Swal.fire calls
window.Swal = StyledSwal;
</script>
<script>
function cashierSystem() {
    return {
        products: @json($products),
        mobileCartOpen: false,
        cart: [], search: '', categoryFilter: '', discountPercent: 0, paymentMethod: 'cash', amountPaid: 0, debtorName: '', debtorContact: '', customerContact: '', dueDate: '', isDebt: false,
        showReceipt: false, receiptStatus: 'success', receiptCode: '', receiptTotal: 0, receiptUrl: '', waSent: false, waError: null,
        showQris: false, qrisData: null, isLoading: false,
        qrisPollingActive: false, qrisPollingStatus: 'idle', qrisCountdown: 0, showCancelConfirm: false,
        _qrisPollingInterval: null, _qrisCountdownInterval: null,
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
        resetCart() { this.cart = []; this.discountPercent = 0; this.amountPaid = 0; this.debtorName = ''; this.debtorContact = ''; this.customerContact = ''; this.dueDate = ''; this.isDebt = false; this.mobileCartOpen = false; this.receiptStatus = 'success'; },
        async processCheckout() {
            this.receiptStatus = 'success';
            const res = await fetch('{{ route("cashier.checkout") }}', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ items: this.cart.map(i => ({ product_code: i.product_code, quantity: i.qty, unit_price: i.selling_price })), discount: this.discountAmount, payment_method: this.isDebt ? 'debt' : this.paymentMethod, amount_paid: this.isDebt ? 0 : (this.paymentMethod === 'cash' ? this.amountPaid : this.total), debtor_name: this.debtorName, debtor_contact: this.debtorContact, customer_contact: this.customerContact, due_date: this.dueDate })
            });
            try {
                const data = await res.json();
                if (res.ok) { 
                    this.receiptCode = data.transaction_code; this.receiptTotal = data.total; this.receiptUrl = data.receipt_url; 
                    this.waSent = data.wa_sent || false; this.waError = data.wa_error || null;
                    
                    if (data.qris_data) {
                        this.qrisData = data.qris_data;
                        if (data.qris_data.snap_token) {
                            if (typeof window.snap === 'undefined') {
                                Swal.fire({title: 'Oops!', text: 'Midtrans Snap tidak tersedia. Pastikan Client Key sudah diatur.', icon: 'error'});
                                return;
                            }
                            window.snap.pay(data.qris_data.snap_token, {
                                onSuccess: (result) => {
                                    this.confirmDigitalPayment(data.qris_data.payment_code, result);
                                },
                                onPending: (result) => {
                                    Swal.fire({title: 'Dibatalkan', text: 'Pembayaran belum diselesaikan. Transaksi dibatalkan.', icon: 'warning'}).then(() => {
                                        this.cancelTransaction(data.qris_data.payment_code);
                                    });
                                },
                                onError: (result) => {
                                    Swal.fire({title: 'Gagal', text: 'Pembayaran gagal. Transaksi dibatalkan.', icon: 'error'}).then(() => {
                                        this.cancelTransaction(data.qris_data.payment_code);
                                    });
                                },
                                onClose: () => {
                                    Swal.fire({title: 'Dibatalkan', text: 'Anda menutup popup pembayaran. Transaksi dibatalkan.', icon: 'warning'}).then(() => {
                                        this.cancelTransaction(data.qris_data.payment_code);
                                    });
                                }
                            });
                        } else {
                            this.showQris = true;
                        }
                    } else {
                        this.showReceipt = true;
                        this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                    }
                } else { 
                    if (data.needs_config) {
                        Swal.fire({
                            title: 'Pengaturan Belum Lengkap',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Buka Pengaturan',
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("settings.payment") }}';
                            }
                        });
                    } else {
                        Swal.fire({title: 'Oops!', text: data.message || 'Error processing transaction', icon: 'error'}); 
                    }
                }
            } catch (e) {
                Swal.fire({title: 'Error', text: 'Terjadi kesalahan pada server', icon: 'error'});
            }
        },
        async confirmDigitalPayment(paymentCode, snapResult) {
            this.isLoading = true;
            try {
                // Konfirmasi manual jika webhook telat, atau bisa diarahkan ke check-status
                const res = await fetch(`/payments/${paymentCode}/check-status`, {
                    method: 'GET', headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if(data.status === 'paid' || data.success) { // anggap paid jika success
                    this.showReceipt = true;
                    this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                } else {
                    // Fallback
                    this.showReceipt = true;
                    this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                }
            } catch(e) {
                this.showReceipt = true;
                this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
            } finally {
                this.isLoading = false;
            }
        },
        async cancelTransaction(paymentCode) {
            this.isLoading = true;
            try {
                const res = await fetch(`/payments/${paymentCode}/cancel`, {
                    method: 'POST', 
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                await res.json();
            } catch(e) {
                console.error(e);
            } finally {
                this.isLoading = false;
                this.qrisData = null;
                this.cart = []; // clear cart since it's cancelled
                this.calculateTotal();
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
                    this.stopQrisPolling();
                    this.showQris = false;
                    this.showReceipt = true;
                    this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                } else {
                    Swal.fire({title: 'Gagal', text: data.message || 'Gagal mengkonfirmasi pembayaran', icon: 'error'});
                }
            } catch(e) { Swal.fire({title: 'Error', text: 'Terjadi kesalahan', icon: 'error'}); }
            finally { this.isLoading = false; }
        },
        async checkDynamicQris() {
            if(!this.qrisData) return;
            this.isLoading = true;
            this.qrisPollingStatus = 'checking';
            try {
                const res = await fetch(`/payments/${this.qrisData.payment_code}/check-status`, {
                    method: 'GET', headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if(data.status === 'paid') {
                    this.stopQrisPolling();
                    this.showQris = false;
                    this.showReceipt = true;
                    this.cart.forEach(ci => { const p = this.products.find(pp => pp.product_code === ci.product_code); if (p) p.stock -= ci.qty; });
                } else if (data.status === 'expired' || data.status === 'failed') {
                    this.stopQrisPolling();
                    Swal.fire({title: 'Info', text: data.message || 'Pembayaran ' + data.status, icon: 'info'});
                    this.showQris = false;
                }
                // Jika masih pending, update countdown dari server
                if (data.remaining_seconds !== undefined) {
                    this.qrisCountdown = Math.floor(data.remaining_seconds);
                }
            } catch(e) { /* silent fail untuk auto-polling */ }
            finally { this.isLoading = false; this.qrisPollingStatus = 'idle'; }
        },
        startQrisPolling() {
            this.stopQrisPolling(); // Clear existing intervals

            // Set countdown dari expiry_time
            if (this.qrisData?.expiry_time) {
                const expiryMs = new Date(this.qrisData.expiry_time).getTime();
                this.qrisCountdown = Math.max(0, Math.floor((expiryMs - Date.now()) / 1000));

                this._qrisCountdownInterval = setInterval(() => {
                    this.qrisCountdown = Math.max(0, this.qrisCountdown - 1);
                    if (this.qrisCountdown <= 0) {
                        this.stopQrisPolling();
                        this.showQris = false;
                        Swal.fire({title: 'Kedaluwarsa', text: 'QRIS sudah kedaluwarsa. Silakan buat transaksi baru.', icon: 'warning'});
                    }
                }, 1000);
            }

            // Auto-poll ke server setiap 5 detik
            this.qrisPollingActive = true;
            this._qrisPollingInterval = setInterval(() => {
                if (!this.isLoading && this.showQris) {
                    this.checkDynamicQris();
                }
            }, 5000);

            // Cek pertama setelah 3 detik
            setTimeout(() => { if (this.showQris) this.checkDynamicQris(); }, 3000);
        },
        stopQrisPolling() {
            this.qrisPollingActive = false;
            this.qrisPollingStatus = 'idle';
            if (this._qrisPollingInterval) { clearInterval(this._qrisPollingInterval); this._qrisPollingInterval = null; }
            if (this._qrisCountdownInterval) { clearInterval(this._qrisCountdownInterval); this._qrisCountdownInterval = null; }
        },
        cancelQris() {
            if(!this.qrisData) return;
            this.showCancelConfirm = true;
        },
        async processCancelQris() {
            this.isLoading = true;
            try {
                const res = await fetch(`/payments/${this.qrisData.payment_code}/cancel`, {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if(res.ok) {
                    this.stopQrisPolling();
                    this.showQris = false;
                    this.showCancelConfirm = false;
                    Swal.fire({title: 'Berhasil', text: 'Pembayaran berhasil dibatalkan.', icon: 'success', timer: 2000, showConfirmButton: false});
                } else {
                    Swal.fire({title: 'Gagal', text: 'Gagal membatalkan pembayaran', icon: 'error'});
                }
            } catch(e) { Swal.fire({title: 'Error', text: 'Terjadi kesalahan', icon: 'error'}); }
            finally { this.isLoading = false; }
        }
    }
}
</script>
@endpush
