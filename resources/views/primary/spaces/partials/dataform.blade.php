                    <div class="form-group mb-4">
                        <x-input-label for="code">Code</x-input-label>
                        <x-text-input name="code" id="{{ $form['mode'] ?? '' }}_code" class="w-full" placeholder="Code without spaces" required></x-text-input>
                    </div>
                    
                    <div class="form-group mb-4">
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Name" required></x-text-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="type_type">Space Type</x-input-label>
                        <x-input-select id="{{ $form['mode'] ?? '' }}_type_type" name="type_type" class="w-full" required>
                            <option value="SPACE">Space</option>
                        </x-input-select>
                    </div>

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