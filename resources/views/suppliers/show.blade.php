@extends('layouts.app')
@section('page-title', __('messages.detail') . ' ' . __('messages.supplier'))
@section('content')
<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>
    <div class="card-feature p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ $supplier->name }}</h3>
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="inline-flex">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $supplier->name }}">
                <input type="hidden" name="whatsapp_number" value="{{ $supplier->whatsapp_number }}">
                <input type="hidden" name="email" value="{{ $supplier->email }}">
                <input type="hidden" name="address" value="{{ $supplier->address }}">
                
                <input type="hidden" name="status" value="{{ $supplier->status === 'active' ? 'inactive' : 'active' }}">
                <button type="submit" class="badge hover:opacity-80 transition-opacity {{ $supplier->status === 'active' ? 'badge-success' : 'badge-critical' }}" title="Klik untuk mengubah status">
                    {{ $supplier->status }} <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </button>
            </form>
        </div>
        @if($supplier->image)
            <img src="{{ asset('storage/' . $supplier->image) }}" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-[var(--color-surface)] shadow-md mb-6">
        @else
            <div class="w-32 h-32 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] flex items-center justify-center text-4xl font-bold mx-auto mb-6 shadow-sm border-4 border-[var(--color-surface)]">
                {{ substr($supplier->name, 0, 2) }}
            </div>
        @endif
        
        <div class="text-center mb-6">
            <h2 class="type-heading-md text-[var(--color-ink-deep)]">{{ $supplier->name }}</h2>
        </div>
        
        <div class="mt-6 text-left border-t border-[var(--color-hairline-soft)] pt-6 space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Kontak WA</div>
                <div class="col-span-2 type-body-sm text-[var(--color-ink)]">
                    @if($supplier->whatsapp_number)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', ltrim($supplier->whatsapp_number, '0')) }}" target="_blank" class="text-green-600 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.711.927 3.15.927 3.178 0 5.768-2.587 5.769-5.766 0-3.181-2.588-5.769-5.77-5.769zM12.031 16.5c-1.076 0-2.022-.296-2.887-.852l-.208-.124-1.306.342.349-1.272-.136-.216c-.612-.968-.934-2.091-.934-3.266 0-3.18 2.589-5.769 5.77-5.769 3.181 0 5.769 2.587 5.769 5.768 0 3.182-2.588 5.769-5.769 5.769zm3.173-4.341c-.174-.087-1.029-.508-1.189-.566-.159-.058-.275-.087-.391.087-.116.174-.449.566-.55.682-.101.116-.203.13-.377.043-.174-.087-.734-.271-1.398-.863-.515-.46-.863-1.029-.964-1.203-.101-.174-.011-.269.076-.356.079-.079.174-.203.261-.304.087-.101.116-.174.174-.29.058-.116.029-.217-.014-.304-.044-.087-.391-.943-.536-1.291-.141-.341-.285-.295-.391-.3-.099-.005-.214-.005-.33-.005-.116 0-.304.043-.464.217s-.608.594-.608 1.448c0 .855.623 1.68 7.1 1.776 1.157 1.258 1.611 1.353 1.901 1.353.29 0 .941-.384 1.072-.754.13-.371.13-.688.092-.754-.038-.066-.144-.109-.318-.196z"/></svg>
                            {{ $supplier->whatsapp_number }}
                        </a>
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.email') }}</div>
                <div class="col-span-2 type-body-sm text-[var(--color-ink)]">{{ $supplier->email ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.address') }}</div>
                <div class="col-span-2 type-body-sm text-[var(--color-ink)]">{{ $supplier->address ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-primary !px-5 text-sm">{{ __('messages.edit') }}</a>
        </div>
    </div>
</div>
@endsection
