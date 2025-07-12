@php
    $layout = $layout ?? session('layout') ?? 'lobby';

    $token = Auth::user()?->api_token ?? '25|c3o9hnSHT1xZVoh3ndZQoLBkZRKTc67mKdVWdfKBcd6a5744';
    $space_id = get_space_id(request());
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div id="react-page-root"
        data-token="{{ $token }}"
        data-space-id="{{ $space_id }}">
    </div>


    <script type="module" src="{{ asset('react/assets/index.js') }}"></script>
</x-dynamic-component>