@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.service_repair'))
@section('content')
<div class="mx-auto max-w-2xl" x-data="repairForm()">
    <div class="card-feature p-6">
        <form method="POST" action="{{ route('service-repairs.store') }}" class="space-y-5" enctype="multipart/form-data">@csrf

            {{-- Customer Info --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.customer_name') }} *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required minlength="3" maxlength="100" class="input-field"><p class="mt-1 text-xs text-gray-400">3–100 karakter</p></div>
                <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="input-field" placeholder="081234567890" inputmode="numeric" minlength="7" maxlength="20"><p class="mt-1 text-xs text-gray-400">7–20 karakter</p></div>
                <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.notes') }}</label>
                    <textarea name="notes" rows="2" maxlength="500" class="input-field" placeholder="Catatan umum...">{{ old('notes') }}</textarea><p class="mt-1 text-xs text-gray-400">Maks. 500 karakter</p></div>
            </div>

            {{-- Damage Items --}}
            <div class="border-t border-[var(--color-hairline-soft)] pt-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="type-subtitle-lg text-[var(--color-ink-deep)]">Item Kerusakan *</h4>
                    <span class="text-xs text-[var(--color-slate)]">Bisa lebih dari 1 perangkat</span>
                </div>

                <template x-for="(item, idx) in items" :key="idx">
                    <div class="rounded-xl border border-[var(--color-hairline-soft)] p-4 mb-3 space-y-3 bg-[var(--color-surface-soft)]">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-[var(--color-ink)]">🔧 Item #<span x-text="idx+1"></span></p>
                            <button type="button" @click="removeItem(idx)" x-show="items.length > 1"
                                class="text-xs text-[var(--color-critical)] hover:bg-red-50 px-2 py-1 rounded">✕ Hapus</button>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Nama Perangkat / Item *</label>
                                <input type="text" :name="'items['+idx+'][name]'" required minlength="3" maxlength="100" class="input-field" placeholder="Contoh: Samsung Galaxy A15, LCD iPhone 13...">
                                <p class="mt-1 text-xs text-gray-400">3–100 karakter</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Merek</label>
                                <input type="text" :name="'items['+idx+'][brand]'" maxlength="60" class="input-field" placeholder="Samsung, Apple, dll...">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Seri / Model</label>
                                <input type="text" :name="'items['+idx+'][series]'" maxlength="60" class="input-field" placeholder="Galaxy A15, iPhone 13...">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Keluhan / Kerusakan *</label>
                                <textarea :name="'items['+idx+'][complaint]'" rows="2" required minlength="10" maxlength="1000" class="input-field !h-auto !py-2" placeholder="Tuliskan keluhan atau kerusakan yang dilaporkan pelanggan (min. 10 karakter)..."></textarea>
                                <p class="mt-1 text-xs text-gray-400">10–1000 karakter</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Biaya Jasa (opsional)</label>
                                <input type="text" :name="'items['+idx+'][service_fee]'" min="0" class="input-field input-rupiah" placeholder="0">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Bukti Foto Kerusakan Item (opsional)</label>
                                <input type="file" :name="'items['+idx+'][images][]'" multiple accept="image/*" class="input-field !py-2.5">
                                <p class="text-xs text-[var(--color-slate)] mt-1">Bisa unggah lebih dari satu gambar</p>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()"
                    class="rounded-xl border-2 border-dashed border-[var(--color-hairline)] px-4 py-3 text-sm text-[var(--color-slate)] hover:bg-[var(--color-surface-soft)] w-full transition-colors">
                    + Tambah Item Kerusakan
                </button>
            </div>

            <div class="flex gap-3 pt-2 justify-center">
                <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
                <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function repairForm() {
    return {
        items: [{ name: '', brand: '', series: '', complaint: '', service_fee: 0 }],
        addItem() { this.items.push({ name: '', brand: '', series: '', complaint: '', service_fee: 0 }); },
        removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); }
    }
}
</script>
@endpush
