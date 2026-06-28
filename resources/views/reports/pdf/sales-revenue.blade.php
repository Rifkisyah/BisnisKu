<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Penjualan & Pendapatan</h1><p>{{ $startDate }} s/d {{ $endDate }}</p></div>

<div class="section-title">Ringkasan Pendapatan</div>
<table class="data-table">
    <tr><th>Pendapatan Penjualan</th><td class="text-right">Rp {{ number_format($salesRevenue,0,',','.') }}</td></tr>
    <tr><th>Pendapatan Jasa</th><td class="text-right">Rp {{ number_format($serviceRevenue,0,',','.') }}</td></tr>
</table>

<div style="border:1px solid #000;padding:10px;margin-top:15px;text-align:center;font-weight:bold;font-size:14px;background:#f5f5f5">Total Keseluruhan Pendapatan: Rp {{ number_format($totalRevenue,0,',','.') }}</div>

<div class="section-title">Peringkat Penjualan Produk (Top 10)</div>
<table class="data-table">
    <thead><tr><th style="width:30px">#</th><th>Nama Produk</th><th class="text-right">Qty Terjual</th></tr></thead>
    <tbody>
        @foreach($topProducts as $idx => $tp)
        <tr><td>{{ $idx+1 }}</td><td>{{ $tp->name }}</td><td class="text-right">{{ $tp->total_qty }}</td></tr>
        @endforeach
    </tbody>
</table>

<div class="section-title">Daftar Transaksi Penjualan</div>
<table class="data-table">
    <thead><tr><th>Kode Transaksi</th><th>Tanggal</th><th>Kasir</th><th>Metode Pembayaran</th><th class="text-right">Total</th></tr></thead>
    <tbody>
        @foreach($transactions as $t)
        <tr><td>{{ $t->transaction_code }}</td><td>{{ $t->transaction_date->format('d/m/Y H:i') }}</td><td>{{ $t->cashier->username ?? 'Unknown' }}</td><td>{{ strtoupper($t->payment_method) }}</td><td class="text-right">Rp {{ number_format($t->total,0,',','.') }}</td></tr>
        @endforeach
    </tbody>
</table>

</body></html>
