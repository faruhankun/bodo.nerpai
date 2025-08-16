
<input type="hidden" name="space_id" class="w-full" value="{{ $space_id }}"></input>


@if($form['mode'] == 'create')

<!-- Select User -->
<x-div.box-input for="user_id" title="User">
    <x-input-label for="user_id">User</x-input-label>
    <select name="user_id" id="{{ $form['mode'] ?? '' }}_user_id" class="w-full px-4 py-2 border rounded">
        <option value="">-- Select User --</option>
    </select>
</x-div.box-input>
@endif


@if($form['mode'] == 'edit')
<div class="form-group mb-4">
    <x-input-label for="name">Name</x-input-label>
    <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Name" required readonly></x-text-input>
</div>



<!-- Select Roles -->
<x-div.box-input for="role_id" title="Roles">
    <label for="role_id">Roles</label>
    <select name="role_id" id="{{ $form['mode'] ?? '' }}_role_id" class="w-full px-4 py-2 border rounded">
        <option value="">-- Select Roles --</option>
    </select>
</x-div.box-input>
@endif



<div class="form-group mb-4">
    <x-input-label for="name">Space Role (umum)</x-input-label>
    <x-input-select name="type" id="{{ $form['mode'] ?? '' }}_type" class="form-control w-full" required>    
        <option value="owner">Owner (full access)</option>
        <option value="admin">Admin (lihat, edit, setting, delete)</option>
        <option value="member" selected>Member (lihat)</option>
        <option value="guest">Guest (lihat data umum)</option>
    </x-input-select>
</div>


<!-- <div class="form-group mb-4">
    <x-input-label for="status">Status</x-input-label>
    <x-input-select name="status" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_status" required>
        <option value="active">active</option>
        <option value="inactive">inactive</option>
    </x-input-select>
</div> -->



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