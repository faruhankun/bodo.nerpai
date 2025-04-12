<!-- Modal Edit Setting -->
<div x-cloak x-data="{ open: false }" @edit-modal-js.window="open = true">
    <div x-show="open" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-6xl p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit {{ $title }}</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="modal-body">
                <form id="editDataForm" method="POST">
                    @csrf
                    @method('PUT')

                    {{ $slot }}
                </form>
            </div>
        </div>
    </div>
</div>