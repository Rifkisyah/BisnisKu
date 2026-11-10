<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Data Produk</h1></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th class="text-right">Harga Beli</th><th class="text-right">Harga Jual</th><th class="text-center">Stok</th><th>Status</th></tr></thead>
<tbody>
    @foreach($products as $p)
    <tr>
        <td>{{ $p->product_code }}</td>
        <td>{{ $p->name }}</td>
        <td>{{ $p->category->name ?? '-' }}</td>
        <td class="text-right">Rp {{ number_format($p->purchase_price,0,',','.') }}</td>
        <td class="text-right">Rp {{ number_format($p->selling_price,0,',','.') }}</td>
        <td class="text-center">{{ $p->stock }}</td>
        <td>{{ strtoupper($p->status) }}</td>
    </tr>
    @endforeach
</tbody></table>
</body></html>
