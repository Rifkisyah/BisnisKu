<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Receipt {{ $transaction->transaction_code }}</title>
<style>
    body { font-family: monospace; max-width: 300px; margin: 0 auto; padding: 10px; font-size: 12px; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .line { border-top: 1px dashed #000; margin: 8px 0; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 2px 0; vertical-align: top; }
    .text-right { text-align: right; }
    @media print { body { margin: 0; padding: 5px; } }
</style>
</head>
<body>
<div class="center bold" style="font-size:14px;text-transform:uppercase;">{{ \App\Models\Setting::get('store_name', 'NAMA TOKO') }}</div>
<div class="center" style="font-size:12px; margin-bottom:5px;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</div>
<div class="center" style="font-size:12px; margin-bottom:10px;">{{ \App\Models\Setting::get('store_phone', 'No Telp') }}</div>
<div class="center" style="font-size:10px;color:#666">Struk Pembayaran</div>
<div class="line"></div>
<table>
    <tr><td>{{ $transaction->transaction_code }}</td><td class="text-right">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td></tr>
</table>
<div>Kasir: {{ $transaction->user->name }}</div>
@if($transaction->customer_name)<div>Pelanggan: {{ $transaction->customer_name }}</div>@endif
@if($transaction->payment_method === 'debt' && $transaction->debt)
<div>Jenis Transaksi: Hutang</div>
<div>Nama Penghutang: {{ $transaction->debt->debtor_name }}</div>
<div>Jatuh Tempo: {{ $transaction->debt->due_date ? $transaction->debt->due_date->format('d/m/Y') : '-' }}</div>
@endif
<div class="line"></div>
<table>
@foreach($transaction->items as $item)
    <tr><td colspan="2">{{ $item->product->name }}</td></tr>
    <tr>
        <td>&nbsp;&nbsp;{{ $item->quantity }} x Rp {{ number_format($item->unit_price,0,',','.') }}</td>
        <td class="text-right">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
    </tr>
@endforeach
</table>
<div class="line"></div>
<table>
    <tr><td>Subtotal</td><td class="text-right">Rp {{ number_format($transaction->subtotal,0,',','.') }}</td></tr>
    @if($transaction->discount > 0)
    <tr><td>Diskon</td><td class="text-right">-Rp {{ number_format($transaction->discount,0,',','.') }}</td></tr>
    @endif
    <tr class="bold"><td>TOTAL</td><td class="text-right">Rp {{ number_format($transaction->total,0,',','.') }}</td></tr>
    <tr><td>Bayar ({{ __('messages.'.$transaction->payment_method) }})</td><td class="text-right">Rp {{ number_format($transaction->amount_paid,0,',','.') }}</td></tr>
    <tr><td>Kembali</td><td class="text-right">Rp {{ number_format($transaction->change_amount,0,',','.') }}</td></tr>
</table>
<div class="line"></div>
<div class="center" style="font-size:10px;color:#666">Terima kasih atas kunjungan Anda!<br>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
<script>
window.onload=function(){ window.print(); }
window.onafterprint=function(){ window.location.href = "{{ route('transactions.index') }}"; }
</script>
</body></html>
