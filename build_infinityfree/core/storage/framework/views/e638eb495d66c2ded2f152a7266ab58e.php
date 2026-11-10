
<?php $__env->startSection('page-title', __('messages.detail') . ' ' . __('messages.transaction')); ?>
<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-3xl space-y-6">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?php echo e(route('transactions.index')); ?>" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg><?php echo e(__('messages.back') ?? 'Kembali'); ?></a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('transactions.receipt', $transaction)); ?>" target="_blank" class="btn-ghost !py-2 !px-4"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Print</a>
            <a href="<?php echo e(route('transactions.receipt.pdf', $transaction)); ?>" class="btn-primary !py-2 !px-4"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF</a>
        </div>
    </div>
    <div class="card-feature p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="type-heading-sm text-[var(--color-ink-deep)]"><?php echo e($transaction->transaction_code); ?></h3>
            <!-- Assuming status could go here if it exists, otherwise just the code -->
        </div>
        <div class="mb-4">
            <p class="type-body-sm text-[var(--color-slate)]">Tanggal: <?php echo e($transaction->transaction_date->format('d M Y, H:i')); ?></p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div><p class="type-caption-bold text-[var(--color-slate)]"><?php echo e(__('messages.customer_name')); ?></p><p class="type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($transaction->customer_name ?: '-'); ?></p></div>
            <div><p class="type-caption-bold text-[var(--color-slate)]"><?php echo e(__('messages.payment_method')); ?></p><p class="type-body-sm font-medium text-[var(--color-ink)]"><?php echo e(__('messages.'.$transaction->payment_method)); ?></p></div>
            <div><p class="type-caption-bold text-[var(--color-slate)]"><?php echo e(__('messages.amount_paid')); ?></p><p class="type-body-sm font-medium text-[var(--color-ink)]">Rp <?php echo e(number_format($transaction->amount_paid, 0, ',', '.')); ?></p></div>
            <div><p class="type-caption-bold text-[var(--color-slate)]"><?php echo e(__('messages.change')); ?></p><p class="type-body-sm font-medium text-[var(--color-success)]">Rp <?php echo e(number_format($transaction->change, 0, ',', '.')); ?></p></div>
        </div>
        
        <?php if($transaction->payment_method === 'debt' && $transaction->debt): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <h4 class="font-bold text-amber-800 mb-2">Informasi Hutang</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><p class="type-caption-bold text-amber-700">Nama Penghutang</p><p class="type-body-sm font-medium text-amber-900"><?php echo e($transaction->debt->debtor_name); ?></p></div>
                <div><p class="type-caption-bold text-amber-700">Jatuh Tempo</p><p class="type-body-sm font-medium text-amber-900"><?php echo e($transaction->debt->due_date ? $transaction->debt->due_date->format('d/m/Y') : '-'); ?></p></div>
            </div>
        </div>
        <?php endif; ?>

        <table class="w-full"><thead><tr class="border-b border-[var(--color-hairline-soft)]">
            <th class="pb-3 text-left table-header"><?php echo e(__('messages.product')); ?></th><th class="pb-3 text-left table-header">Qty</th>
            <th class="pb-3 text-left table-header"><?php echo e(__('messages.price')); ?></th><th class="pb-3 text-left table-header"><?php echo e(__('messages.subtotal')); ?></th>
        </tr></thead><tbody>
        <?php $__currentLoopData = $transaction->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="border-b border-[var(--color-hairline-soft)]/50"><td class="py-3 type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($item->product->name); ?></td>
            <td class="py-3 type-body-sm"><?php echo e($item->quantity); ?></td><td class="py-3 type-body-sm">Rp <?php echo e(number_format($item->unit_price, 0, ',', '.')); ?></td>
            <td class="py-3 type-body-sm font-bold">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody></table>
        <div class="mt-4 flex justify-end">
            <div class="w-56 space-y-2 text-right">
                <div class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]"><?php echo e(__('messages.subtotal')); ?></span><span>Rp <?php echo e(number_format($transaction->subtotal, 0, ',', '.')); ?></span></div>
                <div class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]"><?php echo e(__('messages.discount')); ?></span><span>-Rp <?php echo e(number_format($transaction->discount, 0, ',', '.')); ?></span></div>
                <div class="flex justify-between type-subtitle-lg border-t border-[var(--color-hairline-soft)] pt-2"><span><?php echo e(__('messages.total')); ?></span><span class="text-[var(--color-primary)]">Rp <?php echo e(number_format($transaction->total, 0, ',', '.')); ?></span></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/transactions/show.blade.php ENDPATH**/ ?>