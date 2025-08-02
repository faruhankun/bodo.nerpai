@php 
    $space_role = session('space_role') ?? null;

    $tx_related = $data->children ?? [];
    if($data->input){
        $tx_related[] = $data->input;
    }
@endphp

<x-crud.modal-show title="Transaction Details: {{ $data->number }}" trigger="View">
    @include('primary.transaction.journal_supplies.partials.datashow')

    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>

        @if($space_role == 'admin' || $space_role == 'owner')
            <a target="_blank" href="{{ route('journal_supplies.edit', $data->id) }}">
                <x-primary-button type="button">Edit Journal</x-primary-button>
            </a>
        @endif
    </div>
</x-crud.modal-show>

