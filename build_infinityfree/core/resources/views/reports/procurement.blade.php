@extends('layouts.app')
@section('page-title', __('messages.report_procurement'))
@section('content')
<form method="GET" class="mb-4 flex flex-col md:flex-row md:flex-wrap items-start md:items-end gap-4 rounded-xl bg-[var(--color-canvas)] p-4 shadow-sm ring-1 ring-gray-100">
    <div><label class="block text-xs text-[var(--color-slate)] mb-1">{{ __('messages.start_date') }}</label><input type="date" name="start_date" value="{{ $startDate }}" class="input-field !w-auto"></div>
    <div><label class="block text-xs text-[var(--color-slate)] mb-1">{{ __('messages.end_date') }}</label><input type="date" name="end_date" value="{{ $endDate }}" class="input-field !w-auto"></div>
    <button type="submit" class="btn-primary !py-2.5 !px-5">{{ __('messages.filter') }}</button>
    <a href="{{ route('reports.procurement', ['start_date'=>$startDate,'end_date'=>$endDate,'export'=>'pdf']) }}" class="rounded-lg border border-[var(--color-hairline)] px-3 py-2 text-sm text-[var(--color-charcoal)] hover:bg-[var(--color-surface-soft)]"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF</a>
</form>
<div class="rounded-xl bg-[var(--color-canvas)] shadow-sm ring-1 ring-gray-100 overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm">
    <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]/50 text-left text-xs font-medium text-[var(--color-slate)] uppercase">
        <th class="px-4 py-3">{{ __('messages.code') }}</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">{{ __('messages.date') }}</th><th class="px-4 py-3">{{ __('messages.status') }}</th><th class="px-4 py-3 text-right">{{ __('messages.total') }}</th>
    </tr></thead><tbody class="divide-y divide-[var(--color-hairline-soft)]/50">@foreach($procurements as $p)
    <tr><td class="px-4 py-2 font-mono text-xs">{{ $p->procurement_code }}</td><td class="px-4 py-2">{{ $p->supplier->name }}</td><td class="px-4 py-2 text-[var(--color-slate)]">{{ $p->procurement_date->format('d/m/Y') }}</td>
        <td class="px-4 py-2"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100">{{ __('messages.'.$p->status) }}</span></td><td class="px-4 py-2 text-right font-medium">Rp {{ number_format($p->total,0,',','.') }}</td></tr>
    @endforeach</tbody></table></div></div>
@endsection

