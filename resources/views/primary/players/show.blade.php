@php
    $size_display = ($data->model2?->size_type ?? '?') . ' : ' . ($data->model2?->size?->number ?? $data->model2?->size?->code ?? '?');
@endphp


<x-crud.modal-show title="Player Details" trigger="View">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-div-box-show title="Code">{{ $data->model2?->code }}</x-div-box-show>
        <x-div-box-show title="Size">{{ $size_display }}</x-div-box-show>
        <!-- <x-div-box-show title="Type">{{ ($data->model2?->type_type ?? '?') . ' : ' . ($data->model2?->type?->number ?? '?') }}</x-div-box-show> -->
        <x-div-box-show title="Size-Name">{{ $data->model2?->size?->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Address">{{ $data->model2?->size?->address ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Status">{{ $data->model2?->status }}</x-div-box-show>
        <x-div-box-show title="Notes">
            {{ $data->model2?->notes ?? 'N/A' }}
        </x-div-box-show>
    </div>

    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

    <!-- Action Section -->
    <!-- <h3 class="text-lg font-bold my-3">Actions</h3>
    <div class="flex gap-3">
        <form method="POST" action="{{ route('players.switch', $data->id) }}">
            @csrf

            <x-primary-button :href="route('players.switch', $data->id)" onclick="event.preventDefault();
                this.closest('form').submit();">
                {{ __('Enter Player =>') }}
            </x-primary-button>
        </form>
    </div> -->

    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <!-- <a href="{{ route('players.edit', $data->id) }}">
            <x-primary-button type="button">Edit Player</x-primary-button>
        </a> -->
    </div>
</x-crud.modal-show>

