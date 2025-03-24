<x-company-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-lg font-bold dark:text-white mb-4">Manage Sales Orders</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your sales orders.</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                       

                        <div class="w-full md:w-auto flex justify-end">
                            <a href="{{ route('sales.create') }}">
                                <x-button-add :route="route('sales.create')" text="Tambah Sales Order" />
                            </a>
                        </div>
                    </div>

                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
								<x-table-th>SO</x-table-th>
								<x-table-th>Date</x-table-th>
								<x-table-th>Warehouse</x-table-th>
                                <x-table-th>Consignee</x-table-th>
								<x-table-th>Total Amount</x-table-th>
								<x-table-th>Team</x-table-th>
								<x-table-th>Status</x-table-th> <!-- New column -->
								<x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($sales as $sale)
                                <x-table-tr>
                                    <x-table-td>{{ $sale->number }}</x-table-td>
                                    <x-table-td>{{ $sale->date }}</x-table-td>
                                    <x-table-td>{{ $sale->warehouse->name }}</x-table-td>
                                    <x-table-td>
                                        {{ $sale?->consignee_type ?? 'N/A' }} : {{ $sale->consignee?->name ?? 'N/A' }}
                                    </x-table-td>
                                    <x-table-td>{{ $sale->total_amount }}</x-table-td>
                                    <x-table-td>{{ $sale->employee->companyuser->user->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $sale->status }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                            <x-button-show :route="route('sales.show', $sale->id)" />
                                            @if ($sale->status == 'SO_OFFER' ||
                                                $sale->status == 'SO_REQUEST')
                                                <x-button-delete :route="route('sales.destroy', $sale->id)" />
                                            @endif
                                        </div>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>
