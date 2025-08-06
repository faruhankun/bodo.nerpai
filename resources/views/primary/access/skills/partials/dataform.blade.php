


@if($form['mode'] == 'create')
<!-- Select Item -->
<x-div.box-input for="guard_name" title="Item">
    <select name="guard_name" id="{{ $form['mode'] ?? '' }}_guard_name" class="w-full px-4 py-2 border rounded">
        <option value="">-- Select Guard --</option>
        <option value="web">web</option>
        <option value="api">api</option>
        <option value="space">space</option>
    </select>
</x-div.box-input>
@endif

<div class="form-group mb-4">
    <x-input-label for="name">Name</x-input-label>
    <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Account Name" required></x-text-input>
</div>

                    <!-- <div class="form-group mb-4">
                        <x-input-label for="notes">Notes</x-input-label>
                        <x-input-textarea name="notes" id="{{ $form['mode'] ?? '' }}_notes" class="w-full" placeholder="Optional notes"></x-input-textarea>
                    </div> -->

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 mt-4">
                        <x-primary-button type="submit">{{ $form['id'] ?? 'Save' }}</x-primary-button>
                        <button type="button" @click="isOpen = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
                    </div>