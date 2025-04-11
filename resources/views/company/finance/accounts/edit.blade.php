<!-- Modal Edit Setting -->
<div x-cloak x-data="{ open: false }" @edit-modal.window="open = true">
    <div x-show="open" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Account</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="modal-body">
                <form id="editDataForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    @include('company.finance.accounts.partials.dataform', ['form' => ['id' => 'Edit Account', 'mode' => 'edit']])
                </form>
            </div>
        </div>
    </div>
</div>