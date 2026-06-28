<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Pengadaan Produk</h1><p>{{ $startDate ?? '-' }} s/d {{ $endDate ?? '-' }}</p></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Tanggal</th><th>Sumber</th><th>Supplier</th><th>Status</th><th class="text-right">Total Biaya</th></tr></thead>
<tbody>
    @php $totalCost = 0; @endphp
    @foreach($purchases as $p)
    @php $totalCost += $p->total; @endphp
    <tr>
        <td>{{ $p->product_purchase_code }}</td>
        <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d/m/Y') }}</td>
        <td>{{ ucfirst($p->source) }}</td>
        <td>{{ $p->supplier->name ?? '-' }}</td>
        <td>{{ strtoupper($p->status) }}</td>
        <td class="text-right">Rp {{ number_format($p->total,0,',','.') }}</td>
    </tr>
    @endforeach
</tbody></table>
<div class="text-right" style="font-weight:bold;margin-top:10px">Total Pengeluaran Pengadaan: Rp {{ number_format($totalCost,0,',','.') }}</div></body></html>
