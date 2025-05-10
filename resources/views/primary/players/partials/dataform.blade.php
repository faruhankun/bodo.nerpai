<input type="hidden" name="player_id" class="player_id" value="{{ $player_id ?? '' }}">

@if($form['mode'] == 'create')
    <x-div.box-input for="new_player_id" label="New Player ID">
        <select name="new_player_id" id="create_new_player_id" class="form-control w-full select2" required>    
            <option value="">-- Select Player --</option>
        </select>
    </x-div.box-input>
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