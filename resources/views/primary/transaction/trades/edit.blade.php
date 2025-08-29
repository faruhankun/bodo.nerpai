@php
    $layout = session('layout');

    $space_id = session('space_id') ?? null;
    if(is_null($space_id)){
        abort(403);
    }

    $player = session('player_id') ? \App\Models\Primary\Player::findOrFail(session('player_id')) : Auth::user()->player;

    $spaces_dest = $player?->spaces->where('id', '!=', $space_id) ?? [];
    $output_journal = $journal?->output;

    $spaces_origin = $player?->spaces->where('id', '!=', $space_id) ?? [];
    $input_journal = $journal?->input;
@endphp


<x-dynamic-component :component="'layouts.' . $layout">
    <div class="py-12">
        <div class=" sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    <h3 class="text-2xl dark:text-white font-bold">Edit Journal: {{ $journal->number }}</h3>
                    <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                    <form action="{{ route('trades.update', $journal->id) }}" 
                        method="POST" 
                        enctype="multipart/form-data"
                        onsubmit="return validateForm()">
                        @csrf
                        @method('PUT')



                        @include('primary.transaction.trades.partials.dataform', ['form' => ['id' => 'Edit Journal', 'mode' => 'edit'], 'data' => $journal])



                        <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>

                        <div class="container">
                            <!-- Journal Details Table -->
                            <x-table.table-table id="journalDetailTable">
                                <x-table.table-thead>
                                    <tr>
                                        <x-table.table-th>No</x-table.table-th>
                                        <x-table.table-th>Item</x-table.table-th>
                                        <x-table.table-th>Type</x-table.table-th>
                                        <x-table.table-th>Qty</x-table.table-th>
                                        <x-table.table-th>Price</x-table.table-th>
                                        <x-table.table-th>Discount</x-table.table-th>
                                        <x-table.table-th>Notes</x-table.table-th>
                                        <x-table.table-th>Action</x-table.table-th>
                                    </tr>
                                </x-table.table-thead>
                                <x-table.table-tbody id="journal-detail-list">
                                </x-table.table-tbody>
                            </x-table.table-table>

                            <div class="mb-4">
                                <x-button2 type="button" id="add-detail" class="mr-3 m-4">Add Journal
                                    Detail</x-button2>
                            </div>

                                        
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>



                            <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 w-full mb-4">
                                <div class="form-group">
                                    <x-div.box-show title="File Terkait">
                                        <x-input-label for="files">Upload File Terkait (max 2 MB)</x-input-label>
                                        <input type="file" name="files[]" class="form-control" id="files" multiple >

                                        <!-- List File Lama -->
                                        <ul id="files-list" class="mt-2">
                                            @if(!empty($journal->files))
                                                @foreach($journal->files as $index => $file)
                                                    <li data-old="{{ $index }}" class="flex items-center gap-2">
                                                        <a href="{{ asset($file['path']) }}" target="_blank">{{ $file['name'] }}</a>
                                                        <button type="button" class="remove-old-file text-red-500">Hapus</button>
                                                        <input type="hidden" name="old_files[{{ $index }}][name]" value="{{ $file['name'] ?? '' }}">
                                                        <input type="hidden" name="old_files[{{ $index }}][path]" value="{{ $file['path'] ?? '' }}">
                                                        <input type="hidden" name="old_files[{{ $index }}][size]" value="{{ $file['size'] ?? 0 }}">
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </x-div.box-show>

                                <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    // Hapus file lama
                                    document.addEventListener("click", function(e) {
                                        if (e.target.classList.contains("remove-old-file")) {
                                            e.target.closest("li").remove();
                                        }
                                    });

                                    // Hapus file baru
                                    document.getElementById("files").addEventListener("change", function(e) {
                                        const list = document.getElementById("files-list");
                                        list.querySelectorAll(".new-file").forEach(el => el.remove());

                                        Array.from(e.target.files).forEach((file, i) => {
                                            let li = document.createElement("li");
                                            li.classList.add("new-file","flex","items-center","gap-2");
                                            li.textContent = file.name;

                                            let btn = document.createElement("button");
                                            btn.type = "button";
                                            btn.className = "remove-new-file text-red-500";
                                            btn.textContent = "Hapus";

                                            btn.addEventListener("click", () => {
                                                let dt = new DataTransfer();
                                                Array.from(e.target.files).forEach((f, idx) => {
                                                    if (idx !== i) dt.items.add(f);
                                                });
                                                e.target.files = dt.files;
                                                li.remove();
                                            });

                                            li.appendChild(btn);
                                            list.appendChild(li);
                                        });
                                    });
                                });
                                </script>
                                </div>
                            </div>
                            <div class="my-6 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                        
                        
                        
                        </div>

                        <div class="m-4 flex justify-end space-x-4">
                            <a href="{{ route('trades.index') }}">
                                <x-secondary-button type="button">Cancel</x-secondary-button>
                            </a>
                            <x-primary-button>Update Journal</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Rows -->
    <script>
        let detailIndex = 0;

        function formatNumberNoTrailingZeros(num, precision = 2) {
            return new Intl.NumberFormat('en-US', {
                useGrouping: false,
                maximumFractionDigits: precision
            }).format(num);
        }

        function renderDetailRow(detail = {}) {
            const rowIndex = detailIndex++;
            const selectedId = detail.detail_id || '';
            const selectedModel = detail.model_type || '';
            const quantity = detail.quantity || 1;
            const price = detail.price || 0;
            const discount = detail.discount || 0;
            const notes = detail.notes || '';

            const sku = detail.sku || '';
            const name = detail.name || '';
            const weight = detail.weight || 0;

            const model_types = @json($model_types);
            const modelTypeOptions = model_types.map(model_type => {
                const selected = model_type.id === selectedModel ? 'selected' : '';
                return `<option value="${model_type.id}" ${selected}>${model_type.name}</option>`;
            }).join('');

            return `
                <tr class="detail-row">
                    <td class="mb-2">${rowIndex + 1}</td>
                    <td>
                        <select name="details[${rowIndex}][detail_id]" class="inventory-select w-20" required>
                            <option value="">Select Inventory</option>
                        </select>
                    </td>
                    <td>
                        <select name="details[${rowIndex}][model_type]" class="model_type-select type-select my-3" required>
                            <option value="">Select Type</option>
                            ${modelTypeOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="details[${rowIndex}][quantity]" class="quantity-input w-20" value="${formatNumberNoTrailingZeros(quantity)}">
                    </td>
                    <td>
                        <input type="text" size="5" name="details[${rowIndex}][price]" class="price-input w-25" value="${formatNumberNoTrailingZeros(price)}" default="0" min="0">
                    </td>
                    <td>
                        <input type="text" size="3" name="details[${rowIndex}][discount]" class="discount-input w-25" value="${formatNumberNoTrailingZeros(discount)}" default="0" min="0">
                    </td>
                    <td>
                        <textarea name="details[${rowIndex}][notes]" class="notes-input">${notes}</textarea>
                        <input type="hidden" name="details[${rowIndex}][sku]" class="sku-input" value="${sku}">
                        <input type="hidden" name="details[${rowIndex}][name]" class="name-input" value="${name}">
                        <input type="hidden" name="details[${rowIndex}][weight]" class="weight-input" value="${weight}">
                    </td>
                    <td>
                        <button type="button" class="bg-red-500 text-sm text-white px-4 py-1 rounded-md hover:bg-red-700 remove-detail">Remove</button>
                    </td>
                </tr>
            `;
        }

        function initInventorySelect($element, selectedData = null) {
            $element.select2({
                placeholder: 'Search & Select Item',
                width: '100%',
                height: '100%',
                padding: '20px',
                ajax: {
                    url: '/items/search',
                    dataType: 'json',
                    paginate: true,
                    data: function(params) {
                        return {
                            q: params.term,
                            space_id: '{{ $space_id }}',
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.text, // Ubah sesuai nama field yang kamu punya
                                price: item.price,
                                sku: item.sku,
                                name: item.name,
                                weight: item.weight,
                            }))
                        };
                    },
                    cache: true
                }
            });

            if (selectedData) {
                // Delay agar select2 benar-benar ter-attach
                const option = new Option(selectedData.text, selectedData.id, true, true);
                $element.append(option).trigger('change');
            }

            // Event setelah pilih inventory
            $element.on('select2:select', function(e) {
                const selected = e.params.data;
                const $row = $(this).closest('tr');
                
                $row.find('.price-input').val(selected.price || 0);
                $row.find('.sku-input').val(selected.sku || '');
                $row.find('.name-input').val(selected.name || '');
                $row.find('.weight-input').val(selected.weight || 0);

                console.log(selected);
            });
        }

        function appendDetailRow(detail) {
            const $row = $(renderDetailRow(detail));
            $("#journal-detail-list").append($row);

            const $select = $row.find('.inventory-select');
            const selectedData = detail.detail_id && detail.detail ? {
                id: detail.detail_id,
                text: `${detail.detail.sku} - ${detail.detail.name} : ${detail.detail.notes}`,
            } : null;

            setTimeout(() => {
                initInventorySelect($select, selectedData);
            }, 0);
        }
    </script>

    <script>
        $(document).ready(function() { 
            // Add Journal Detail row
            $("#add-detail").click(function() {
                let newRow = renderDetailRow();
                const $row = $(newRow);

                $("#journal-detail-list").append($row);

                initInventorySelect($row.find('.inventory-select'));
            });

            // Remove Journal Detail row
            $(document).on("click", ".remove-detail", function() {
                $(this).closest("tr").remove();
            });
        });

        function validateForm() {
            // Your validation logic here
            let isValid = true;

            return isValid; // Return true if the form is valid, false otherwise
        }
    </script>

