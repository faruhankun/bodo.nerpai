@php
    $layout = $layout ?? session('layout') ?? 'lobby';

    $token = get_token_web(request());
    $space_id = get_space_id(request());
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div id="react-page-root"
        data-token="{{ $token }}"
        data-space-id="{{ $space_id }}">
    </div>

    <div id="react-page-spaces">
    </div>


    <script type="module" src="{{ asset('react/assets/index.js') }}"></script>
</x-dynamic-component>