<div id="duplicate-modal-wrapper">
    <div onclick="showDuplicateModal('{{ $route }}')" {{ $attributes->merge(['class' => 'inline-block p-2 bg-blue-600 rounded-lg mr-3 text-white cursor-pointer']) }}>
        <button type="button">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 1H4C2.9 1 2 .9 2 2V16H4V4H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"/>
            </svg>
        </button>

        {{ $slot }}
    </div>

    <!-- Modal -->
    <div id="duplicate-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-80">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Confirm Duplication</h2>
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to duplicate this item? 
                A new copy will be created.
            </p>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg"
                    onclick="hideDuplicateModal()">Cancel</button>
                <form id="duplicate-form" method="POST">
                    @csrf
                    <input type="hidden" name="request_source" value="web">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Duplicate</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDuplicateModal(route) {
        document.getElementById('duplicate-modal').classList.remove('hidden');
        document.getElementById('duplicate-form').setAttribute('action', route);
    }

    function hideDuplicateModal() {
        document.getElementById('duplicate-modal').classList.add('hidden');
    }
</script>
