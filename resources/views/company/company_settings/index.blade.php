@php
    $layout = session('layout');
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class="max-w-7xl my-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white" x-data="{ isOpen: false, item: {} }">
                    <h3 class="text-lg font-bold dark:text-white">Manage Settings</h3>
                    <p class="text-sm dark:text-gray-200 mb-6">Create, edit, and manage your Settings</p>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            @include('company.company_settings.create')
                        </div>
                    </div>

                    <!-- Settings Table -->
                    <x-table.table-table id="search-table">
                        <x-table.table-thead>
                            <tr>
                                <x-table.table-th>ID</x-table.table-th>
                                <x-table.table-th>Module</x-table.table-th>
                                <x-table.table-th>Source</x-table.table-th>
                                <x-table.table-th>Key</x-table.table-th>
                                <x-table.table-th>Value</x-table.table-th>
                                <x-table.table-th>Note</x-table.table-th>
                                <x-table.table-th>Actions</x-table.table-th>
                            </tr>
                        </x-table.table-thead>
                        <x-table.table-tbody>
                            @foreach ($settings as $setting)
                                <x-table.table-tr>
                                    <x-table.table-td>{{ $setting->id }}</x-table.table-td>
                                    <x-table.table-td>{{ $setting->module ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $setting->source_type ?? 'COMP' }} : {{ $setting->source?->name ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $setting->key ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $setting->value ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>{{ $setting->notes ?? 'N/A' }}</x-table.table-td>
                                    <x-table.table-td>
                                        <div class="flex space-x-2">
                                            <x-button2 onclick="editSetting({{ $setting }})" class="btn btn-primary">Edit</x-button2>
                                            <!-- <button type="button" @click="editSetting({{ $setting }})" class="btn btn-primary">Edit</button> -->
                                            <x-button-delete :route="route('company_settings.destroy', $setting->id)" />
                                        </div>
                                    </x-table.table-td>
                                </x-table.table-tr>
                            @endforeach
                        </x-table.table-tbody>
                    </x-table.table-table>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-4">
                        <div class="flex flex-col md:flex-row items-center space-x-3">
                            @include('company.company_settings.edit')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>

<script>
    function editSetting(setting) {
        document.getElementById('edit_id').value = setting.id;
        document.getElementById('edit_module').value = setting.module;
        document.getElementById('edit_key').value = setting.key;
        document.getElementById('edit_value').value = setting.value;
        document.getElementById('edit_source_type').value = setting.source_type;
        document.getElementById('edit_source_id').value = setting.source_id;

        let form = document.getElementById('editSettingForm');
        form.action = `/company_settings/${setting.id}`;

        // Dispatch event ke Alpine.js untuk membuka modal
        window.dispatchEvent(new CustomEvent('edit-setting'));
    }
</script>
