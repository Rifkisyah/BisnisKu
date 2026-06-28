<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>body{font-family:sans-serif;font-size:11px;max-width:300px;margin:0 auto;padding:10px}
.center{text-align:center}.bold{font-weight:bold}.line{border-top:1px dashed #333;margin:6px 0}
.row{display:flex;justify-content:space-between}table{width:100%;border-collapse:collapse}td{padding:2px 0}</style></head>
<body>
<div class="center bold" style="font-size:13px;text-transform:uppercase;">{{ \App\Models\Setting::get('store_name', 'NAMA TOKO') }}</div>
<div class="center" style="font-size:11px; margin-bottom:4px;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</div>
<div class="center" style="font-size:11px; margin-bottom:8px;">{{ \App\Models\Setting::get('store_phone', 'No Telp') }}</div>
<div class="center" style="color:#666;font-size:9px">Struk Pembayaran</div>
<div class="line"></div>
<div class="row"><span>{{ $transaction->transaction_code }}</span><span>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</span></div>
<div>Kasir: {{ $transaction->user->name }}</div>
<div class="line"></div>
@foreach($transaction->items as $item)
<div>{{ $item->product->name }}</div>
<div class="row"><span>&nbsp;&nbsp;{{ $item->quantity }} x Rp {{ number_format($item->unit_price,0,',','.') }}</span><span>Rp {{ number_format($item->subtotal,0,',','.') }}</span></div>
@endforeach
<div class="line"></div>
<div class="row bold"><span>TOTAL</span><span>Rp {{ number_format($transaction->total,0,',','.') }}</span></div>
<div class="row"><span>Bayar</span><span>Rp {{ number_format($transaction->amount_paid,0,',','.') }}</span></div>
<div class="row"><span>Kembali</span><span>Rp {{ number_format($transaction->change_amount,0,',','.') }}</span></div>
<div class="line"></div>
<div class="center" style="font-size:9px;color:#666">Terima kasih!</div>
</body></html>
