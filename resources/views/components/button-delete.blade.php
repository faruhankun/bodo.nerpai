<div id="delete-modal-wrapper">
    <div {{ $attributes->merge(['class' => 'inline-block p-2 bg-red-600 rounded-lg mr-3']) }}>
        <button type="button" onclick="showDeleteModal('{{ $route }}')">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z">
                </path>
            </svg>
        </button>
    </div>

    <!-- Modal -->
    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-80">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this item? This action cannot be
                undone.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg"
                    onclick="hideDeleteModal()">Cancel</button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="request_source" value="web">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(route) {
        document.getElementById('delete-modal').classList.remove('hidden');
        document.getElementById('delete-form').setAttribute('action', route);
    }

    function hideDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
