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
                    <h3 class="text-2xl font-bold text-xl dark:text-white">Laporan Laba Rugi</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Periode : {{ $start_date }} - {{ $end_date }}</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div id="profit_loss" class="w-full">
                        @php
                            $pendapatan = $accounts->where('type_id', 12);
                            $beban_pokok = $accounts->where('type_id', 13);
                            $biaya_operasional = $accounts->where('type_id', 14);
                            $pendapatan_lainnya = $accounts->where('type_id', 15);
                            $beban_lainnya = $accounts->where('type_id', 16);

                            $total_pendapatan = $pendapatan->sum('balance') * -1;
                            $total_beban_pokok = $beban_pokok->sum('balance');
                            $total_biaya_operasional = $biaya_operasional->sum('balance');
                            $total_pendapatan_lainnya = $pendapatan_lainnya->sum('balance') * -1;
                            $total_beban_lainnya = $beban_lainnya->sum('balance');

                            $laba_kotor = $total_pendapatan - $total_beban_pokok;
                            $laba_operasional = $laba_kotor - $total_biaya_operasional;
                            $laba_bersih = $laba_operasional + $total_pendapatan_lainnya - $total_beban_lainnya;
                        @endphp

                        <x-table-table>
                            <x-table-thead>
                                <x-table-tr>
                                    <x-table-td class="font-bold text-xl">Laporan Laba Rugi</x-table-td>
                                    <x-table-td class="text-right font-bold text-xl">Periode X - Y</x-table-td>
                                </x-table-tr>
                            </x-table-thead>
                            <x-table-tbody>
                                <x-table-tr>
                                    <x-table-td class="font-bold text-xl">Pendapatan</x-table-td>
                                </x-table-tr>
                                @foreach ($pendapatan as $item)
                                    <x-table-tr>
                                        <x-table-td>{{ $item->code}} - {{ $item->name }}</x-table-td>
                                        <x-table-td class="text-right">{{ number_format($item->balance * -1, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Total Pendapatan</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($total_pendapatan, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr>
                                    <x-table-td class="font-bold text-xl">Beban Pokok Penjualan</x-table-td>
                                </x-table-tr>
                                @foreach ($beban_pokok as $item)
                                    <x-table-tr>
                                        <x-table-td>{{ $item->code}} - {{ $item->name }}</x-table-td>
                                        <x-table-td class="text-right">{{ number_format($item->balance, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Total Beban Pokok Penjualan</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($total_beban_pokok, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Laba Kotor</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($laba_kotor, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr>
                                    <x-table-td class="font-bold text-xl">Biaya Operasional</x-table-td>
                                </x-table-tr>
                                @foreach ($biaya_operasional as $item)
                                    <x-table-tr>
                                        <x-table-td>{{ $item->code}} - {{ $item->name }}</x-table-td>
                                        <x-table-td class="text-right">{{ number_format($item->balance, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Total Biaya Operasional</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($total_biaya_operasional, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Laba Operasional</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($laba_operasional, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr>
                                    <x-table-td class="font-bold text-xl">Pendapatan Lainnya</x-table-td>
                                </x-table-tr>
                                @foreach ($pendapatan_lainnya as $item)
                                    <x-table-tr>
                                        <x-table-td>{{ $item->code}} - {{ $item->name }}</x-table-td>
                                        <x-table-td class="text-right">{{ number_format($item->balance * -1, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Total Pendapatan Lainnya</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($total_pendapatan_lainnya, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr>
                                    <x-table-td class="font-bold text-xl">Beban Lainnya</x-table-td>
                                </x-table-tr>
                                @foreach ($beban_lainnya as $item)
                                    <x-table-tr>
                                        <x-table-td>{{ $item->code}} - {{ $item->name }}</x-table-td>
                                        <x-table-td class="text-right">{{ number_format($item->balance, 2) }}</x-table-td>
                                    </x-table-tr>
                                @endforeach
                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Total Beban Lainnya</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($total_beban_lainnya, 2) }}</x-table-td>
                                </x-table-tr>

                                <x-table-tr class="font-bold text-xl">
                                    <x-table-td>Laba Bersih</x-table-td>
                                    <x-table-td class="text-right">{{ number_format($laba_bersih, 2) }}</x-table-td>
                                </x-table-tr>
                            </x-table-tbody>
                        </x-table-table>
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
    
</x-dynamic-component>
