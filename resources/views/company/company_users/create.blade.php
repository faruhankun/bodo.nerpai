<x-modal-create trigger="Invite User" title="Invite User">
    <form action="{{ route('company_users.store') }}" method="POST" class="">
        @csrf

        <!-- User Name -->
        <!-- <div class="form-group">
            <x-input-label for="name">User Name</x-input-label>
            <x-text-input type="text" id="name" name="name" class="w-full" required placeholder="Masukkan nama gudang" />
        </div> -->

        <div class="form-group mb-4">
            <x-input-label for="user_id" class="block text-sm font-medium text-gray-700">Select User</x-input-label>
            <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded">
                @foreach ($users_to_invite as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-4">
            <x-primary-button type="submit">Invite User</x-primary-button>
            <button type="button" @click="isOpen = false" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
        </div>
    </form>
</x-modal-create>
