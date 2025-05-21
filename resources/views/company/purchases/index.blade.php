<x-company-layout>
	<div class="py-12">
		<div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-white">
				<h3 class="text-lg font-bold dark:text-white">Manage Purchases</h3>
				<p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your purchase listings.</p>
				<div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

					<div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        
                        <div class="w-full md:w-auto flex justify-end">
                            <a href="{{ route('purchases.create') }}">
                                <x-button-add :route="route('purchases.create')" text="Add Purchases" />
                            </a>
                        </div>
                    </div>
					<div class="overflow-x-auto">
					<x-table.table-table class="table table-bordered" id="search-table">
						<x-table.table-thead class="thead-dark">
							<tr>
								<x-table.table-th>ID</x-table.table-th>
								<x-table.table-th>PO</x-table.table-th>
								<x-table.table-th>Date</x-table.table-th>
								<x-table.table-th>Supplier</x-table.table-th>
								<x-table.table-th>Warehouse</x-table.table-th>
								<x-table.table-th>Total Amount</x-table.table-th>
								<x-table.table-th>Team</x-table.table-th>
								<x-table.table-th>Status</x-table.table-th> <!-- New column -->
								<x-table.table-th>Actions</x-table.table-th>
							</tr>
						</x-table.table-thead>
						<x-table.table-tbody>
							@foreach ($purchases as $purchase)
								<x-table.table-tr>
									<x-table.table-td>{{ $purchase->id }}</x-table.table-td>
									<x-table.table-td>{{ $purchase->po_number }}</x-table.table-td>
									<x-table.table-td>{{ $purchase->po_date }}</x-table.table-td>
									<x-table.table-td>{{ $purchase->supplier->name ?? 'N/A' }}</x-table.table-td>
									<x-table.table-td>{{ $purchase->warehouse->name ?? 'N/A' }}</x-table.table-td>
									<x-table.table-td>{{ $purchase->total_amount }}</x-table.table-td>
									<x-table.table-td>{{ $purchase->employee->companyuser->user->name }}</x-table.table-td>
									<x-table.table-td>
										@if ($purchase->status == 'Quantity Discrepancy')
											<span class="badge badge-warning">Quantity Discrepancy</span>
										@else
											{{ $purchase->status }}
										@endif
									</x-table.table-td>

									<x-table.table-td>
										<div class="flex items-center gap-3 justify-end">
											<x-button-show :route="route('purchases.show', $purchase->id)" />
											@if ($purchase->status == 'PO_PLANNED')
												<x-button-delete :route="route('purchases.destroy', $purchase->id)" />
											@else
												<x-button-add :route="route('purchases.duplicate', $purchase->id)" text="Copy"/>
											@endif
										</div>
									</x-table.table-td>
								</x-table.table-tr>
							@endforeach
						</x-table.table-tbody>
					</x-table.table-table>
				</div>
				</div>
			</div>
		</div>
	</div>
	
</x-company-layout>