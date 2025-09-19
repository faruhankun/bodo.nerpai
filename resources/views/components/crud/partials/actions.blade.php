@props([
    'actions' => [
        'show' => '',
        'show_modal' => '',
        'edit' => '',
        'edit_modal' => '',
        'delete' => '',
        'delete_id' => null,
    ]
])

@php
    $actions['show'] = $actions['show'] ?? '';
    $actions['show_modal'] = $actions['show_modal'] ?? '';
    $actions['edit'] = $actions['edit'] ?? '';
    $actions['delete'] = $actions['delete'] ?? '';
    $actions['delete_id'] = $actions['delete_id'] ?? $data->id;

    $space_role = session('space_role') ?? null;
@endphp

<div class="flex gap-3 justify-end">
    @if($actions['show'] == 'modal')
        @if($actions['show_modal'] != '')
            @include($actions['show_modal'], ['data' => $data])
        @endif
    @elseif($actions['show'] == 'button')
        <x-button-show :route="route($route . '.show', $data->id)" target="_blank"/>
    @elseif($actions['show'] == 'modaljs')
        <x-buttons.button-showjs onclick="showjs({{ $data }})"></x-buttons.button-showjs>
    @endif


    @if($space_role == 'owner' || $space_role == 'admin')
        @if($actions['edit'] == 'modal')
            <x-button2 onclick="edit({{ $data }})" class="btn btn-primary">Edit</x-button2>
        @elseif($actions['edit'] == 'button')
        <x-button-edit :route="route($route . '.edit', $data->id)" target="_blank"/>
        @endif


        @if($actions['delete'] == 'button')
            <x-button-delete :route="route($route . '.destroy', $actions['delete_id'])" />
        @endif
    @endif
</div>