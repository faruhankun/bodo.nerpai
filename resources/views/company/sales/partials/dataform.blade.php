<x-input.input-select2 input_id="{{ $form['mode'] ?? '' }}_customer_id" 
                        name="customer_id"
                        label="Customer">
</x-input.input-select2>

<div class="mb-4">
    <x-input-label for="date">Sale Date</x-input-label>
    <x-input-input type="date" name="date" value="{{ $sale->date ?? date('Y-m-d') }}"></x-input-input>
</div>

<div class="mb-4">
    <label for="warehouse_id">Select Warehouse</label>
    <x-input-select name="warehouse_id" class="form-control w-full" required >
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" {{ ($sale->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>
                {{ $warehouse->name }}
            </option>
        @endforeach
    </x-input-select>
</div>

@if($form['mode'] == 'edit')

@endif



                            