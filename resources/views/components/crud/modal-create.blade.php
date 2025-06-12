<div x-data="{ isOpen: false }" class="relative">
    <!-- Trigger Button -->
    <a href="#" @click.prevent="isOpen = true" class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"></path>
        </svg>
        {{ $trigger ?? 'Create Item' }}
    </a>

    <!-- Modal Dialog -->
    <div x-cloak x-show="isOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div
            @click.away="isOpen = false" 
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-6xl p-6">
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
