@php
    $router = 'contacts';

    $trigger = $trigger ?? 'show_modal_js';

    $space_role = session('space_role') ?? null;
@endphp


<x-crud.modal.modal-js title="Details" trigger="{{ $trigger }}">
    <div id='datashow_{{ $trigger }}'>
    </div>


    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen_{{ $trigger }} = false">Cancel</x-secondary-button>

        @if($space_role == 'admin' || $space_role == 'owner')
            <a id="modal_edit_link" target="_blank">
                <x-primary-button type="button">Edit Journal</x-primary-button>
            </a>
        @endif
    </div>
</x-crud.modal.modal-js>

