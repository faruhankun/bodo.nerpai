@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
	<div class="py-12">
		<div class=" sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
				<div class="p-6 text-gray-900 dark:text-white">
					<h3 class="text-2xl font-bold dark:text-white">Request Expense</h3>
					<div
						class="p-2 border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-600 mb-4">
						<form action="{{ route('expenses.store') }}" method="POST">
							@csrf
							
							<div class="mb-4">
								<x-input-label for="date">Expense Date</x-input-label>
								<input type="date" name="date"
									class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
									required  value="<?= date('Y-m-d'); ?>">
							</div>

							<div class="mb-4">
								<x-input-label for="type">Expense Type</x-input-label>
								<x-input-select name="type" id="type" class="w-full" required>
									@foreach ($types as $type)
										<option value="{{ $type }}">{{ $type }}</option>
									@endforeach
								</x-input-select>
							</div>

							<div class="mb-4">
								<x-input-label for="amount">Amount</x-input-label>
								<x-input-input type="number" name="amount" class="w-full" required/>
							</div>

							<div class="mb-4">
								<x-input-label for="description">Description</x-input-label>
								<x-input-textarea name="description" class="w-full" />
							</div>

							<div class="mb-4">
								<x-input-label for="notes">Notes</x-input-label>
								<x-input-textarea name="notes" class="w-full" />
							</div>

							<div class="mb-3 mt-3 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
							<div class="mb-4 flex justify-end">
								<a href="{{ route('expenses.index') }}">
                                    <x-secondary-button type="button">Cancel</x-secondary-button>
                                </a>
								<x-primary-button>Request Expense</x-primary-button>
							</div>						
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-dynamic-component>