<tr {{ $attributes->merge(['scope' => 'col', 'class' => 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-300']) }}>
    {{ $slot }}
</tr>
