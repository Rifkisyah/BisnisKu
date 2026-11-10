<?php $__env->startSection('page-title', __('messages.products')); ?>
<?php $__env->startSection('content'); ?>
<div x-data="{ viewMode: localStorage.getItem('productView') || (window.innerWidth < 768 ? 'grid' : 'table'), showFilter: false }" x-init="$watch('viewMode', val => localStorage.setItem('productView', val)); window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode !== 'grid') viewMode = 'grid'; })">
    
    <div class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)] print:hidden print-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
            <div class="flex items-center justify-between w-full sm:w-auto">
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Aksi & Cari</h2>
                <div class="flex sm:hidden bg-[var(--color-surface-soft)] rounded-lg p-1 border border-[var(--color-hairline-soft)]">
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1 rounded-md transition-all" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1 rounded-md transition-all" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
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
                <div class="hidden sm:flex bg-[var(--color-surface-soft)] rounded-lg p-1 border border-[var(--color-hairline-soft)]">
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1.5 rounded-md transition-all" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1.5 rounded-md transition-all" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
                <?php if(!auth()->user()->isKasir()): ?>
                <a href="<?php echo e(route('products.create')); ?>" class="btn-primary !py-2.5 !px-4 whitespace-nowrap text-center text-xs sm:text-sm w-full sm:w-auto justify-center flex items-center">
                    <svg class="h-4 w-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="inline-block"><?php echo e(__('messages.add')); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end w-full">
            <div class="w-full sm:flex-1">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('messages.search')); ?>..." class="input-field w-full">
            </div>
            <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap">
                <div class="w-full sm:w-48">
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Tipe Produk</label>
                    <select name="tab" class="input-field w-full" onchange="this.form.submit()">
                        <option value="" <?php echo e(request('tab', '') === '' ? 'selected' : ''); ?>><?php echo e(__('messages.all')); ?></option>
                        <option value="physical" <?php echo e(request('tab') === 'physical' ? 'selected' : ''); ?>><?php echo e(__('messages.physical')); ?></option>
                        <option value="digital" <?php echo e(request('tab') === 'digital' ? 'selected' : ''); ?>><?php echo e(__('messages.digital')); ?></option>
                        <option value="sparepart" <?php echo e(request('tab') === 'sparepart' ? 'selected' : ''); ?>>Sparepart (Servis)</option>
                    </select>
                </div>
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
            </div>

            <div class="w-full sm:w-auto">
                <label class="block type-caption-bold text-transparent mb-1 hidden sm:block">&nbsp;</label>
                <div class="h-[42px] flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer bg-[var(--color-canvas)] px-3 py-2 rounded-lg border border-[var(--color-hairline-soft)] hover:border-[var(--color-primary)] transition-colors h-full">
                        <input type="checkbox" name="show_sparepart" value="1" <?php echo e(request('show_sparepart') ? 'checked' : ''); ?> onchange="this.form.submit()" class="checkbox-field rounded border-gray-300 text-[var(--color-primary)] shadow-sm focus:border-[var(--color-primary)] focus:ring focus:ring-[var(--color-primary)] focus:ring-opacity-50">
                        <span class="type-body-sm text-[var(--color-ink)] font-medium">Tampilkan Sparepart</span>
                    </label>
                </div>
            </div>
            
            <div class="w-full sm:w-auto">
                <button type="submit" class="btn-primary w-full sm:w-auto px-6 !py-2.5"><?php echo e(__('messages.search')); ?></button>
            </div>
        </form>
    </div>

    
    <div x-show="viewMode === 'table'" class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.code')); ?></th>
                    <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.name')); ?></th>
                    <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.category')); ?></th>
                    <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.purchase_price')); ?></th>
                    <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.selling_price')); ?></th>
                    <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.stock')); ?></th>
                    <th class="px-5 py-3 text-left table-header print:hidden print-hidden action-col"><?php echo e(__('messages.actions')); ?></th>
                </tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr onclick="window.location='<?php echo e(route('products.show', $p)); ?>'" class="border-b border-[var(--color-hairline-soft)]/50 transition-colors duration-150 hover:bg-[var(--color-surface-soft)] cursor-pointer">
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]"><?php echo e($p->product_code); ?></td>
                    <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($p->name); ?></td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]"><?php echo e($p->category->name); ?></td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-charcoal)]">Rp <?php echo e(number_format($p->purchase_price, 0, ',', '.')); ?></td>
                    <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-ink)]">Rp <?php echo e(number_format($p->selling_price, 0, ',', '.')); ?></td>
                    <td class="px-5 py-3">
                        <?php if($p->isLowStock()): ?><span class="badge badge-critical"><?php echo e($p->stock); ?></span>
                        <?php else: ?><span class="type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($p->stock); ?></span><?php endif; ?>
                    </td>
                    <td class="px-5 py-3 print:hidden print-hidden action-col">
                        <div class="flex items-center gap-1">
                            <a href="<?php echo e(route('products.show', $p)); ?>" onclick="event.stopPropagation()" class="btn-ghost !py-1.5 !px-3 !text-xs"><?php echo e(__('messages.detail')); ?></a>
                            <?php if(!auth()->user()->isKasir()): ?>
                            <button @click.stop="$dispatch('open-delete-modal', { url: '<?php echo e(route('products.destroy', $p)); ?>' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20"><?php echo e(__('messages.delete')); ?></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]"><?php echo e(__('messages.no_data')); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 print:hidden print-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div onclick="window.location='<?php echo e(route('products.show', $p)); ?>'" class="card flex flex-col p-3 sm:p-4 hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 cursor-pointer">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-2 sm:mb-3 gap-1">
                <span class="type-caption-bold text-[var(--color-primary)] text-[10px] sm:text-xs truncate"><?php echo e($p->product_code); ?></span>
                <?php if($p->isLowStock()): ?><span class="badge badge-critical !px-1.5 !py-0.5 !text-[10px]">Stok: <?php echo e($p->stock); ?></span>
                <?php else: ?><span class="badge badge-neutral !px-1.5 !py-0.5 !text-[10px]">Stok: <?php echo e($p->stock); ?></span><?php endif; ?>
            </div>
            <div class="aspect-square bg-[var(--color-surface-soft)] rounded-lg mb-2 sm:mb-3 flex items-center justify-center">
                <span class="aspect-square bg-gray-100 overflow-hidden w-full h-full rounded-lg"><img src="https://placehold.co/600x400?text=No+Image" class="w-full h-full object-cover"></span>
            </div>
            <h4 class="type-caption-bold sm:type-body-sm font-medium text-[var(--color-ink)] line-clamp-2 mb-1"><?php echo e($p->name); ?></h4>
            <p class="text-[10px] sm:type-caption text-[var(--color-slate)] mb-2 sm:mb-3"><?php echo e($p->category->name); ?></p>
            <div class="mt-auto space-y-1">
                <div class="flex flex-col sm:flex-row justify-between text-[10px] sm:type-caption">
                    <span class="text-[var(--color-slate)] hidden sm:inline">Beli</span>
                    <span class="text-[var(--color-charcoal)] sm:ml-auto">Rp <?php echo e(number_format($p->purchase_price, 0, ',', '.')); ?></span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between type-caption-bold sm:type-body-sm">
                    <span class="text-[var(--color-slate)] hidden sm:inline">Jual</span>
                    <span class="font-bold text-[var(--color-ink)] text-[11px] sm:text-sm sm:ml-auto">Rp <?php echo e(number_format($p->selling_price, 0, ',', '.')); ?></span>
                </div>
                <div class="pt-3 mt-3 border-t border-[var(--color-hairline-soft)] flex justify-end gap-1">
                    <a href="<?php echo e(route('products.show', $p)); ?>" onclick="event.stopPropagation()" class="btn-ghost !py-1.5 !px-3 !text-xs"><?php echo e(__('messages.detail')); ?></a>
                    <?php if(!auth()->user()->isKasir()): ?>
                    <button @click.stop="$dispatch('open-delete-modal', { url: '<?php echo e(route('products.destroy', $p)); ?>' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20"><?php echo e(__('messages.delete')); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full py-12 text-center type-body-md text-[var(--color-stone)]"><?php echo e(__('messages.no_data')); ?></div>
        <?php endif; ?>
    </div>

    <?php if(method_exists($products, 'links')): ?>
    <div class="mt-4 border-t border-[var(--color-hairline-soft)] pt-4 print:hidden print-hidden"><?php echo e($products->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/products/index.blade.php ENDPATH**/ ?>