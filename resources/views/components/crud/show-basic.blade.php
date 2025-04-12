@props([
    'header' => 'Index',
    'model' => 'index',
    'table_id' => 'indexTable',
    'thead' => [],
    'tbody' => [],
])

@php
    $layout = $layout ?? session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Inventory Transfer Details: {{ $inventory_transfers->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">Inventory Transfer Details</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6 mb-6">
                        <x-div-box-show title="Inventory Transfer Number">{{ $inventory_transfers->number ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Shipper">{{ $inventory_transfers->shipper_type ?? 'N/A' }} : {{ $inventory_transfers->shipper->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Origin Address">{{ $inventory_transfers->origin_address ?? 'N/A' }}</x-div-box-show>
                        
                        <x-div-box-show title="Inventory Transfer Date">{{ $inventory_transfers->date?->format('Y-m-d') ?? 'N/A' }}</x-div>
                        <x-div-box-show title="Consignee">{{ $inventory_transfers->consignee_type ?? 'N/A' }} : {{ $inventory_transfers->consignee->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Destination Address">{{ $inventory_transfers->destination_address ?? 'N/A' }}</x-div-box-show>

                        <x-div-box-show title="Expedition Kurir">{{ $inventory_transfers->courier->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Admin Handler">{{ $inventory_transfers->admin?->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Team Request">{{ $inventory_transfers->team?->companyuser->user->name ?? 'N/A' }}</x-div-box-show>

                        <x-div-box-show title="Status">
                            <p
                                class="text-lg font-medium inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $inventory_transfers->status ?? 'N/A' }}
                            </p>
                        </x-div-box-show>
                        <x-div-box-show title="Admin Notes">{{ $inventory_transfers->admin_notes ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Team Notes">{{ $inventory_transfers->team_notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <h3 class="text-lg font-bold mt-6">Items</h3>
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>#</x-table-th>
                                <x-table-th>Item</x-table-th>
                                <x-table-th>Quantity</x-table-th>
                                <x-table-th>Cost</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach ($inventory_transfers->items as $index => $item)
                                <x-table-tr>
                                    <x-table-td>{{ $item->item_id }}</x-table-td>
                                    <x-table-td>{{ $item->item_type }} : {{ $item->item?->product->name }}</x-table-td>
                                    <x-table-td>{{ $item->quantity ?? 'N/A'}}</x-table-td>
                                    <x-table-td>{{ $item->cost_per_unit ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $item->notes ?? 'N/A' }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                        </div>
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <h3 class="text-lg font-bold mt-6">Outbounds</h3>
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Number</x-table-th>
                                <x-table-th>Date</x-table-th>
                                <x-table-th>Admin</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Notes</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach($inventory_transfers->outbounds as $outbound)
                                <x-table-tr>
                                    <x-table-td>{{ $outbound->id }}</x-table-td>
                                    <x-table-td>{{ $outbound->number }}</x-table-td>
                                    <x-table-td>{{ $outbound->date?->format('Y-m-d') ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $outbound->employee?->companyuser->user->name ?? 'N/A' }}</x-table-td>
                                    <x-table-td>{{ $outbound->status }}</x-table-td>
                                    <x-table-td>{{ $outbound->notes }}</x-table-td>
                                    <x-table-td>
                                        <x-button-show :route="route('warehouse_outbounds.show', $outbound->id)" />
                                    </x-table-td>
                                </x-table-tr>
                            @endforeach
                        </x-table-tbody>
                    </x-table-table>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @if ($inventory_transfers->status == 'ITF_REQUEST')
                        <div>
                            <div class="flex justify mt-4">
                                <form action="{{ route('inventory_transfers.action', ['id'=> $inventory_transfers->id, 'action' => 'ITF_PROCESS']) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <x-primary-button type="submit">Process Request Inventory Transfer</x-primary-button>
                                </form>
                            </div>
                        </div>
                        @elseif ($inventory_transfers->status == 'ITF_PROCESS' 
                                && $itf_ready_to_outbound)
                            <div class="flex justify-end m-4">
                                <form action="{{ route('inventory_transfers.action', ['id' => $inventory_transfers->id, 'action' => 'ITF_OUTBOUND']) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <x-primary-button type="submit">Buat Outbound Request ke Shipper</x-primary-button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

                    <!-- Back Button -->
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('inventory_transfers.index') }}">Back to List</a>
                        </x-secondary-button>
                        @if($inventory_transfers->status == 'ITF_PROCESS' &&
                            $layout == 'company')
                            <x-button href="{{ route('inventory_transfers.edit', $inventory_transfers->id) }}" text="Edit Inventory Transfer"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">Edit Inventory Transfer</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
