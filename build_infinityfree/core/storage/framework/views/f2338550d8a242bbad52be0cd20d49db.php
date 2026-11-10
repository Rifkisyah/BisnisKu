<!DOCTYPE html><html><head><meta charset="UTF-8"><?php echo $__env->make('components.pdf-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></head>
<body><div class="header"><h1>Laporan Data Produk</h1></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th class="text-right">Harga Beli</th><th class="text-right">Harga Jual</th><th class="text-center">Stok</th><th>Status</th></tr></thead>
<tbody>
    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td><?php echo e($p->product_code); ?></td>
        <td><?php echo e($p->name); ?></td>
        <td><?php echo e($p->category->name ?? '-'); ?></td>
        <td class="text-right">Rp <?php echo e(number_format($p->purchase_price,0,',','.')); ?></td>
        <td class="text-right">Rp <?php echo e(number_format($p->selling_price,0,',','.')); ?></td>
        <td class="text-center"><?php echo e($p->stock); ?></td>
        <td><?php echo e(strtoupper($p->status)); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody></table>
</body></html>
<?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/products/pdf.blade.php ENDPATH**/ ?>