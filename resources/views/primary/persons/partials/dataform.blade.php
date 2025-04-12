<div class="form-group mb-4">
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Name" required></x-text-input>
                    </div>

                    @if($form['mode'] == 'edit')
                        <div class="form-group mb-4">
                            <x-input-label for="full_name">Full Name</x-input-label>
                            <x-text-input name="full_name" id="{{ $form['mode'] ?? '' }}_full_name" class="w-full" placeholder="Full Name" required></x-text-input>
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <x-input-label for="birth_date">Birth Date</x-input-label>
                        <x-date-input id="{{ $form['mode'] ?? '' }}_birth_date" name="birth_date" type="date" class="mt-1 block w-full" required></x-date-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="email">Email</x-input-label>
                        <x-text-input name="email" id="{{ $form['mode'] ?? '' }}_email" class="w-full" placeholder="Email" type="email" required></x-text-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="phone_number">Phone Number</x-input-label>
                        <x-text-input name="phone_number" id="{{ $form['mode'] ?? '' }}_phone_number" class="w-full" placeholder="Phone Number" type="text" required></x-text-input>
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
                        <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel</button>
                    </div>