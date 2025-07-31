@props([
    'actions' => [
        'show' => '',
        'show_modal' => '',
        'edit' => '',
        'edit_modal' => '',
        'delete' => '',
    ]
])

@php
    $actions['show'] = $actions['show'] ?? '';
    $actions['show_modal'] = $actions['show_modal'] ?? '';
    $actions['edit'] = $actions['edit'] ?? '';
    $actions['delete'] = $actions['delete'] ?? '';

    $space_role = session('space_role') ?? null;
@endphp

<div class="flex gap-3 justify-end">
    @if($actions['show'] == 'modal')
        @if($actions['show_modal'] != '')
            @include($actions['show_modal'], ['data' => $data])
        @endif
    @elseif($actions['show'] == 'button')
        <x-button-show :route="route($route . '.show', $data->id)" />
    @elseif($actions['show'] == 'modaljs')
        <x-buttons.button-showjs onclick="showjs({{ $data }})"></x-buttons.button-showjs>
    @endif


    @if($space_role == 'owner' || $space_role == 'admin')
        @if($actions['edit'] == 'modal')
            <x-button2 onclick="edit({{ $data }})" class="btn btn-primary">Edit</x-button2>
        @elseif($actions['edit'] == 'button')
        <x-button-edit :route="route($route . '.edit', $data->id)" />
        @endif


        @if($actions['delete'] == 'button')
            <x-button-delete :route="route($route . '.destroy', $data->id)" />
        @endif
    @endif
</div>