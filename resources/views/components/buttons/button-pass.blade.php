@props(['route', 'confirm_text'])

@php
    $confirm_text = $confirm_text ?? 'Are you sure you want to pass this item? 
                It will be moved to the next stage.';
@endphp

<div id="pass-modal-wrapper">
    <div onclick="showPassModal('{{ $route }}')" {{ $attributes->merge(['class' => 'inline-block p-2 bg-green-600 rounded-lg mr-3 text-white cursor-pointer']) }}>
        <button type="button">
            <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                    d="M1 8h11m0 0L8 4m4 4-4 4m4-4H3" />
            </svg>
        </button>

        {{ $slot }}
    </div>

    <!-- Modal -->
    <div id="pass-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-120">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Confirm Action</h2>
            <p class="text-lg text-gray-600 mb-6">
                {{ $confirm_text }}
            </p>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg"
                    onclick="hidePassModal()">Cancel</button>
                <form id="pass-form" method="POST">
                    @csrf
                    <input type="hidden" name="request_source" value="web">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Pass</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showPassModal(route) {
        document.getElementById('pass-modal').classList.remove('hidden');
        document.getElementById('pass-form').setAttribute('action', route);
    }

    function hidePassModal() {
        document.getElementById('pass-modal').classList.add('hidden');
    }
</script>
