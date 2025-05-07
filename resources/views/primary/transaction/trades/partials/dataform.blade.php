
<div class="grid grid-cols-3 sm:grid-cols-3 gap-6 mb-6">
    <x-input-input type="hidden" name="sender_id" id="{{ $form['mode'] ?? '' }}_sender_id_hidden"></x-input-input>
    <x-div.box-input for="sender_id" label="Sender">
        <x-input-select name="sender_id" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_sender_id" required>
            <option value="">-- Select Player --</option>
            @foreach($players as $sender)
                <option value="{{ $sender->id }}">{{ $sender->code }} - {{ $sender->name }}</option>
            @endforeach
        </x-input-select>
    </x-div.box-input>

    <x-div.box-input for="sent_date" label="Sent Date">
        <input type="date" name="sent_date" id="{{ $form['mode'] ?? '' }}_sent_date" class="w-full" value="{{ old('sent_date') }}">
    </x-div.box-input>

    <x-div.box-input for="sender_notes" label="Sender Notes">
        <x-input-textarea name="sender_notes" id="{{ $form['mode'] ?? '' }}_sender_notes" class="w-full" placeholder="Sender notes"></x-input-textarea>
    </x-div.box-input>

        <x-div.box-input for="input_id" label="Space Sender">
            <x-input-select name="input_id" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_input_id" >
                <option value="">-- Select Space --</option>
                @foreach($spaces as $space)
                    <option value="{{ $space->id }}">{{ $space->code }} - {{ $space->name }}</option>
                @endforeach
            </x-input-select>
        </x-div.box-input>
        <input type="hidden" name="input_id" id="{{ $form['mode'] ?? '' }}_input_id_hidden">
</div>

            
<div class="mb-3 mt-3 flex-grow border-t border-gray-300 dark:border-gray-700"></div>
<div class="grid grid-cols-3 sm:grid-cols-3 gap-6">  
    <x-input-input type="hidden" name="receiver_id" id="{{ $form['mode'] ?? '' }}_receiver_id_hidden"></x-input-input>
    <x-div.box-input for="receiver_id" label="Receiver">
        <x-input-select name="receiver_id" class="edit_readonly mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_receiver_id" required>
            <option value="">-- Select Player --</option>
            @foreach($players as $receiver)
                <option value="{{ $receiver->id }}">{{ $receiver->code }} - {{ $receiver->name }}</option>
            @endforeach
        </x-input-select>
    </x-div.box-input>

    <x-div.box-input for="received_date" label="Received Date">
        <input type="date" name="received_date" id="{{ $form['mode'] ?? '' }}_received_date" class="w-full">
    </x-div.box-input>

    <x-div.box-input for="receiver_notes" label="Receiver Notes">
        <x-input-textarea name="receiver_notes" id="{{ $form['mode'] ?? '' }}_receiver_notes" class="w-full" placeholder="Receiver notes"></x-input-textarea>
    </x-div.box-input>

        <x-div.box-input for="output_id" label="Space Receiver">
            <x-input-select name="output_id" class="mt-1 block w-full" id="{{ $form['mode'] ?? '' }}_output_id" >
                <option value="">-- Select Space --</option>
                @foreach($spaces as $space)
                    <option value="{{ $space->id }}">{{ $space->code }} - {{ $space->name }}</option>
                @endforeach
            </x-input-select>
        </x-div.box-input>
        <input type="hidden" name="output_id" id="{{ $form['mode'] ?? '' }}_output_id_hidden">
</div>