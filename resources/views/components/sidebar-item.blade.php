@props([
    'icon' => 'icon-sidebar',
    'route' => '#',
    'text' => 'Lobby',
    'route_params' => [],
])

<a href="{{ route($route, $route_params) }}"
    class="flex items-center px-2 py-1 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
    @if ($icon)
        <x-dynamic-component :component="'icons.' . $icon" class="flex-shrink-0 w-4 h-4 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" />
    @endif
    <span class="ms-3 whitespace-nowrap">{{ $text }}</span>
</a>
