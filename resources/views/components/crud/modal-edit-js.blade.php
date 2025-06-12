<!-- Modal Edit Setting -->
<div x-data="{ isOpen: false }" 
    @edit-modal-js.window="isOpen = true" 
    @close-edit-modal-js.window="isOpen = false"
    @keydown.escape.window="isOpen = false"
    class="relative">
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
            <div class="modal-body overflow-y-auto max-h-[70vh] pr-2">
                <form id="editDataForm" method="POST">
                    @csrf
                    @method('PUT')

                    {{ $slot }}
                </form>
            </div>
        </div>
    </div>
</div>