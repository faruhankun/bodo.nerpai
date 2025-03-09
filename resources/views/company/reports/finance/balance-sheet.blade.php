@php
    $layout = session('layout');

    $date = request('date') ?? now()->format('d F Y');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold text-2xl text-xl dark:text-white">Laporan Neraca</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Tanggal: {{ $date }}</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div class="flex items-center justify-between mb-4">
                        @php
                            $assets = $accounts->whereBetween('type_id', [1, 7]);
                            $liabilities = $accounts->whereBetween('type_id', [8, 10]);
                            $equities = $accounts->where('type_id', 11);

                            $totalAssets = $assets->sum('balance');
                            $totalLiabilities = $liabilities->sum('balance');
                            $totalEquities = $equities->sum('balance');
                        @endphp

                        <x-table-table>
                            <x-table-thead>
                                <tr>
                                    <td colspan="1" class="font-bold text-2xl">Laporan Neraca</td>
                                    <x-table-td class="text-right font-bold text-2xl">{{ $date }}</x-table-td>
                                </tr>
                            </x-table-thead>
                            <x-table-tbody>
                                
                                <!-- ASET -->
                                <x-table-tr><x-table-td colspan="2" class="font-bold text-2xl">Aset</x-table-td></x-table-tr>
                                @foreach ($assets->groupBy('type_id') as $type => $accounts)
                                    <x-table-tr><x-table-td colspan="2" class="font-semibold">{{ $accounts->first()->account_type?->name }}</x-table-td></x-table-tr>
                                    @foreach ($accounts as $account)
                                        <x-table-tr>    
                                            <x-table-td class="pl-8">{{ $account->code }} - {{ $account->name }}</x-table-td>
                                            <x-table-td class="text-right">{{ number_format($account->balance, 2) }}</x-table-td>
                                        </x-table-tr>
                                    @endforeach
                                @endforeach
                                <x-table-tr>
                                    <td colspan="2" class="font-bold text-2xl">Total Aset</td>
                                    <td class="text-right font-bold">{{ number_format($totalAssets, 2) }}</td>
                                </x-table-tr>
                                <tr class="border-t border-gray-300 dark:border-gray-700"></tr>

                                <!-- LIABILITAS -->
                                <x-table-tr><x-table-td colspan="2" class="font-bold text-2xl">Liabilitas</x-table-td></x-table-tr>
                                @foreach ($liabilities->groupBy('type_id') as $type => $accounts)
                                    <x-table-tr><x-table-td colspan="2" class="pl-4 font-semibold">{{ $accounts->first()->account_type?->name }}</x-table-td></x-table-tr>
                                    @foreach ($accounts as $account)
                                        <x-table-tr>
                                            <x-table-td class="pl-8">{{ $account->code }} - {{ $account->name }}</x-table-td>
                                            <x-table-td class="text-right">{{ number_format($account->balance, 2) }}</x-table-td>
                                        </x-table-tr>
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td colspan="2" class="font-bold text-2xl">Total Liabilitas</td>
                                    <td class="text-right font-bold">{{ number_format($totalLiabilities, 2) }}</td>
                                </tr>

                                <!-- EKUITAS -->
                                <x-table-tr><x-table-td colspan="2" class="font-bold text-2xl">Ekuitas</x-table-td></x-table-tr>
                                @foreach ($equities as $account)
                                    <x-table-tr>
                                        <x-table-td class="pl-4">{{ $account->code }} - {{ $account->name }}</x-table-td>
                                        <x-table-td class="text-right">{{ number_format($account->balance, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="font-bold text-2xl">Total Ekuitas</td>
                                    <td class="text-right font-bold">{{ number_format($totalEquities, 2) }}</td>
                                </tr>
                                
                                <tr>
                                    <td colspan="2" class="font-bold text-2xl">Total Liabilitas & Ekuitas</td>
                                    <td class="text-right font-bold">{{ number_format($totalLiabilities + $totalEquities, 2) }}</td>
                                </tr>
                            </x-table-tbody>
                        </x-table-table>
                    </div>

                    <!-- Back Button -->
                    <div class="flex mt-8">
                        <x-secondary-button>
                            <a href="{{ route('reports.index') }}">Back to Report</a>
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</x-dynamic-component>
