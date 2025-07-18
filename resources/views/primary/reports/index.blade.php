@php
    $layout = session('layout');

    $settings = [
        'supplies' => get_variable('space.setting.supplies') ?? true,

        'accounting' => get_variable('space.setting.accounting') ?? true,
    ];
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl font-bold dark:text-white">Rangkuman Space</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Cek, Evaluasi, Control your summaries</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 mb-6">
                        @if($settings['accounting'])
                            <x-div-box-show title="Accounting" class="text-xl font-bold">
                                <ul>
                                    <li class="mb-3"><a href="{{ route('accountsp.summary') }}">Rangkuman Akun</a></li>
                                    <li class="mb-3"><a href="{{ route('summaries.show', 'cashflow') }}">Cashflow</a></li>
                                </ul>
                            </x-div-box-show>
                        @endif

                        @if($settings['supplies'])
                            <x-div-box-show title="Items" class="text-xl font-bold">
                                <ul>
                                    <li class="mb-3"><a href="{{ route('summaries.show', 'items.summary') }}">Rangkuman Items</a></li>
                                </ul>
                            </x-div-box-show>

                            <x-div-box-show title="Supplies" class="text-xl font-bold">
                                <ul>
                                    <li class="mb-3"><a href="{{ route('supplies.summary') }}">Rangkuman Supplies</a></li>
                                </ul>
                            </x-div-box-show>
                        @endif

                            <x-div-box-show title="Contact" class="text-xl font-bold">
                                <ul>
                                    <li class="mb-3"><a href="{{ route('contacts.summary') }}">Rangkuman Contact</a></li>
                                </ul>
                            </x-div-box-show>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</x-dynamic-component>


<script>
    $(document).ready(function() {
        
    });
</script>