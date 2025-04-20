<x-crud.modal-show title="Person Details" trigger="View">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-div-box-show title="Number">{{ $data->number }}</x-div-box-show>
        <x-div-box-show title="User:Username">{{ $data->player->user?->username ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Name">{{ $data->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Full Name">{{ $data->full_name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Birth Date">{{ $data->birth_date ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Email">{{ $data->email ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Phone Number">{{ $data->phone_number ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Gender">{{ $data->gender ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
        <x-div-box-show title="Notes">
            {{ $data->notes ?? 'N/A' }}
        </x-div-box-show>
    </div>

    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <!-- <a href="{{ route('players.edit', $data->id) }}">
            <x-primary-button type="button">Edit Person</x-primary-button>
        </a> -->
    </div>
</x-crud.modal-show>

