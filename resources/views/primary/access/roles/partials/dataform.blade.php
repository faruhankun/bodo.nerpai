

<div class="form-group mb-4">
    <x-input-label for="name">Name</x-input-label>
    <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Name" required></x-text-input>
</div>


@if($form['mode'] == 'edit')
    <div class="form-group mb-4" id="edit_permissions">
        <x-input-label for="permissions">Permissions</x-input-label>
        <div class="grid grid-cols-2 gap-2 mt-2">
            @foreach ($permissions as $permission)
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="permissions[]" 
                            id="perm_{{ $permission->id }}" 
                            value="{{ $permission->id }}"
                            class="form-checkbox text-blue-500 rounded">
                    <label for="{{ $permission->id }}">{{ $permission->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
@endif


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