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
        <x-div.box-show title="Contributor">
            Created By: {{ $data->sender?->name ?? '-' }} <br>
            Updated By: {{ $data?->handler?->name ?? '-' }}
        </x-div.box-show>

        
        <x-div.box-show title="Space Asal">
            TX: {{ $data->input?->number ?? '-' }} <br>
            Space: {{ $data?->input?->space?->name ?? '-' }} 
        </x-div.box-show>

        
        <x-div.box-show title="Sender Notes">
            Sender Notes: {{ $data->sender_notes ?? '-' }}
            Handler Notes: {{ $data->handler_notes ?? '-' }}
        </x-div.box-show>
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


        <!-- @if($output_journal) -->
            <!-- <x-div.box-show title="Space Tujuan">
                TX: {{ $data->output?->number ?? '-' }}
                Space: {{ $data?->output?->space?->name ?? '-' }} <br>
            </x-div.box-show> -->
        <!-- @else -->
            <!-- <x-div.box-input for="space_origin" title="Space Asal" label="Space Asal">  
                <x-input-select name="space_origin" id="{{ $form['mode'] ?? '' }}_space_origin" class="w-full px-4 py-2 border rounded">
                    @if($input_journal)
                        <option value="{{ $input_journal->space_id }}">{{ $input_journal->space->name }}</option>
                    @else
                        <option value="">Pilih Space Asal</option>
                        @foreach ($spaces_dest as $space)
                            <option value="{{ $space->id }}">{{ $space->name }}</option>
                        @endforeach
                    @endif
                </x-input-select>
            </x-div.box-input> -->
        <!-- @endif -->


        <div class="form-group">
            <x-input-label for="handler_notes">Handler Notes</x-input-label>
            <x-input-textarea name="handler_notes" class="form-control" id="{{ $form['mode'] ?? '' }}_handler_notes"></x-input-textarea>
        </div>
    @endif
</div>
