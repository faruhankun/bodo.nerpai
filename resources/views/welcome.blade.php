<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Nerpai') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="">
    <main>
        <div class="min-h-screen flex items-center justify-center bg-background text-primary-foreground">
            <div class="max-w-md p-6 bg-card shadow-lg rounded-lg flex flex-col items-center">
                <img aria-hidden="true" alt="haebot" src="{{ asset('svg/hehe.svg') }}"
                    class="w-24 h-24 mb-4" />
                <h1 class="text-3xl font-bold mb-2 text-center text-primary">Welcome to Nerpai!!</h1>
                <p class="text-sm text-center text-gray-500">Your go-to destination for a highly
                    performant, highly adaptive ERP solution</p>
                <div class="flex space-x-4 mt-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('lobby') }}"
                                class="bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:bg-primary/80">Lobby</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:bg-primary/80">Login</a>
                            <a href="{{ route('register') }}"
                                class="bg-secondary text-secondary-foreground px-4 py-2 rounded-lg hover:bg-secondary/80">Register</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>

</html>
