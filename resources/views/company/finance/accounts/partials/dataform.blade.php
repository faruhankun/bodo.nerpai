                    <div class="form-group mb-4">
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input name="name" id="{{ $form['mode'] ?? '' }}_name" class="w-full" placeholder="Account Name" required></x-text-input>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="type_id">Account Type</x-input-label>
                        <x-input-select name="type_id" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_type_id"
                            @change="document.getElementById('{{ $form['mode'] ?? '' }}_basecode').value = event.target.selectedOptions[0].dataset.basecode" required>
                            @foreach ($account_types as $account_type)
                                <option id="option-{{ $account_type->id }}-for"
                                    value="{{ $account_type->id }}" data-basecode="{{ $account_type->basecode }}">{{ $account_type->name }}</option>
                            @endforeach
                        </x-input-select>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="code">Code</x-input-label>
                        <div class="flex gap-2">
                            <input
                                class="w-20 flex items-center justify-center bg-gray-50 border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300"
                                id="{{ $form['mode'] ?? '' }}_basecode" name="basecode" readonly></input>
                            <x-text-input class="flex-1" name="code" id="{{ $form['mode'] ?? '' }}_code" class="w-full"
                                placeholder="Suffix" required></x-text-input>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="status">Status</x-input-label>
                        <x-input-select name="status" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </x-input-select>
                    </div>

                    <div class="form-group mb-4">
                        <x-input-label for="parent_id">Parent Account</x-input-label>
                        <x-input-select name="parent_id" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_parent_id">
                            <option value=""><-- Select Parent Account --></option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
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