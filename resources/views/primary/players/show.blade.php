<x-crud.modal-show title="Player Details" trigger="View">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-div-box-show title="Code">{{ $data->code }}</x-div-box-show>
        <x-div-box-show title="Size">{{ ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? '?') }}</x-div-box-show>
        <x-div-box-show title="Type">{{ ($data->type_type ?? '?') . ' : ' . ($data->type?->number ?? '?') }}</x-div-box-show>
        <x-div-box-show title="Size-Name">{{ $data->size?->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
        <x-div-box-show title="Notes">
            {{ $data->notes ?? 'N/A' }}
        </x-div-box-show>
    </div>

    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <!-- <a href="{{ route('players.edit', $data->id) }}">
            <x-primary-button type="button">Edit Player</x-primary-button>
        </a> -->
    </div>
</x-crud.modal-show>

