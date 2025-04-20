<div class="form-group mb-4">
    <x-input-label for="code">Code</x-input-label>
    <x-text-input name="code" id="{{ $form['mode'] ?? '' }}_code" class="w-full" placeholder="Code" required></x-text-input>
</div>

                    <div class="form-group mb-4">
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Name" required></x-text-input>
                    </div>


                    @if($form['mode'] == 'edit')
                    <div class="form-group mb-4">
                        <x-input-label for="address">Address</x-input-label>
                        <x-text-input name="address" id="{{ $form['mode'] ?? '' }}_address" class="w-full" placeholder="Address"></x-text-input>
                    </div>
                    @endif


                    <div class="form-group mb-4">
                        <x-input-label for="status">Status</x-input-label>
                        <x-input-select name="status" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_status" required>
                            <option value="active">active</option>
                            <option value="inactive">inactive</option>
                        </x-input-select>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="notes">Notes</x-input-label>
                        <x-input-textarea name="notes" id="{{ $form['mode'] ?? '' }}_notes" class="w-full" placeholder="Optional notes"></x-input-textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 mt-4">
                        <x-primary-button type="submit">{{ $form['id'] ?? 'Save' }}</x-primary-button>
                        <button type="button" @click="isOpen = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
                    </div>