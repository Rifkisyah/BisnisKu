@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.product'))
@section('content')
<div class="mx-auto max-w-2xl"><div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm">
    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">@csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label><input type="text" name="name" value="{{ old('name') }}" required minlength="3" maxlength="100" class="input-field"><p class="mt-1 text-xs text-gray-400">3–100 karakter</p></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.category') }} *</label>
                <select name="category_code" required class="input-field"><option value="">{{ __('messages.select_category') }}</option>@foreach($categories as $c)<option value="{{ $c->category_code }}" {{ old('category_code') == $c->category_code ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Supplier</label>
                <select name="supplier_code" class="input-field"><option value="">{{ __('messages.select_supplier') }}</option>@foreach($suppliers as $s)<option value="{{ $s->supplier_code }}" {{ old('supplier_code') == $s->supplier_code ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.purchase_price') }} *</label><input type="text" name="purchase_price" value="{{ old('purchase_price', 0) }}" required min="0" class="input-field input-rupiah"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.selling_price') }} *</label><input type="text" name="selling_price" value="{{ old('selling_price', 0) }}" required min="0" class="input-field input-rupiah"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.minimum_stock') }} *</label><input type="number" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" required min="0" class="input-field"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Satuan</label><input type="text" name="unit" value="{{ old('unit', 'pcs') }}" placeholder="pcs, unit, set, kg..." minlength="1" maxlength="20" class="input-field"><p class="mt-1 text-xs text-gray-400">Maks. 20 karakter</p></div>
            <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.description') }}</label><textarea name="description" rows="3" maxlength="500" class="input-field !h-auto !py-3">{{ old('description') }}</textarea><p class="mt-1 text-xs text-gray-400">Maks. 500 karakter</p></div>
            <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.image') }}</label>
                <input type="file" name="image" accept="image/*" class="input-field !py-2 file:mr-3 file:rounded-[var(--radius-full)] file:border-0 file:bg-[var(--color-primary-soft)] file:px-3 file:py-1 file:type-caption-bold file:text-[var(--color-primary)]"></div>
        </div>
        {{-- Info: stok tidak diisi saat membuat produk --}}
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 flex gap-2">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <span><strong>Stok awal = 0.</strong> Stok hanya bertambah melalui <a href="{{ route('product-purchases.create') }}" class="underline font-semibold">Pengadaan</a> yang berstatus Received. Produk ini akan tersedia untuk transaksi setelah stok masuk.</span>
        </div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
    <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost w-full sm:w-auto text-center order-2 sm:order-1">{{ __('messages.back') ?? 'Kembali' }}</a>
    <button type="submit" class="btn-primary w-full sm:w-auto text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
</div>
    </form>
</div></div>
@endsection
