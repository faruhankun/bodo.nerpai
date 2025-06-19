@php
    $size_display = ($data->model2?->size_type ?? '?') . ' : ' . ($data->model2?->size?->number ?? $data->model2?->size?->code ?? '?');

    $router = 'contacts';

    $trigger = $trigger ?? 'show_modal_js';
@endphp


<x-crud.modal.modal-js title="Contact Details" trigger="{{ $trigger }}">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        
    </div>

    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>
    <h3 class="text-lg font-bold my-3">Contact Details</h3>
    <div id='dataform_{{ $trigger }}' class="break-words">
    </div>

    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>



    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen_{{ $trigger }} = false">Cancel</x-secondary-button>
    </div>
</x-crud.modal.modal-js>

