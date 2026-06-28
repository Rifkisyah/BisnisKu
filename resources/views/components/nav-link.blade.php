@props(['route', 'icon', 'label', 'sidebarOpen' => true])
@php $active = request()->routeIs($route . '*') || request()->routeIs($route); @endphp
<a href="{{ route($route) }}"
   class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
    <span x-show="sidebarOpen" x-transition class="truncate">{{ $label }}</span>
</a>
