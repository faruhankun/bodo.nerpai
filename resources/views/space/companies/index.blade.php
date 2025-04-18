<x-lobby-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
            {{ __('Our Company') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h1 class="text-2xl font-bold mb-6">Company List</h1>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                                @include('space.companies.create')
                         </div>
                    </div>

                    <!-- company Table -->
                    <x-table-table id="search-table">
                        <x-table-thead>
                            <tr>
                                <x-table-th>ID</x-table-th>
                                <x-table-th>Code</x-table-th>
                                <x-table-th>Name</x-table-th>
                                <x-table-th>Location</x-table-th>
                                <x-table-th>Database</x-table-th>
                                <x-table-th>Status</x-table-th>
                                <x-table-th>Actions</x-table-th>
                            </tr>
                        </x-table-thead>
                        <x-table-tbody>
                            @foreach (\App\Models\User::find(auth()->user()->id)->companies as $company)
                                <x-table-tr>
                                    <x-table-td>{{ $company->id }}</x-table-td>
                                    <x-table-td>{{ $company->code }}</x-table-td>
                                    <x-table-td>{{ $company->name }}</x-table-td>
                                    <x-table-td>{{ $company->address }}</x-table-td>
                                    <x-table-td>{{ $company->database ? parse_url($company->database)['host'] ?? $company->database->name : 'Internal' }}</x-table-td>
                                    <x-table-td>{{ $company->pivot->status }}</x-table-td>
                                    <x-table-td>
                                        <div class="flex items-center space-x-2">
                                            @if($company->pivot->status == 'approved')
                                                <form method="POST" action="{{ route('companies.switch', $company->id) }}">
                                                    @csrf
                
                                                    <x-primary-button :href="route('companies.switch', $company->id)" onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                                        {{ __('Masuk Company') }}
                                                    </x-primary-button>
                                                </form>
                                            @elseif($company->pivot->status == 'invited')
                                                <form action="{{ route('companies.rejectInvite', $company->id) }}" method="POST">
                                                    @csrf
                                                    <x-secondary-button type="submit">Reject Invite</x-secondary-button>
                                                </form>
                                                <form method="POST" action="{{ route('companies.acceptInvite', $company->id) }}">
                                                    @csrf
                                                    <x-primary-button :href="route('companies.acceptInvite', $company->id)" onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                                        {{ __('Approve Invitation') }}
                                                        </x-primary-button>
                                                </form>
                                            @endif
                                            {{-- @include('companies.edit', ['Tenant' => $company]) --}}
                                            <x-button-delete :route="route('companies.destroy', $company->id)" />
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
    
</x-lobby-layout>
