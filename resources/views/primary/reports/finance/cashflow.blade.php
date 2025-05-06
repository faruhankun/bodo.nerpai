@php
    $layout = session('layout');

    $start_date = request('start_date') ?? now()->startOfMonth()->format('Y-m-d');
    $end_date = request('end_date') ?? now()->endOfMonth()->format('Y-m-d');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <div class="my-6 flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="text-left mb-4">
                            <h3 class="text-2xl font-bold dark:text-white">Laporan Cashflow</h3>
                            <p class="text-sm dark:text-gray-200 mb-6">Periode : {{ $start_date }} - {{ $end_date }}</p>
                        </div>
                            
                        <div class="justify-end mb-4">
                            <label for="cashflow_method" class="font-bold"></label>
                            <x-input-select name="cashflow_method" id="cashflow_method" class="border rounded p-2" onchange="toggleCashflowMethod()">
                                <option value="indirect">Metode Tidak Langsung</option>
                                <option value="direct">Metode Langsung</option>
                            </x-input-select>
                        </div>
                    </div>

                    <div class="my-3 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div id="cashflow" class="w-full">
                        @php
                            $method = request('method', 'indirect'); // Default Tidak Langsung
                            $cogs = $accounts->where('type_id', 13)->sum('balance');
                            $sales = $accounts->where('type_id', 12)->sum('balance');
                            
                            $netIncome =  ($accounts->where('type_id', 12)->sum('balance') 
                                        + $accounts->where('type_id', 13)->sum('balance') 
                                        + $accounts->where('type_id', 14)->sum('balance') 
                                        - $accounts->where('type_id', 15)->sum('balance') 
                                        + $accounts->where('type_id', 16)->sum('balance'));
                            
                            $changeReceivables =  ($accounts->where('type_id', 2)->sum('balance'));
                            $changeInventory =  $accounts->where('type_id', 3)->sum('balance');
                            $changePayables =  $accounts->where('type_id', 8)->sum('balance');
                            
                            $purchaseFixedAssets =  $accounts->where('type_id', 5)->sum('balance');
                            
                            $loansReceived =  ($accounts->where('type_id', 9)->sum('balance')
                                                + $accounts->where('type_id', 10)->sum('balance'));
                            $equityChanges =  $accounts->where('type_id', 11)->sum('balance');
                            
                            $endingCash = $accounts->where('type_id', 1)->sum('balance');
                            
                            $cashFromOperations = $netIncome + $changeReceivables + $changeInventory + $changePayables;
                            $cashFromInvesting = $purchaseFixedAssets;
                            $cashFromFinancing = $loansReceived + $equityChanges;
                            $netCashFlow = $cashFromOperations + $cashFromInvesting + $cashFromFinancing;
                            $initialCash = $endingCash - $netCashFlow;
                        @endphp

                        <div id="cashflow_indirect" class="cashflow-section">
                            <h2 class="text-xl font-bold mb-2">Laporan Arus Kas (Metode Tidak Langsung)</h2>
                            <x-table-table>
                                <x-table-tr>
                                    <x-table-th colspan="2" class="font-bold text-l">Arus Kas dari Aktivitas Operasi</x-table-th>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Laba Bersih</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($netIncome, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Perubahan Piutang</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($changeReceivables, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Perubahan Persediaan</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($changeInventory, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Perubahan Hutang Usaha</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($changePayables, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr class="font-bold">
                                    <x-table-td>Total Kas dari Aktivitas Operasi</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($cashFromOperations, 2) }}</x-table-td>
                                </x-table-tr>

                            </x-table-table>
                        </div>

                        <div id="cashflow_direct" class="cashflow-section hidden">
                            <h2 class="text-xl font-bold mb-2">Laporan Arus Kas (Metode Langsung)</h2>
                            <x-table-table>
                                <x-table-tr>
                                    <x-table-th colspan="2" class="font-bold text-l">Arus Kas dari Aktivitas Operasi</x-table-th>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Penerimaan Kas dari Pelanggan</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($netIncome + $changeReceivables, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Pembayaran ke Pemasok</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($accounts->where('code', '5-501')->sum('balance'), 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Pembayaran Biaya Operasional</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($accounts->where('code', '6-601')->sum('balance'), 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr class="font-bold">
                                    <x-table-td>Total Kas dari Aktivitas Operasi</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($cashFromOperations, 2) }}</x-table-td>
                                </x-table-tr>
                            </x-table-table>
                        </div>
                        
                        <div id="cashflow_all">
                            <br>
                            <x-table-table>
                                <x-table-tr>
                                    <x-table-th colspan="2" class="font-bold text-l">Arus Kas dari Aktivitas Investasi</x-table-th>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Pembelian Aset Tetap</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($purchaseFixedAssets, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr class="font-bold">
                                    <x-table-td>Total Kas dari Aktivitas Investasi</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($cashFromInvesting, 2) }}</x-table-td>
                                </x-table-tr>
                            </x-table-table>
                            <br>
                            <x-table-table>
                                <x-table-tr>
                                    <x-table-th colspan="2" class="font-bold text-l">Arus Kas dari Aktivitas Pendanaan</x-table-th>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Penerimaan Pinjaman</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($loansReceived, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Perubahan Ekuitas</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($equityChanges, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr class="font-bold">
                                    <x-table-td>Total Kas dari Aktivitas Pendanaan</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($cashFromFinancing, 2) }}</x-table-td>
                                </x-table-tr>
                            </x-table-table>
                            <br>
                            <x-table-table>
                                <x-table-tr>
                                    <x-table-th class="font-bold text-l">Total Arus Kas</x-table-th>
                                    <x-table-th class="text-right font-bold text-l">{{ number_format($netCashFlow, 2) }}</x-table-th>
                                </x-table-tr>
                                <x-table-tr>
                                    <x-table-td>Saldo Kas Awal</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($initialCash, 2) }}</x-table-td>
                                </x-table-tr>
                                <x-table-tr class="font-bold">
                                    <x-table-td>Saldo Kas Akhir</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($endingCash, 2) }}</x-table-td>
                                </x-table-tr>
                            </x-table-table>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="flex mt-8">
                        <x-secondary-button>
                            <a href="{{ route('summaries.index') }}">Back to Report</a>
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            
        });

        function toggleCashflowMethod() {
            let method = document.getElementById('cashflow_method').value;
            document.getElementById('cashflow_direct').classList.toggle('hidden', method !== 'direct');
            document.getElementById('cashflow_indirect').classList.toggle('hidden', method !== 'indirect');
        }
    </script>
</x-dynamic-component>
