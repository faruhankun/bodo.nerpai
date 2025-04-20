                    <input type="hidden" name="space_id" class="space_id" value="{{ $space_id ?? '' }}">

@if($form['mode'] == 'create')
    <x-input.input-select2 input_id="{{ $form['mode'] ?? '' }}_player_id" 
                       name="player_id"
                       label="Select Player"
                       placeholder="Select Player"
                       option_value="{{ $player_id ?? '' }}"
                       option_text="{{ $player_name ?? 'Select Player' }}">
    </x-input.input-select2>
@endif
                    
                    <div class="form-group mb-4">
                        <x-input-label for="name">Role</x-input-label>
                        <x-input-select name="type" id="{{ $form['mode'] ?? '' }}_type" class="form-control w-full" required>    
                            <option value="owner">Owner</option>
                            <option value="admin">Admin</option>
                            <option value="member" selected>Member</option>
                            <option value="guest">Guest</option>
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