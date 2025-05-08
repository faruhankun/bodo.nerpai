<x-crud.modal-show title="Group Details" trigger="View">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-div-box-show title="Name">{{ $data->name ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Code">{{ $data->code ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Address">{{ $data->address ?? 'N/A' }}</x-div-box-show>
        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
        <x-div-box-show title="Notes">
            {{ $data->notes ?? 'N/A' }}
        </x-div-box-show>
    </div>

    <div class="my-6 flex-grow border-t border-gray-500 dark:border-gray-700"></div>

    <!-- Action Section -->
    <h3 class="text-lg font-bold my-3">Actions</h3>
    <div class="flex gap-3">
        <form method="POST" action="{{ route('players.switch', $data->player->id) }}">
            @csrf

            <x-primary-button :href="route('players.switch', $data->player->id)" onclick="event.preventDefault();
                this.closest('form').submit();">
                {{ __('Enter Group =>') }}
            </x-primary-button>
        </form>
    </div>

    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <!-- <a href="{{ route('players.edit', $data->id) }}">
            <x-primary-button type="button">Edit Group</x-primary-button>
        </a> -->
    </div>
</x-crud.modal-show>

