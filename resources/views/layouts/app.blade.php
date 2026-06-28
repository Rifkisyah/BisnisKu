<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BisnisKu') - {{ \App\Models\Setting::get('store_name', 'Nama Toko') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:300,400,500,600,700,800" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <style>
        @media (min-width: 640px) {
            .filter-w-responsive { max-width: 200px !important; }
            .btn-w-responsive { max-width: 150px !important; }
        }
        @media (max-width: 639px) {
            .filter-w-responsive, .btn-w-responsive { width: 100% !important; max-width: 100% !important; }
        }

        /* GLOBAL PRINT STYLES FOR ALL MODULES */
        @media print {
            @page { margin: 10mm; } /* Let user/browser decide orientation */
            body, html, .h-full, .min-h-screen, #app { 
                background: white !important; color: black !important; font-size: 10px !important; 
                height: auto !important; min-height: auto !important; overflow: visible !important; 
            }
            
            /* Hide Layout */
            aside, header, #dashboard-filter-form, .print-hidden, .pill-tab, select, button, a.btn, a.pill-tab { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; background: white !important; width: 100% !important; max-width: 100% !important; height: auto !important; overflow: visible !important; }
            
            /* Cards & Panels - Clean for Print */
            .card, .card-feature, .bg-white.rounded-2xl { 
                box-shadow: none !important; border: none !important; page-break-inside: auto; break-inside: auto; 
                margin: 0 !important; padding: 0 !important; background: transparent !important;
            }
            
            /* Tables sizing - dense */
            .overflow-x-auto, .overflow-hidden { overflow: visible !important; }
            table { width: 100% !important; border-collapse: collapse !important; margin-top: 5px !important; page-break-inside: auto; }
            th, td { border: 1px solid #000 !important; padding: 4px 6px !important; font-size: 10px !important; color: #000 !important; }
            th { background-color: #f3f4f6 !important; font-weight: bold !important; text-transform: uppercase; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            
            /* Badges */
            .badge, .badge-success, .badge-attention, .badge-critical, .badge-neutral, .badge-warning { background: transparent !important; color: #000 !important; border: none !important; padding: 0 !important; font-weight: bold !important; }
            
            /* Global fixes */
            * { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                border-radius: 0 !important;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[var(--color-canvas)] font-[Montserrat] transition-colors duration-300" x-data="{ sidebarOpen: true, mobileSidebar: false }">

{{-- Flash Messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-1rem]"
     x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed top-4 right-4 z-[100] max-w-md">
    <div class="badge-success flex items-center gap-3 !rounded-[var(--radius-xl)] !px-5 !py-3.5 shadow-lg" style="font-size:14px;font-weight:500">
        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="ml-2 opacity-70 hover:opacity-100"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
</div>
@endif
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     x-transition class="fixed top-4 right-4 z-[100] max-w-md">
    <div class="badge-critical flex items-center gap-3 !rounded-[var(--radius-xl)] !px-5 !py-3.5 shadow-lg" style="font-size:14px;font-weight:500">
        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('error') }}</span>
        <button @click="show = false" class="ml-2 opacity-70 hover:opacity-100"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
</div>
@endif

{{-- Delete Confirmation Modal --}}
<div x-data="deleteModal()" x-show="open" x-cloak x-on:open-delete-modal.window="openModal($event.detail)"
     class="fixed inset-0 z-[90] flex items-center justify-center p-4">
    <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[var(--color-ink-deep)]/40 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-sm rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-2xl">
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[var(--color-critical)]/10">
                <svg class="h-7 w-7 text-[var(--color-critical)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ __('messages.confirm_delete_title') }}</h3>
            <p class="mt-2 type-body-sm text-[var(--color-slate)]">{{ __('messages.confirm_delete') }}</p>
        </div>
        <div class="mt-6 flex gap-3">
            <button @click="open = false" class="btn-ghost flex-1">{{ __('messages.cancel') }}</button>
            <form :action="deleteUrl" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-[var(--radius-full)] bg-[var(--color-critical)] px-6 py-3 text-sm font-bold text-white transition-all duration-150">{{ __('messages.delete') }}</button>
            </form>
        </div>
    </div>
</div>

<div class="flex h-full">
    {{-- Sidebar --}}
    {{-- Mobile overlay --}}
    <div x-show="mobileSidebar" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" @click="mobileSidebar = false"
         class="fixed inset-0 z-40 bg-[var(--color-ink-deep)]/30 backdrop-blur-sm lg:hidden" x-cloak></div>

    <aside :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-[var(--color-hairline-soft)] bg-[var(--color-canvas)] transition-transform duration-300 lg:relative lg:z-auto lg:translate-x-0"
           x-bind:class="{ 'lg:w-64': sidebarOpen, 'lg:w-20': !sidebarOpen, 'translate-x-0': mobileSidebar, '-translate-x-full': !mobileSidebar }">

        {{-- Logo --}}
        <div class="flex h-16 items-center gap-3 border-b border-[var(--color-hairline-soft)] px-5">
            @php
                $storeLogo = \App\Models\Setting::get('store_logo');
                $storeName = \App\Models\Setting::get('store_name', 'Nama Toko');
            @endphp
            @if($storeLogo)
                <img src="{{ asset($storeLogo) }}" alt="Logo" class="h-9 w-9 shrink-0 object-cover rounded-[var(--radius-xl)]">
            @else
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[var(--radius-xl)] bg-[var(--color-primary)] text-white font-bold text-sm">BK</div>
            @endif
            <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                <h1 class="text-base font-bold text-[var(--color-ink-deep)] tracking-tight">BisnisKu</h1>
                <p class="text-[10px] text-[var(--color-stone)] truncate">{{ $storeName }}</p>
            </div>
            <button @click="mobileSidebar = false" class="ml-auto text-[var(--color-stone)] hover:text-[var(--color-ink)] lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @php $r = auth()->user()->role->name; @endphp

            <x-nav-link route="dashboard" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>' :label="__('messages.dashboard')" :sidebar-open="true" />

            @if(in_array($r, ['owner', 'kasir']))
            <x-nav-link route="cashier.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>' :label="__('messages.pos')" :sidebar-open="true" />

            @endif

            <x-nav-link route="products.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>' :label="__('messages.products')" :sidebar-open="true" />
            <x-nav-link route="categories.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>' :label="__('messages.categories')" :sidebar-open="true" />

            @if($r === 'owner')
            <x-nav-link route="suppliers.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>' :label="__('messages.suppliers')" :sidebar-open="true" />
            <x-nav-link route="employees.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>' :label="__('messages.employees')" :sidebar-open="true" />
            <x-nav-link route="product-purchases.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>' :label="__('messages.procurements')" :sidebar-open="true" />
            @endif

            @if(in_array($r, ['owner', 'teknisi']))
            <x-nav-link route="service-repairs.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>' :label="__('messages.service_repairs')" :sidebar-open="true" />
            @endif

            @if($r === 'owner')
            <x-nav-link route="debts.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>' :label="__('messages.debts')" :sidebar-open="true" />

            <x-nav-link route="reports.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>' :label="__('messages.reports')" :sidebar-open="true" />

            <div class="pt-4 mt-4 border-t border-[var(--color-hairline-soft)]"></div>
            <x-nav-link route="settings.index" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>' :label="__('messages.settings')" :sidebar-open="true" />
            @endif
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex flex-1 flex-col overflow-hidden">
        {{-- Topbar --}}
        <header class="flex h-16 items-center justify-between border-b border-[var(--color-hairline-soft)] bg-[var(--color-canvas)] px-4 lg:px-8">
            <div class="flex items-center gap-3">
                <button @click="mobileSidebar = !mobileSidebar" class="rounded-full p-2 text-[var(--color-slate)] hover:bg-[var(--color-surface-soft)] lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <button @click="sidebarOpen = !sidebarOpen" class="hidden rounded-full p-2 text-[var(--color-slate)] hover:bg-[var(--color-surface-soft)] lg:block">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">@yield('page-title', __('messages.dashboard'))</h2>
            </div>
            <div class="flex items-center gap-3">
                {{-- Language Switcher --}}
                <form action="{{ route('locale.switch') }}" method="POST" class="flex items-center bg-[var(--color-surface-soft)] rounded-full p-1 border border-[var(--color-hairline-soft)]">
                    @csrf
                    <button type="submit" name="locale" value="id" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'id' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">ID</button>
                    <button type="submit" name="locale" value="en" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[var(--color-ink-deep)] text-[var(--color-canvas)] shadow-sm' : 'text-[var(--color-slate)] hover:text-[var(--color-ink)]' }}">EN</button>
                </form>
                {{-- Theme Switcher --}}
                <button @click="theme = theme === 'light' ? 'dark' : 'light'" class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--color-surface-soft)] text-[var(--color-ink)] hover:bg-[var(--color-hairline-soft)] transition-all">
                    <svg x-show="theme === 'light'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-cloak x-show="theme === 'dark'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                {{-- User Menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-[var(--radius-full)] bg-[var(--color-surface-soft)] px-3 py-2 hover:bg-[var(--color-hairline-soft)] transition-all duration-150">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[var(--color-primary)] text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</div>
                        <div class="hidden text-left sm:block">
                            <p class="type-body-sm-bold text-[var(--color-ink)]">{{ auth()->user()->username }}</p>
                            <p class="type-caption text-[var(--color-steel)]">{{ auth()->user()->role->name }}</p>
                        </div>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-2 w-48 rounded-[var(--radius-xl)] bg-[var(--color-canvas)] py-2 shadow-lg border border-[var(--color-hairline-soft)]">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 type-body-sm text-[var(--color-charcoal)] hover:bg-[var(--color-surface-soft)] transition-all duration-150">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-[var(--color-surface-soft)] p-4 lg:p-8">
            @if($errors->any())
            <div class="mb-4 card !rounded-[var(--radius-xl)] !border-[var(--color-critical-strong)] p-4">
                <ul class="list-disc pl-5 type-body-sm text-[var(--color-critical)] space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script>
function deleteModal() {
    return {
        open: false,
        deleteUrl: '',
        openModal(detail) { this.deleteUrl = detail.url; this.open = true; }
    }
}
function formatRupiah(number) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
}
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('input-rupiah')) {
        let val = e.target.value.replace(/[^0-9]/g, '');
        if (val) e.target.value = new Intl.NumberFormat('id-ID').format(val);
        else e.target.value = '';
    } else if (e.target.tagName === 'INPUT' && (e.target.inputMode === 'numeric' || e.target.type === 'number')) {
        let val = e.target.value.replace(/[^0-9]/g, '');
        if (e.target.maxLength > 0 && e.target.maxLength !== 524288) {
            val = val.slice(0, e.target.maxLength);
        }
        if (e.target.value !== val) {
            e.target.value = val;
        }
    }
});
document.addEventListener('submit', function (e) {
    e.target.querySelectorAll('.input-rupiah').forEach(input => {
        input.value = input.value.replace(/\./g, '');
    });
});
</script>
@stack('scripts')
</body>
</html>
