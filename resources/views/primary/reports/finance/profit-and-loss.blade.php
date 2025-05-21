@php
    $layout = session('layout');

    $start_date = $param['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
    $end_date = $param['end_date'] ?? now()->endOfMonth()->format('Y-m-d');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold dark:text-white">Laporan Laba Rugi</h3>
                    <div class="flex justify-end">
                        <form action="{{ route('summaries.show', 'profit-and-loss') }}" method="GET">
                            <div class="form-group">
                                <x-input.input-basic type="date" name="start_date" value="{{ $start_date }}" required></x-crud.input-basic>
                            </div>
                            <div class="form-group">
                                <x-input.input-basic type="date" name="end_date" value="{{ $end_date }}" required></x-crud.input-basic>
                            </div>
                            <div class="form-group">
                                <x-primary-button class="ml-4">Filter</x-primary-button>
                            </div>
                        </form>
                    </div>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div id="profit_loss" class="w-full">
                        <x-table.table-table>
                            <x-table.table-thead>
                                <x-table.table-tr>
                                    <x-table.table-td class="font-bold text-3xl">Laporan Laba Rugi</x-table.table-td>
                                    <x-table.table-td class="text-right font-bold text-2xl">Periode: {{ $start_date }} - {{ $end_date }}</x-table.table-td>
                                </x-table.table-tr>
                            </x-table.table-thead>
                            <x-table.table-tbody>
                                <x-table.table-tr>
                                    <x-table.table-td class="font-bold text-xl">Pendapatan</x-table.table-td>
                                </x-table.table-tr>
                                @foreach ($data['pendapatan'] as $item)
                                    <x-table.table-tr>
                                        <x-table.table-td class="pl-16">{{ $item->code}} - {{ $item->name }}</x-table.table-td>
                                        <x-table.table-td class="text-right pr-8">{{ number_format($item->balance , 2) }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td class="pl-8">Total Pendapatan</x-table.table-td>
                                    <x-table.table-td class="text-right pr-8">{{ number_format($data['total_pendapatan'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr>
                                    <x-table.table-td class="font-bold text-xl">Beban Pokok Penjualan</x-table.table-td>
                                </x-table.table-tr>
                                @foreach ($data['beban_pokok'] as $item)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $item->code}} - {{ $item->name }}</x-table.table-td>
                                        <x-table.table-td class="text-right">{{ number_format($item->balance, 2) }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Total Beban Pokok Penjualan</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['total_beban_pokok'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Laba Kotor</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['laba_kotor'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr>
                                    <x-table.table-td class="font-bold text-xl">Biaya Operasional</x-table.table-td>
                                </x-table.table-tr>
                                @foreach ($data['biaya_operasional'] as $item)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $item->code}} - {{ $item->name }}</x-table.table-td>
                                        <x-table.table-td class="text-right">{{ number_format($item->balance, 2) }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Total Biaya Operasional</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['total_biaya_operasional'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Laba Operasional</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['laba_operasional'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr>
                                    <x-table.table-td class="font-bold text-xl">Pendapatan Lainnya</x-table.table-td>
                                </x-table.table-tr>
                                @foreach ($data['pendapatan_lainnya'] as $item)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $item->code}} - {{ $item->name }}</x-table.table-td>
                                        <x-table.table-td class="text-right">{{ number_format($item->balance , 2) }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Total Pendapatan Lainnya</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['total_pendapatan_lainnya'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr>
                                    <x-table.table-td class="font-bold text-xl">Beban Lainnya</x-table.table-td>
                                </x-table.table-tr>
                                @foreach ($data['beban_lainnya'] as $item)
                                    <x-table.table-tr>
                                        <x-table.table-td>{{ $item->code}} - {{ $item->name }}</x-table.table-td>
                                        <x-table.table-td class="text-right">{{ number_format($item->balance, 2) }}</x-table.table-td>
                                    </x-table.table-tr>
                                @endforeach
                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Total Beban Lainnya</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['total_beban_lainnya'], 2) }}</x-table.table-td>
                                </x-table.table-tr>

                                <x-table.table-tr class="font-bold text-xl">
                                    <x-table.table-td>Laba Bersih</x-table.table-td>
                                    <x-table.table-td class="text-right">{{ number_format($data['laba_bersih'], 2) }}</x-table.table-td>
                                </x-table.table-tr>
                            </x-table.table-tbody>
                        </x-table.table-table>
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
