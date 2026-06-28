<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Receipt {{ $transaction->transaction_code }}</title>
<style>body{font-family:monospace;max-width:300px;margin:0 auto;padding:10px;font-size:12px}
.center{text-align:center}.bold{font-weight:bold}.line{border-top:1px dashed #000;margin:8px 0}
.row{display:flex;justify-content:space-between}.right{text-align:right}
@media print{body{margin:0;padding:5px}}</style></head>
<body>
<div class="center bold" style="font-size:14px;text-transform:uppercase;">{{ \App\Models\Setting::get('store_name', 'NAMA TOKO') }}</div>
<div class="center" style="font-size:12px; margin-bottom:5px;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</div>
<div class="center" style="font-size:12px; margin-bottom:10px;">{{ \App\Models\Setting::get('store_phone', 'No Telp') }}</div>
<div class="center" style="font-size:10px;color:#666">Struk Pembayaran</div>
<div class="line"></div>
<div class="row"><span>{{ $transaction->transaction_code }}</span><span>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</span></div>
<div>Kasir: {{ $transaction->user->name }}</div>
@if($transaction->customer_name)<div>Pelanggan: {{ $transaction->customer_name }}</div>@endif
<div class="line"></div>
@foreach($transaction->items as $item)
<div>{{ $item->product->name }}</div>
<div class="row"><span>&nbsp;&nbsp;{{ $item->quantity }} x Rp {{ number_format($item->unit_price,0,',','.') }}</span><span>Rp {{ number_format($item->subtotal,0,',','.') }}</span></div>
@endforeach
<div class="line"></div>
<div class="row"><span>Subtotal</span><span>Rp {{ number_format($transaction->subtotal,0,',','.') }}</span></div>
@if($transaction->discount > 0)<div class="row"><span>Diskon</span><span>-Rp {{ number_format($transaction->discount,0,',','.') }}</span></div>@endif
<div class="row bold"><span>TOTAL</span><span>Rp {{ number_format($transaction->total,0,',','.') }}</span></div>
<div class="row"><span>Bayar ({{ __('messages.'.$transaction->payment_method) }})</span><span>Rp {{ number_format($transaction->amount_paid,0,',','.') }}</span></div>
<div class="row"><span>Kembali</span><span>Rp {{ number_format($transaction->change_amount,0,',','.') }}</span></div>
<div class="line"></div>
<div class="center" style="font-size:10px;color:#666">Terima kasih atas kunjungan Anda!<br>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
<script>window.onload=function(){window.print()}</script>
</body></html>
