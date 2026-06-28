<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Business Intelligence Report</h1><p>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p></div>
<table class="metric-grid"><tr><td><div class="metric-title">Penjualan</div><div class="metric-value">Rp {{ number_format($totalRevenue,0,',','.') }}</div></td>
<td><div class="metric-title">Service</div><div class="metric-value">Rp {{ number_format($serviceRevenue,0,',','.') }}</div></td></tr></table>
<div class="section-title">Top Produk</div>
<table class="data-table"><thead><tr><th>Produk</th><th class="text-right">Qty</th><th class="text-right">Pendapatan</th></tr></thead>
<tbody>@foreach($topProducts as $p)<tr><td>{{ $p->name }}</td><td class="text-right">{{ $p->total_qty }}</td><td class="text-right">Rp {{ number_format($p->total_revenue,0,',','.') }}</td></tr>@endforeach</tbody></table>
</body></html>
