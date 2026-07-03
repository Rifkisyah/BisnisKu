@extends('layouts.app')
@section('page-title', __('messages.edit') . ' Supplier')
@section('content')
<div class="mx-auto max-w-lg"><div class="card-feature p-8">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-5" enctype="multipart/form-data">@csrf @method('PUT')
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label><input type="text" name="name" value="{{ old('name', $supplier->name) }}" required minlength="3" maxlength="100" class="input-field"><p class="mt-1 text-xs text-gray-400">3–100 karakter</p></div>
        <div>
            <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.contact') }}</label>
            <div class="flex gap-2">
                <select name="phone_prefix" class="input-field !w-24">
                    <option value="+62" {{ old('phone_prefix', $supplier->phone_prefix) == '+62' ? 'selected' : '' }}>+62</option>
                    <option value="+60" {{ old('phone_prefix', $supplier->phone_prefix) == '+60' ? 'selected' : '' }}>+60</option>
                    <option value="+65" {{ old('phone_prefix', $supplier->phone_prefix) == '+65' ? 'selected' : '' }}>+65</option>
                </select>
                <input type="text" name="phone_number" value="{{ old('phone_number', $supplier->phone_number) }}" minlength="10" maxlength="13" class="input-field flex-1" placeholder="81234567890" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
        </div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.email') }}</label><input type="email" name="email" value="{{ old('email', $supplier->email) }}" maxlength="150" class="input-field"></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.address') }}</label><textarea name="address" rows="3" maxlength="300" class="input-field !h-auto !py-3">{{ old('address', $supplier->address) }}</textarea><p class="mt-1 text-xs text-gray-400">{{ __('messages.max_300_chars') }}</p></div>
        <div>
            <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Gambar Supplier</label>
            @if($supplier->image)
                <div class="mb-2"><img src="{{ asset('storage/' . $supplier->image) }}" class="w-16 h-16 rounded object-cover"></div>
            @endif
            <input type="file" name="image" accept="image/*" class="input-field !py-2.5">
        </div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
    <a href="{{ route('suppliers.show', $supplier) }}" class="btn-ghost w-full sm:w-auto text-center order-2 sm:order-1"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    <button type="submit" class="btn-primary w-full sm:w-auto text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
</div>
    </form>
</div></div>
@endsection
