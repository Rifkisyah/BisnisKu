<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Transaksi Kasir</h1><p>{{ $startDate ?? '-' }} s/d {{ $endDate ?? '-' }}</p></div>
<table class="data-table"><thead><tr><th>Kode Transaksi</th><th>Tanggal</th><th>Kasir</th><th>Metode</th><th class="text-right">Total</th></tr></thead>
<tbody>
    @foreach($transactions as $t)
    <tr>
        <td>{{ $t->transaction_code }}</td>
        <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y H:i') }}</td>
        <td>{{ $t->cashier->username ?? '-' }}</td>
        <td>{{ strtoupper($t->payment_method) }}</td>
        <td class="text-right">Rp {{ number_format($t->total,0,',','.') }}</td>
    </tr>
    @endforeach
</tbody></table>
<div class="text-right" style="font-weight:bold;margin-top:10px">Total Transaksi: Rp {{ number_format($totalRevenue,0,',','.') }}</div></body></html>
