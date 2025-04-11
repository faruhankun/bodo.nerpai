@props([
    'actions' => [
        'show' => '',
        'show_modal' => '',
        'edit' => '',
        'edit_modal' => '',
        'delete' => '',
    ]
])

<div class="flex gap-3 justify">
    @if($actions['edit'] == 'modal')
        <x-button2 onclick="edit({{ $data }})" class="btn btn-primary">Edit</x-button2>
    @elseif($actions['edit'] == 'button')
       <x-button-edit :route="route($route . '.edit', $data->id)" />
    @endif

    @if($actions['delete'] == 'button')
        <x-button-delete :route="route($route . '.destroy', $data->id)" />
    @endif
</div>