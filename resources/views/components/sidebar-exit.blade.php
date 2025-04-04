@props([
    'item' => [
        'route' => 'lobby',
        'route_params' => [],
        'text' => 'Lobby',
    ],
])

<a href="{{ route($item['route'], $item['route_params']) }}"
    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
    <svg class="flex-shrink-0 w-4 h-4 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M1 8h11m0 0L8 4m4 4-4 4m4-4H3" />
    </svg>
    <span class="flex-1 ms-3 whitespace-nowrap">{{ $item['text'] }}</span>
</a>