@extends('layouts.app')
@section('page-title', __('messages.edit') . ' ' . __('messages.product'))
@section('content')
<div class="mx-auto max-w-2xl"><div class="card-feature p-8">
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">@csrf @method('PUT')
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.code') }}</label><input type="text" value="{{ $product->product_code }}" disabled class="input-field !bg-[var(--color-surface-soft)]"></div>
            <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label><input type="text" name="name" value="{{ old('name', $product->name) }}" required minlength="3" maxlength="100" class="input-field"><p class="mt-1 text-xs text-gray-400">3–100 karakter</p></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.category') }} *</label>
                <select name="category_code" required class="input-field">@foreach($categories as $c)<option value="{{ $c->category_code }}" {{ $product->category_code == $c->category_code ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Supplier</label>
                <select name="supplier_code" class="input-field"><option value="">-</option>@foreach($suppliers as $s)<option value="{{ $s->supplier_code }}" {{ $product->supplier_code == $s->supplier_code ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.purchase_price') }} *</label><input type="text" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required min="0" class="input-field input-rupiah"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.selling_price') }} *</label><input type="text" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required min="0" class="input-field input-rupiah"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.minimum_stock') }} *</label><input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" required min="0" class="input-field"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.status') }}</label>
                <select name="status" class="input-field"><option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option><option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option></select></div>
            <div class="sm:col-span-2"><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.description') }}</label><textarea name="description" rows="3" maxlength="500" class="input-field !h-auto !py-3">{{ old('description', $product->description) }}</textarea><p class="mt-1 text-xs text-gray-400">{{ __('messages.max_500_chars') }}</p></div>
            <div class="sm:col-span-2">
                @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" class="mb-2 h-20 w-20 rounded-[var(--radius-xl)] object-cover border border-[var(--color-hairline-soft)]">@endif
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.image') }}</label>
                <input type="file" name="image" accept="image/*" class="input-field !py-2 file:mr-3 file:rounded-[var(--radius-full)] file:border-0 file:bg-[var(--color-primary-soft)] file:px-3 file:py-1 file:type-caption-bold file:text-[var(--color-primary)]"></div>
        </div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
    <a href="{{ route('products.show', $product) }}" class="btn-ghost w-full sm:w-auto text-center order-2 sm:order-1"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    <button type="submit" class="btn-primary w-full sm:w-auto text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
</div>
    </form>
</div></div>
@endsection
