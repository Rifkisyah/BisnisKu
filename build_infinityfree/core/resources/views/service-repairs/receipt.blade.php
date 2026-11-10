<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Service {{ $serviceRepair->repair_code }}</title>
<style>body{font-family:monospace;max-width:300px;margin:0 auto;padding:10px;font-size:12px}.center{text-align:center}.bold{font-weight:bold}.line{border-top:1px dashed #000;margin:8px 0}.row{display:flex;justify-content:space-between}@media print{body{margin:0;padding:5px}}</style></head>
<body>
<div class="center bold" style="font-size:14px;text-transform:uppercase;">{{ \App\Models\Setting::get('store_name', 'NAMA TOKO') }}</div>
<div class="center" style="font-size:12px; margin-bottom:5px;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</div>
<div class="center" style="font-size:12px; margin-bottom:10px;">{{ \App\Models\Setting::get('store_phone', 'No Telp') }}</div>
<div class="center" style="font-size:10px;color:#666">Struk Service</div>
<div class="line"></div>
<div class="row"><span>{{ $serviceRepair->repair_code }}</span><span>{{ $serviceRepair->start_date->format('d/m/Y') }}</span></div>
<div>Pelanggan: {{ $serviceRepair->customer_name }}</div>
<div>HP: {{ $serviceRepair->phone_series }}</div>
<div>Keluhan: {{ $serviceRepair->items()->first()?->complaint ?? '-' }}</div>
<div>Status: {{ __('messages.'.$serviceRepair->status) }}</div>
<div class="line"></div>
<div class="bold">Biaya:</div>
<div class="row"><span>Jasa Service</span><span>Rp {{ number_format($serviceRepair->service_fee,0,',','.') }}</span></div>
@foreach($serviceRepair->items as $item)
@if($item->name !== 'Main Repair Job' || $item->subtotal > 0)
<div class="row"><span>{{ $item->name }} x{{ $item->quantity }}</span><span>Rp {{ number_format($item->subtotal,0,',','.') }}</span></div>
@endif
@endforeach
<div class="line"></div>
<div class="row bold"><span>TOTAL</span><span>Rp {{ number_format($serviceRepair->total_cost,0,',','.') }}</span></div>
<div class="line"></div>
<div class="center" style="font-size:10px;color:#666">Terima kasih!</div>
<script>window.onload=function(){window.print()}</script>
</body></html>
