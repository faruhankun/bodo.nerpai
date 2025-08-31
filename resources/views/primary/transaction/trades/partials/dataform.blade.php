<input type="hidden" name="space_id" value="{{ $space_id }}">
<input type="hidden" name="sender_id" id="{{ $form['mode'] ?? '' }}_sender_id" value="{{ $player->id }}">

<div class="grid grid-cols-3 sm:grid-cols-3 gap-6 mb-6">
    @if ($form['mode'] == 'create')
        <div class="form-group">
            <x-input-label for="sender_name">Sender</x-input-label>
            <x-input.input-basic class="w-full" placeholder="Sender" value="{{ $player->name }}" required readonly></x-input.input-basic>
        </div>

        <div class="form-group">
            <x-input-label for="sent_time">Transaction Date</x-input-label>
            <input type="date" name="sent_time"
                class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
            <x-input-label for="sender_notes">Sender Notes</x-input-label>
            <x-input-textarea name="sender_notes" class="form-control" id="{{ $form['mode'] ?? '' }}_sender_notes"></x-input-textarea>
        </div>
    
    
    @elseif($form['mode'] == 'edit')
        <!-- <x-div.box-show title="Contributor">
            Created By: {{ $data->sender?->name ?? '-' }} <br>
            Updated By: {{ $data?->handler?->name ?? '-' }}
        </x-div.box-show> -->
        <x-div.box-input for="parent_id" title="Transaksi Induk" label="Transaksi Induk">
            <select name="parent_id" id="{{ $form['mode'] ?? '' }}_parent_id" class="w-full px-4 py-2 border rounded">
                <option value="">-- Select Trades --</option>
            </select>
            <label id="{{ $form['mode'] ?? '' }}_parent_data" class="text-xs text-gray-500"></label>
        </x-div.box-input>




        <x-div.box-input for="receiver_id" title="Kontak" label="Kontak">
            <select name="receiver_id" id="{{ $form['mode'] ?? '' }}_receiver_id" class="w-full px-4 py-2 border rounded">
                <option value="">-- Select Kontak --</option>
            </select>
            <label id="{{ $form['mode'] ?? '' }}_receiver_address" class="text-xs text-gray-500"></label>
        </x-div.box-input>



        <div class="form-group">
            <x-input-label for="receiver_notes">Receiver Notes</x-input-label>
            <x-input-textarea name="receiver_notes" class="form-control" id="{{ $form['mode'] ?? '' }}_receiver_notes"></x-input-textarea>
        </div>
    @endif



    @if ($form['mode'] == 'edit')
    <input type="hidden" name="handler_id" id="{{ $form['mode'] ?? '' }}_handler_id" value="{{ $player->id }}">
        <!-- <x-div.box-input for="handler_name" title="Handler" label="Handler">
            <x-input.input-basic class="w-full" placeholder="Handler" value="{{ $player->name }}" required readonly></x-input.input-basic>
        </x-div.box-input> -->

        <div class="form-group">
            <x-input-label for="sent_time">Transaction Date</x-input-label>
            <input type="date" name="sent_time"
                class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                required id="{{ $form['mode'] ?? '' }}_sent_time">
        </div>



        <x-div.box-input for="status" title="Status" label="Status">
            <x-input-select name="status" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_status" required>
                <option value="">-- Select Status --</option>
                @foreach ($status_types as $key => $value)
                    <option value="{{ $key }}" {{ $data->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </x-input-select>
        </x-div.box-input>



        <div class="form-group">
            <x-input-label for="handler_notes">Handler Notes</x-input-label>
            <x-input-textarea name="handler_notes" class="form-control" id="{{ $form['mode'] ?? '' }}_handler_notes"></x-input-textarea>
        </div>
    @endif
</div>
