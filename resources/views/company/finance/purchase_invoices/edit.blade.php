<x-company-layout>
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold dark:text-white">Edit Invoice: {{ $purchase_invoice->number }}</h1>
                    <p class="text-sm dark:text-gray-200 mb-3">Update the details of your purchase_invoice.</p>

                    <div class="p-2 border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-600 mb-4">
                        <form action="{{ route('purchase_invoices.update', $purchase_invoice->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <x-input-label for="date">Invoice Date</x-input-label>
                                <input type="date" name="date" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ ($purchase_invoice->date)->format('Y-m-d') }}">
                            </div>

                            <div class="mb-4">
                                <x-input-label for="due_date">Invoice Due Date</x-input-label>
                                <input type="date" name="due_date" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ ($purchase_invoice->due_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="form-group mb-4">
                                <x-input-label for="cost_products">Cost Products</x-input-label>
                                <input type="number" name="cost_products" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $purchase_invoice->cost_products ?? 0 }}" required>
                            </div>

                            <div class="form-group mb-4">
                                <x-input-label for="vat_input">PPN Masukan</x-input-label>
                                <input type="number" name="vat_input" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $purchase_invoice->vat_input ?? 0 }}" required>
                            </div>

                            <div class="form-group mb-4">
                                <x-input-label for="cost_packing">Cost Packing</x-input-label>
                                <input type="number" name="cost_packing" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $purchase_invoice->cost_packing ?? 0 }}" required>
                            </div>

                            <div class="form-group mb-4">
                                <x-input-label for="cost_insurance">Cost Insurance</x-input-label>
                                <input type="number" name="cost_insurance" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $purchase_invoice->cost_insurance ?? 0 }}" required>
                            </div>

                            <div class="form-group mb-4">
                                <x-input-label for="cost_freight">Cost Freight</x-input-label>
                                <input type="number" name="cost_freight" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" value="{{ $purchase_invoice->cost_freight ?? 0 }}" required>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="status">Invoice Status</x-input-label>
                                <input type="text" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" name="status" value="{{ $purchase_invoice->status }}" readonly>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="notes">Notes</x-input-label>
                                <textarea name="notes" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">{{ $purchase_invoice->notes }}</textarea>
                            </div>

                            <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
                            <div class="m-4">
                                <a href="{{ url()->previous() }}">
                                    <x-secondary-button type="button">Cancel</x-secondary-button>
                                </a>
                                <x-primary-button>Update Invoice</x-primary-button>
                            </div>
                        </form>

                        <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-company-layout>