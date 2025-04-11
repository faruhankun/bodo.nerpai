<div x-cloak x-data="{ open: false }" @create-modal.window="open = true">
    <div x-show="open" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Add Account</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="modal-body">
                <form id="createDataForm" method="POST" action="route('accounts.store')">
                    @csrf
                    
                    @include('company.finance.accounts.partials.dataform', ['form' => ['id' => 'Create Account']])
                </form>
            </div>
        </div>        
    </div>
</div>