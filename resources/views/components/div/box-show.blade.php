<div {{ $attributes->merge(['class' => 'p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600']) }}>
    <p class="text-sm text-gray-500 dark:text-gray-300">{{ $title }}</p>
    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{!! $slot !!}</p>
</div>