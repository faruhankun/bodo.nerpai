<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <div class="p-6 text-gray-900 dark:text-white">
                        <h1 class="text-2xl dark:text-white font-bold">Create Sales Order</h1>
                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <form action="{{ route('sales.store') }}" method="POST">
                            @csrf

                            <!-- Select Customer -->
                            <div class="mb-4">
                                <x-input-label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</x-input-label>
                                <x-input-select name="customer_id" id="customer_id" class="form-control w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" required>
                                </x-input-select>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="date">Sale Date</x-input-label>
                                <input type="date" name="date"
                                    class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                    required value="<?= date('Y-m-d'); ?>" >
                            </div>

                            <div class="mb-4">
                                <x-input-label for="warehouse_id">Select Warehouse</x-input-label>
                                <select name="warehouse_id"
                                    class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                    required>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                            <div class="mb-4">
                                <a href="{{ route('sales.index') }}">
                                    <x-secondary-button type="button">Cancel</x-secondary-button>
                                </a>
                                <x-primary-button type="submit">Create Sale</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-company-layout>

<script>
    $(document).ready(function() {
        $('#customer_id').select2({
            placeholder: 'Select a customer',
            minimumInputLength: 2,
            ajax: {
                url: '/customers/search',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(customer => ({
                            id: customer.id,
                            text: customer.id + ' - ' + customer.name
                        }))
                    };
                },
                cache: true
            }
        });
    });
</script>