</x-dynamic-component>


<!-- files upload list -->



<script>
    $(document).ready(function() {
        $('#edit_receiver_id').select2({
            placeholder: 'Search & Select Kontak',
            width: '100%',
            ajax: {
                url: '/players/data',
                dataType: 'json',
                paginate: true,
                data: function(params) {
                    return {
                        q: params.term,
                        return_type: 'json',
                        space: 'true',
                        space_id: '{{ $space_id }}',
                        page: params.page || 1,
                        model_type_select: 'all',
                    };
                },
                processResults: function(result) {
                    console.log(result);
                    return {
                        results: result.map(item => ({
                            id: item.id,
                            text: (item.code || item.id) + ' : ' + item.name + ' (' + item.email + ' : ' + item.phone_number + ')',
                        }))
                    }
                },
                cache: true
            }
        });

        $('#edit_receiver_id').on('select2:select', function(e) {
            const selected = e.params.data;
            console.log(selected);
            
            $('#edit_receiver_address').html(selected.text);
        });
    });
</script>


<script>
        $(document).ready(function() {
            // Journal
            let journal = {!! json_encode($journal) !!};
            let journal_details = {!! json_encode($journal->details) !!};

            let sentTime = '{{ $journal->sent_time->format('Y-m-d') }}';
            $("#edit_sent_time").val(sentTime);
            $("#edit_sender_notes").val(journal.sender_notes);

            $("#edit_handler_notes").val(journal.handler_notes);


            if(journal.receiver){
                const option = new Option(journal.receiver.name, journal.receiver.id, true, true);
                $("#edit_receiver_id").append(option).trigger('change');
                $('#edit_receiver_address').html(journal.receiver.email + ': ' + journal.receiver.phone_number + ' <br> ' + journal.receiver.address);
                $("#edit_receiver_notes").val(journal.receiver_notes);
            }

            // Details
            let detailIndex = {{ $journal->details->count() }};
            journal_details.forEach(appendDetailRow);
            
            // setTimeout(() => {
            //     $('.inventory-select').each(function() {
            //         initInventorySelect($(this));
            //     });
            // }, 0);
        });
    </script>
