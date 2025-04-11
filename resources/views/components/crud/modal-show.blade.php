<div x-data="{ isOpen: false }" class="relative">
    <!-- Trigger Button -->
    <a href="#" @click.prevent="isOpen = true" class="inline-block p-2 bg-blue-300 rounded-lg">
        <svg class="w-5 h-5 text-stone-900" fill="currentColor" viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M7.5 11C4.80285 11 2.52952 9.62184 1.09622 7.50001C2.52952 5.37816 4.80285 4 7.5 4C10.1971 4 12.4705 5.37816 13.9038 7.50001C12.4705 9.62183 10.1971 11 7.5 11ZM7.5 3C4.30786 3 1.65639 4.70638 0.0760002 7.23501C-0.0253338 7.39715 -0.0253334 7.60288 0.0760014 7.76501C1.65639 10.2936 4.30786 12 7.5 12C10.6921 12 13.3436 10.2936 14.924 7.76501C15.0253 7.60288 15.0253 7.39715 14.924 7.23501C13.3436 4.70638 10.6921 3 7.5 3ZM7.5 9.5C8.60457 9.5 9.5 8.60457 9.5 7.5C9.5 6.39543 8.60457 5.5 7.5 5.5C6.39543 5.5 5.5 6.39543 5.5 7.5C5.5 8.60457 6.39543 9.5 7.5 9.5Z"
                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg>
    </a>

    <!-- Modal Dialog -->
    <div x-cloak x-show="isOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-6xl p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">{{ $title }}</h3>
                <button @click="isOpen = false" class="text-3xl text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
