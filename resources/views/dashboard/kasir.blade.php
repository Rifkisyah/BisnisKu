@extends('layouts.app')
@section('page-title', __('messages.dashboard'))
@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.today_transactions') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $todayTransactions }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-primary)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.today_revenue') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-success)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.total_products') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $productCount }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-attention)]"></div>
    </div>
</div>
<div class="card-feature p-6">
    <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">{{ __('messages.recent_transactions') }}</h3>
    <div class="overflow-x-auto"><table class="w-full">
        <thead><tr class="border-b border-[var(--color-hairline-soft)]">
            <th class="pb-3 pr-4 text-left table-header">{{ __('messages.code') }}</th><th class="pb-3 pr-4 text-left table-header">{{ __('messages.date') }}</th>
            <th class="pb-3 pr-4 text-left table-header">{{ __('messages.total') }}</th><th class="pb-3 text-left table-header">{{ __('messages.payment_method') }}</th>
        </tr></thead><tbody>
        @forelse($recentTransactions as $t)
        <tr class="border-b border-[var(--color-hairline-soft)]/50">
            <td class="py-3 pr-4 type-caption-bold text-[var(--color-primary)]">{{ $t->transaction_code }}</td>
            <td class="py-3 pr-4 type-body-sm text-[var(--color-slate)]">{{ $t->transaction_date->format('d/m/Y H:i') }}</td>
            <td class="py-3 pr-4 type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            <td class="py-3"><span class="badge badge-neutral">{{ __('messages.'.$t->payment_method) }}</span></td>
        </tr>
        @empty<tr><td colspan="4" class="py-8 text-center type-body-sm text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
        </tbody></table></div>
</div>
@endsection
