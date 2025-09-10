<input type="hidden" name="player_id" class="player_id" value="{{ $player_id ?? '' }}">

<div class="form-group mb-4">
    <x-input-label for="name">Name</x-input-label>
    <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Name" required></x-text-input>
</div>


<div class="form-group mb-4">
    <x-input-label for="code">Code</x-input-label>
    <x-text-input name="code" id="{{ $form['mode'] ?? '' }}_code" class="w-full" placeholder="Code (kosong gpp)"></x-text-input>
</div>


<div class="form-group mb-4">
    <x-input-label for="email">Email</x-input-label>
    <x-text-input name="email" id="{{ $form['mode'] ?? '' }}_email" class="w-full" placeholder="Email"></x-text-input>
</div>


<div class="form-group mb-4">
    <x-input-label for="phone_number">Phone Number</x-input-label>
    <x-text-input name="phone_number" id="{{ $form['mode'] ?? '' }}_phone_number" class="w-full" placeholder="Phone Number"></x-text-input>
</div>


@if($form['mode'] == 'edit' && isset($id_address))
                    <x-input.input-address id="address"></x-input.input-address>
@endif

                    <div class="form-group mb-4">
                        <x-input-label for="status">Status</x-input-label>
                        <x-text-input name="status" id="{{ $form['mode'] ?? '' }}_status" class="w-full" placeholder="Status"></x-text-input>
                    </div>


@if($form['mode'] == 'edit')
                    <x-input.input-marketplace id="edit"></x-input.input-marketplace>
@endif                    


                    <div class="grid grid-cols-3 sm:grid-cols-3 gap-6">
                        <div class="form-group mb-4">
                            <x-input-label for="tags">Tags</x-input-label>
                            <x-input-textarea name="tags" id="{{ $form['mode'] ?? '' }}_tags" class="w-full" placeholder="Optional Tags"></x-input-textarea>
                        </div>

                        <div class="form-group mb-4">
                            <x-input-label for="links">Links</x-input-label>
                            <x-input-textarea name="links" id="{{ $form['mode'] ?? '' }}_links" class="w-full" placeholder="Optional Links"></x-input-textarea>
                        </div>

                        <div class="form-group mb-4">
                            <x-input-label for="notes">Notes</x-input-label>
                            <x-input-textarea name="notes" id="{{ $form['mode'] ?? '' }}_notes" class="w-full" placeholder="Optional notes"></x-input-textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 mt-4">
                        <x-primary-button type="submit">{{ $form['id'] ?? 'Save' }}</x-primary-button>
                        <button type="button" @click="isOpen = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
                    </div>