<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nerpai') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="sweetalert2.min.css"> 
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <!-- <img src="{{ asset('svg/hehe.svg') }}" class="h-20 w-20 me-3 dark:invert" alt="Nerpai Logo" /> -->
                <img aria-hidden="true" alt="bodo" src="{{ asset('svg/hehe.svg') }}"
                    class="w-48 h-48 mb-4" />
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg dark:bg-gray-800">
         
            
            {{ $slot }}

            
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @stack('script')

    @if (session('error'))
    <script>
        Swal.fire({
            title: "Error",
            text: "{{ session('error') }}",
            icon: "error",
            timer: 3000,
            customClass: {
                popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500 dark:text-white',   // Customize the popup box
                title: 'text-xl font-semibold text-red-600',
                header: 'text-sm text-gray-700 dark:text-white',
                content: 'text-sm text-gray-700 dark:text-white',
                confirmButton: 'bg-red-900 text-white font-bold py-2 px-4 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-300' // Customize the button
            }
        });
    </script>
@endif
<script src="{{ asset('js/phoneFormatter.js') }}"></script>

</body>

</html>