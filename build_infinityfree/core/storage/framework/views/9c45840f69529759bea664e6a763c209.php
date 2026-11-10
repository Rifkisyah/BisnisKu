<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['route', 'icon', 'label', 'sidebarOpen' => true]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['route', 'icon', 'label', 'sidebarOpen' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php 
$baseRoute = preg_replace('/\.index$/', '', $route);
$active = request()->routeIs($baseRoute . '.*') || request()->routeIs($route); 
?>
<a href="<?php echo e(route($route)); ?>"
   class="sidebar-link <?php echo e($active ? 'sidebar-link-active' : ''); ?>">
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
    <span x-show="sidebarOpen" x-transition class="truncate"><?php echo e($label); ?></span>
</a>
<?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/components/nav-link.blade.php ENDPATH**/ ?>