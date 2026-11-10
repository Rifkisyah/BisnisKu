<?php $__env->startSection('page-title', __('messages.categories')); ?>
<?php $__env->startSection('content'); ?>
<div x-data="{ showFilter: false }" class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)] print:hidden print-hidden">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
        <div class="flex items-center justify-between w-full sm:w-auto">
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Aksi & Cari</h2>
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 w-full sm:w-auto justify-end">
            <div class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex">
                <a href="<?php echo e(request()->fullUrlWithQuery(['export' => 'pdf'])); ?>" class="btn-ghost !py-2 !px-3 flex items-center justify-center text-xs sm:text-sm w-full sm:w-auto" title="Download PDF">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> <span>PDF</span>
                </a>
                <a href="<?php echo e(request()->fullUrlWithQuery(['print' => '1'])); ?>" class="btn-ghost !py-2 !px-3 flex items-center justify-center text-xs sm:text-sm w-full sm:w-auto" title="Print">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> <span>Print</span>
                </a>
            </div>
            <a href="<?php echo e(route('categories.create')); ?>" class="btn-primary !py-2.5 !px-4 whitespace-nowrap text-center text-xs sm:text-sm w-full sm:w-auto justify-center flex items-center">
                <svg class="h-4 w-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="inline-block"><?php echo e(__('messages.add')); ?></span>
            </a>
        </div>
    </div>

    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end w-full">
        <div class="w-full sm:flex-1">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('messages.search')); ?>..." class="input-field w-full">
        </div>
        
        <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap items-end">
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Urutkan</label>
                <select name="sort" class="input-field w-full" onchange="this.form.submit()">
                    <option value="" <?php echo e(!request('sort') || request('sort') == 'created_at_desc' ? 'selected' : ''); ?>>Dibuat (Terbaru)</option>
                    <option value="created_at_asc" <?php echo e(request('sort') == 'created_at_asc' ? 'selected' : ''); ?>>Dibuat (Terlama)</option>
                    <option value="updated_at_desc" <?php echo e(request('sort') == 'updated_at_desc' ? 'selected' : ''); ?>>Diupdate (Terbaru)</option>
                    <option value="updated_at_asc" <?php echo e(request('sort') == 'updated_at_asc' ? 'selected' : ''); ?>>Diupdate (Terlama)</option>
                    <option value="name_asc" <?php echo e(request('sort') == 'name_asc' ? 'selected' : ''); ?>>Abjad (A-Z)</option>
                    <option value="name_desc" <?php echo e(request('sort') == 'name_desc' ? 'selected' : ''); ?>>Abjad (Z-A)</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="btn-primary w-full sm:w-auto px-6 !py-2.5"><?php echo e(__('messages.search')); ?></button>
            </div>
        </div>
    </form>
</div>
<div class="card overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full">
        <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
            <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.name')); ?></th><th class="px-5 py-3 text-left table-header">Slug</th>
            <th class="px-5 py-3 text-left table-header">Type</th><th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.total_products')); ?></th><th class="px-5 py-3 text-left table-header print:hidden print-hidden action-col"><?php echo e(__('messages.actions')); ?></th>
        </tr></thead><tbody>
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr onclick="window.location='<?php echo e(route('categories.show', $c)); ?>'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150 cursor-pointer">
            <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($c->name); ?></td>
            <td class="px-5 py-3 type-caption text-[var(--color-steel)]"><?php echo e($c->slug); ?></td>
            <td class="px-5 py-3"><span class="badge badge-neutral"><?php echo e($c->type); ?></span></td>
            <td class="px-5 py-3 type-body-sm text-[var(--color-charcoal)]"><?php echo e($c->products_count); ?></td>
            <td class="px-5 py-3 print:hidden print-hidden action-col"><div class="flex items-center gap-1">
                <button @click.stop="$dispatch('open-delete-modal', { url: '<?php echo e(route('categories.destroy', $c)); ?>' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20"><?php echo e(__('messages.delete')); ?></button>
            </div></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="5" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]"><?php echo e(__('messages.no_data')); ?></td></tr><?php endif; ?>
        </tbody></table></div>
    <?php if(method_exists($categories, 'links')): ?>
    <div class="border-t border-[var(--color-hairline-soft)] px-5 py-3 print:hidden print-hidden"><?php echo e($categories->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/categories/index.blade.php ENDPATH**/ ?>