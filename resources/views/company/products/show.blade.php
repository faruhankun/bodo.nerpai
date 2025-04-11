<x-crud.modal-show title="Product Details" trigger="View">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-div-box-show title="Product Name">{{ $data->name }}</x-div-box-show>
        <x-div-box-show title="SKU">{{ $data->sku }}</x-div-box-show>
        <x-div-box-show title="Price">
            Rp. {{ number_format($data->price, 0, ',', '.') }}
        </x-div-box-show>
        <x-div-box-show title="Weight (gram)">{{ $data->weight }}</x-div-box-show>
        <x-div-box-show title="Status">{{ $data->status }}</x-div-box-show>
        <x-div-box-show title="Notes">
            {{ $data->notes ?? 'N/A' }}
        </x-div-box-show>
    </div>

    <div class="flex gap-3 justify-end mt-8">
        <x-secondary-button type="button" @click="isOpen = false">Cancel</x-secondary-button>
        <a href="{{ route('products.edit', $data->id) }}">
            <x-primary-button type="button">Edit Product</x-primary-button>
        </a>
    </div>
</x-crud.modal-show>

