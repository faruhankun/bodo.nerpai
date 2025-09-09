@extends('layouts.base', [
    'navbar_left' => [
    ],
    'navbar_right' => [
    ],
    'navbar_dropdown_user' => [
    ],
])

@section('sidebar')
    @include('layouts.sidebar-company')
@endsection

@section('main-content')
    {{ $slot }}
    @include('layouts.footer')
@endsection