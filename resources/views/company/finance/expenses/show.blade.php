
@php 
    $payment_methods = $payment_methods;
    if($expense->payment_method) {
        $payment_method = $payment_methods->where('id', $expense->payment_method)->first();
    }

    $layout = session('layout');
@endphp

<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Expense Details : {{ $expense->number }}</h1>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <h3 class="text-lg font-bold my-3">General Informations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <x-div-box-show title="Number">{{ $expense->number }}</x-div-box-show>
                        <x-div-box-show title="Date">{{ $expense->date?->format('Y-m-d') ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Type">{{ $expense->type }}</x-div-box-show>

                        <x-div-box-show title="Account">{{ $expense->account?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Payment Method">{{ $payment_method?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Amount">Rp{{ number_format($expense->amount, 2) }}</x-div-box-show>

                        <x-div-box-show title="Consignee">{{ $expense?->consignee_type ?? 'N/A' }} : {{ $expense->consignee?->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Status">{{ ucfirst($expense->status) }}</x-div-box-show>
                        <x-div-box-show title="Requested By">{{ $expense->requestedby?->companyuser->user->name ?? 'N/A' }}</x-div-box-show>

                        <x-div-box-show title="Approved By">{{ $expense->approvedby?->companyuser->user->name ?? 'N/A' }}</x-div-box-show>
                        <x-div-box-show title="Notes">{{ $expense->notes ?? 'N/A' }}</x-div-box-show>
                    </div>
                    <div class="mb-3 mt-1 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
            

                    <!-- Action Section -->
                    <h3 class="text-lg font-bold my-3">Actions</h3>
                    <div>
                        @if($expense->status == 'requested')
                            <div class="flex justify mt-4">
                                <div class="mr-2">
                                    <form action="{{ route('expenses.action', ['id' => $expense->id, 'action' => 'rejected']) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <x-secondary-button type="submit">Reject Expense</x-secondary-button>
                                    </form>
                                </div>
                                <div class="mr-2">
                                    <form action="{{ route('expenses.action', ['id' => $expense->id, 'action' => 'approved']) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <x-primary-button type="submit">Approve Expense</x-primary-button>
                                    </form>
                                </div>
                            </div>
                        @elseif ($expense->status == 'approved')
                            <form action="{{ route('expenses.action', ['id' => $expense->id, 'action' => 'process']) }}" method="POST">
                                @csrf
                                @method('POST')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                    <div class="form-group mt-4">
                                        <div class="border rounded-lg p-4 mt-4">
                                            <h2 class="text-lg font-bold mb-2">Account Pembayaran</h2>
                                            <x-input-select name="account_id" id="account_id" class="form-select w-full px-2 py-1" required>
                                                <option value="">-- Select Payment Method --</option>
                                                @foreach ($expense_accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                @endforeach
                                            </x-input-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify mt-4">
                                    <x-primary-button type="submit">Request Payment</x-primary-button>
                                </div>
                            </form>
                        @endif
                    </div>

                    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>


                    <div class="flex justify-end space-x-4">
                        <x-secondary-button>
                            <a href="{{ route('expenses.index') }}">Back to List</a>
                        </x-secondary-button>
                        @if($expense->status == 'approved')
                            <x-button href="{{route('expenses.edit', $expense->id)}}" text="Edit Expense"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-green-700 dark:hover:bg-green-800">Edit Expense</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>