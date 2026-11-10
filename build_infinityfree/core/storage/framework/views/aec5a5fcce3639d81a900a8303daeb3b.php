<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Receipt <?php echo e($transaction->transaction_code); ?></title>
<style>body{font-family:monospace;max-width:300px;margin:0 auto;padding:10px;font-size:12px}
.center{text-align:center}.bold{font-weight:bold}.line{border-top:1px dashed #000;margin:8px 0}
.row{display:flex;justify-content:space-between}.right{text-align:right}
@media print{body{margin:0;padding:5px}}</style></head>
<body>
<div class="center bold" style="font-size:14px;text-transform:uppercase;"><?php echo e(\App\Models\Setting::get('store_name', 'NAMA TOKO')); ?></div>
<div class="center" style="font-size:12px; margin-bottom:5px;"><?php echo e(\App\Models\Setting::get('store_address', 'Alamat Toko')); ?></div>
<div class="center" style="font-size:12px; margin-bottom:10px;"><?php echo e(\App\Models\Setting::get('store_phone', 'No Telp')); ?></div>
<div class="center" style="font-size:10px;color:#666">Struk Pembayaran</div>
<div class="line"></div>
<div class="row"><span><?php echo e($transaction->transaction_code); ?></span><span><?php echo e($transaction->transaction_date->format('d/m/Y H:i')); ?></span></div>
<div>Kasir: <?php echo e($transaction->user->name); ?></div>
<?php if($transaction->customer_name): ?><div>Pelanggan: <?php echo e($transaction->customer_name); ?></div><?php endif; ?>
<?php if($transaction->payment_method === 'debt' && $transaction->debt): ?>
<div>Jenis Transaksi: Hutang</div>
<div>Nama Penghutang: <?php echo e($transaction->debt->debtor_name); ?></div>
<div>Jatuh Tempo: <?php echo e($transaction->debt->due_date ? $transaction->debt->due_date->format('d/m/Y') : '-'); ?></div>
<?php endif; ?>
<div class="line"></div>
<?php $__currentLoopData = $transaction->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div><?php echo e($item->product->name); ?></div>
<div class="row"><span>&nbsp;&nbsp;<?php echo e($item->quantity); ?> x Rp <?php echo e(number_format($item->unit_price,0,',','.')); ?></span><span>Rp <?php echo e(number_format($item->subtotal,0,',','.')); ?></span></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="line"></div>
<div class="row"><span>Subtotal</span><span>Rp <?php echo e(number_format($transaction->subtotal,0,',','.')); ?></span></div>
<?php if($transaction->discount > 0): ?><div class="row"><span>Diskon</span><span>-Rp <?php echo e(number_format($transaction->discount,0,',','.')); ?></span></div><?php endif; ?>
<div class="row bold"><span>TOTAL</span><span>Rp <?php echo e(number_format($transaction->total,0,',','.')); ?></span></div>
<div class="row"><span>Bayar (<?php echo e(__('messages.'.$transaction->payment_method)); ?>)</span><span>Rp <?php echo e(number_format($transaction->amount_paid,0,',','.')); ?></span></div>
<div class="row"><span>Kembali</span><span>Rp <?php echo e(number_format($transaction->change_amount,0,',','.')); ?></span></div>
<div class="line"></div>
<div class="center" style="font-size:10px;color:#666">Terima kasih atas kunjungan Anda!<br>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
<script>window.onload=function(){window.print()}</script>
</body></html>
<?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/transactions/receipt.blade.php ENDPATH**/ ?>