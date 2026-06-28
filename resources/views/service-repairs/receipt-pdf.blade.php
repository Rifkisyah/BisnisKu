<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>body{font-family:sans-serif;font-size:11px;max-width:300px;margin:0 auto;padding:10px}.center{text-align:center}.bold{font-weight:bold}.line{border-top:1px dashed #333;margin:6px 0}.row{display:flex;justify-content:space-between}</style></head>
<body>
<div class="center bold" style="font-size:13px;text-transform:uppercase;">{{ \App\Models\Setting::get('store_name', 'NAMA TOKO') }}</div>
<div class="center" style="font-size:11px; margin-bottom:4px;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</div>
<div class="center" style="font-size:11px; margin-bottom:8px;">{{ \App\Models\Setting::get('store_phone', 'No Telp') }}</div>
<div class="center" style="color:#666;font-size:9px">Struk Service</div>
<div class="line"></div>
<div>{{ $serviceRepair->repair_code }} | {{ $serviceRepair->start_date->format('d/m/Y') }}</div>
<div>Pelanggan: {{ $serviceRepair->customer_name }}</div>
<div>HP: {{ $serviceRepair->phone_brand }} {{ $serviceRepair->phone_model }}</div>
<div class="line"></div>
<div class="row"><span>Jasa</span><span>Rp {{ number_format($serviceRepair->service_fee,0,',','.') }}</span></div>
@foreach($serviceRepair->items as $item)<div class="row"><span>{{ $item->item_name }}</span><span>Rp {{ number_format($item->subtotal,0,',','.') }}</span></div>@endforeach
<div class="line"></div>
<div class="row bold"><span>TOTAL</span><span>Rp {{ number_format($serviceRepair->total_cost,0,',','.') }}</span></div>
</body></html>
