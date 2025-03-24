<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white">Manage Customers</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your customers listings.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Search and Add New Customer -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                       
                        <div class="w-full md:w-auto flex justify-end">
                            <x-button-add :route="route('customers.create')" text="Tambah Customer" />
                        </div>
                    </div>
                    
                    <div id="customers-page"></div>
                </div>
            </div>
        </div>
    </div>
    
</x-company-layout>

<script src="{{ mix('js/customers.js') }}"></script>