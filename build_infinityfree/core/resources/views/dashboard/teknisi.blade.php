@extends('layouts.app')
@section('page-title', __('messages.dashboard'))
@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.active_repairs') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $activeRepairs }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-primary)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.completed_repairs') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $completedRepairs }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-success)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.total_revenue') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-attention)]"></div>
    </div>
</div>
<div class="card-feature p-6">
    <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">{{ __('messages.recent_repairs') }}</h3>
    <div class="overflow-x-auto"><table class="w-full">
        <thead><tr class="border-b border-[var(--color-hairline-soft)]">
            <th class="pb-3 pr-4 text-left table-header">{{ __('messages.code') }}</th><th class="pb-3 pr-4 text-left table-header">{{ __('messages.customer_name') }}</th>
            <th class="pb-3 pr-4 text-left table-header">Total Biaya</th><th class="pb-3 text-left table-header">{{ __('messages.status') }}</th>
        </tr></thead><tbody>
        @forelse($recentRepairs as $r)
        @php $sc=['draft'=>'bg-gray-100 text-gray-600','waiting_dp'=>'bg-red-100 text-red-700','diagnosing'=>'bg-blue-100 text-blue-700','waiting_parts'=>'bg-amber-100 text-amber-700','repairing'=>'bg-indigo-100 text-indigo-700','ready'=>'bg-green-100 text-green-700','done'=>'badge-success','cancelled'=>'badge-critical']; @endphp
        <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-gray-50 cursor-pointer transition-colors" onclick="window.location='{{ route('service-repairs.show', $r) }}'">
            <td class="py-3 pr-4 type-caption-bold text-[var(--color-primary)]">{{ $r->repair_code }}</td>
            <td class="py-3 pr-4 type-body-sm font-medium text-[var(--color-ink)]">{{ $r->customer_name }}</td>
            <td class="py-3 pr-4 type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($r->total_cost, 0, ',', '.') }}</td>
            <td class="py-3"><span class="badge {{ $sc[$r->status] ?? 'badge-neutral' }}">{{ __('messages.'.$r->status) }}</span></td>
        </tr>
        @empty<tr><td colspan="4" class="py-8 text-center type-body-sm text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
        </tbody></table></div>
</div>
@endsection
