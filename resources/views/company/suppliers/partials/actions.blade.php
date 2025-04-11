<div class="flex gap-3 justify-end">
    <x-button-show :route="route('suppliers.show', $supplier->id)" />
    <!-- <x-button-edit :route="route('suppliers.edit', $supplier->id)" /> -->
    <x-button-delete :route="route('suppliers.destroy', $supplier->id)" />
</div>