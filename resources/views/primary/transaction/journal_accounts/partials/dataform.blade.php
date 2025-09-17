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
        <x-div.box-show title="Created By">{{ $data->sender?->name ?? 'N/A' }}</x-div.box-show>
        <x-div.box-show title="Date">{{ optional($data->sent_time)?->format('Y-m-d') ?? '??' }}</x-div.box-show>
        <x-div.box-show title="Sender Notes">{{ $data->sender_notes ?? 'N/A' }}</x-div.box-show>
    @endif

    @if ($form['mode'] == 'edit')
    <input type="hidden" name="handler_id" id="{{ $form['mode'] ?? '' }}_handler_id" value="{{ $player->id }}">
        <div class="form-group">
            <x-input-label for="sent_time">Transaction Date</x-input-label>
            <input type="date" name="sent_time"
                class="bg-gray-100 w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                required id="{{ $form['mode'] ?? '' }}_sent_time">
        </div>

        <div class="form-group">
            <x-input-label for="handler_notes">Handler Notes</x-input-label>
            <x-input-textarea name="handler_notes" class="form-control" id="{{ $form['mode'] ?? '' }}_handler_notes"></x-input-textarea>
        </div>
    @endif
</div>
