@php
    $layout = session('layout') ?? 'guest';
@endphp
<x-dynamic-component :component="'layouts.' . $layout">
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center">
        <h1 class="text-6xl font-bold text-red-500">403 - Forbidden</h1>
        <p class="text-xl text-gray-600 mt-4">Oops! Akses ditolak :(</p>
        <a href="{{ url()->previous() }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">
            Kembali ke Halaman Sebelumnya
        </a>
    </div>
</x-dynamic-component>