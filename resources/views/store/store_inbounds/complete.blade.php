<x-company-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                <h1 class="text-2xl font-bold mb-6">Complete Inbound Request</h1>

                    <form action="{{ route('inbound_requests.storeCompletion', $inboundRequest->id) }}" method="POST">
                        @csrf
                        @foreach ($inboundRequest->purchase->products as $product)
                            @if(isset($inboundRequest->requested_quantities[$product->id]) && $inboundRequest->requested_quantities[$product->id] > 0)
                                <h4 class="text-lg font-bold my-3">{{ $product->name }}</h4>
                                <div class="p-2 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                                    <p class="text-sm text-gray-500 dark:text-gray-300">Requested Quantity</p>
                                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $inboundRequest->requested_quantities[$product->id] }}
                                    </p>
                                </div>

                                <div class="p-2 bg-gray-50 border border-gray-200 rounded-lg shadow-md dark:bg-gray-700 dark:border-gray-600">
                                    <p class="text-sm text-gray-500 dark:text-gray-300">Received Quantity</p>
                                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $inboundRequest->received_quantities[$product->id] ?? 0 }}
                                    </p>
                                </div>
                                
                                <!-- Room Selection -->
                                <div>
                                    <x-input-label>Room</x-input-label>
                                    <select name="locations[{{ $product->id }}][room]" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white room-select"
                                        required>
                                        <option value="">-- Select Existing Room --</option>
                                        @foreach ($inboundRequest->warehouse->locations->unique('room') as $location)
                                            <option value="{{ $location->room }}">{{ $location->room }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Rack Selection -->
                                <div>
                                    <label>Rack</label>
                                    <select name="locations[{{ $product->id }}][rack]" class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white rack-select"
                                        required>
                                        <option value="">-- Select Existing Rack --</option>
                                        @foreach ($inboundRequest->warehouse->locations as $location)
                                            <option data-room="{{ $location->room }}" value="{{ $location->rack }}">
                                                {{ $location->rack }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>
                            @endif
                        @endforeach

                        <x-primary-button>Complete Inbound Request</x-primary-button>
                        <a href="{{ route('inbound_request.show') }}"
                        class="border rounded border-gray-400 dark:border-gray-700 p-2 ml-3 text-sm hover:underline text-gray-700 dark:text-gray-400">Cancel</a>
                    </form>

                    <!-- JavaScript for Dynamic Rack Filtering -->
                    <script>
                        document.querySelectorAll('.room-select').forEach(roomSelect => {
                            roomSelect.addEventListener('change', function () {
                                const selectedRoom = this.value;
                                const rackSelect = this.closest('div').nextElementSibling.querySelector('.rack-select');

                                Array.from(rackSelect.options).forEach(option => {
                                    option.style.display = option.dataset.room === selectedRoom ? 'block' : 'none';
                                });

                                rackSelect.selectedIndex = 0;
                            });
                        });
                    </script>
                </x-company-layout>