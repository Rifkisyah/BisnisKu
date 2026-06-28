<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Hutang Piutang</h1><p>{{ $startDate ?? '-' }} s/d {{ $endDate ?? '-' }}</p></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Tanggal Hutang</th><th>Nama Debitur</th><th>Jatuh Tempo</th><th>Status</th><th class="text-right">Total Hutang</th><th class="text-right">Sisa Hutang</th></tr></thead>
<tbody>
    @php $totalDebt = 0; $totalRemaining = 0; @endphp
    @foreach($debts as $d)
    @php $totalDebt += $d->total_amount; $totalRemaining += $d->remaining_amount; @endphp
    <tr>
        <td>{{ $d->debt_code }}</td>
        <td>{{ \Carbon\Carbon::parse($d->debt_date)->format('d/m/Y') }}</td>
        <td>{{ $d->debtor_name }}</td>
        <td>{{ $d->due_date ? \Carbon\Carbon::parse($d->due_date)->format('d/m/Y') : '-' }}</td>
        <td>{{ strtoupper($d->status) }}</td>
        <td class="text-right">Rp {{ number_format($d->total_amount,0,',','.') }}</td>
        <td class="text-right">Rp {{ number_format($d->remaining_amount,0,',','.') }}</td>
    </tr>
    @endforeach
</tbody></table>
<div class="text-right" style="font-weight:bold;margin-top:10px">Total Seluruh Hutang: Rp {{ number_format($totalDebt,0,',','.') }} <br> Total Sisa Belum Dibayar: Rp {{ number_format($totalRemaining,0,',','.') }}</div></body></html>
