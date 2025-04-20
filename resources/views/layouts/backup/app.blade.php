<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nerpai') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Add your custom styles here */
        .activated {
            background-color: rgb(138, 17, 17);
            outline: none;
            color: #FFFFFF;
            /* Teks berwarna putih saat aktif (light mode) */

            /* Dark Mode Styles */
            @media (prefers-color-scheme: dark) {
                background-color: #374151;
                /* Latar belakang gelap untuk dark mode */
                color: #FFFFFF;
                /* Teks putih untuk kontras tinggi di dark mode */
            }
        }

        /* Hover Effects */
        .hover\:bg-gray-100:hover {
            background-color: #f3f4f6;
            /* Hover color for light mode */
        }

        .dark\:hover\:bg-gray-700:hover {
            background-color: #4b5563;
            /* Hover color for dark mode */
        }
    </style>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900"> 
    @include('layouts.company-navbar')
    @include('layouts.sidebar-company')
    <!-- Page Content -->

    <main class="p-4 sm:ml-64 sm:mt-12">
        <div id="content">
            {{ $slot }}
            

            @include('layouts.footer')
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function setActive(element) {
            // Remove 'active' class from all items
            document.querySelectorAll('a').forEach(item => {
                item.classList.remove('active');
                item.classList.remove('focus:outline-none');
                item.classList.remove('focus:ring');
                item.classList.remove('focus:ring-gray-300');
                item.classList.remove('active:bg-gray-700');
            });

            // Add 'active' class to the clicked item
            element.classList.add('active');
            element.classList.add('focus:outline-none');
            element.classList.add('focus:ring');
            element.classList.add('focus:ring-gray-300');
            element.classList.add('active:bg-gray-700');
        }
    </script>

    @stack('script')
    @if (session('success'))
        <script>
            Swal.fire({
                title: "Success",
                text: "{{ session('success') }}",
                icon: "success",
                timer: 3000,
                customClass: {
                    popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500',   // Customize the popup box
                    title: 'text-xl font-semibold text-green-600',
                    header: 'text-sm text-gray-700 dark:text-white',
                    content: 'text-sm text-gray-700 dark:text-white',
                    confirmButton: 'bg-emerald-900 text-white font-bold py-2 px-4 rounded-md hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-300' // Customize the button
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error",
                timer: 3000,
                customClass: {
                    popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500 dark:text-white',   // Customize the popup box
                    title: 'text-xl font-semibold text-green-600',
                    header: 'text-sm text-gray-700 dark:text-white',
                    content: 'text-sm text-gray-700 dark:text-white',
                    confirmButton: 'bg-red-900 text-white font-bold py-2 px-4 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-300' // Customize the button
                }
            });
        </script>
    @endif

    @if(session('status'))
        <script>
            let status = "{{ session('status') }}";

            if (status === 'profile-updated') {
                Swal.fire({
                    title: 'Profile Updated!',
                    text: 'Your profile has been updated successfully.',
                    icon: 'success',
                    timer: 3000,
                    customClass: {
                        popup: 'bg-white p-6 rounded-lg shadow-xl dark:bg-gray-900 dark:border dark:border-sky-500 dark:text-white',   // Customize the popup box
                        title: 'text-xl font-semibold text-green-600',
                        header: 'text-sm text-gray-700 dark:text-white',
                        content: 'text-sm text-gray-700 dark:text-white',
                        confirmButton: 'bg-emerald-900 text-white font-bold py-2 px-4 rounded-md hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-300' // Customize the button
                    }
                });
            } else if (status === 'profile-update-failed') {
                Swal.fire({
                    title: 'Update Failed!',
                    text: 'There was an error updating your profile.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        </script>
    @endif
    <script>
        $(document).ready(function () {
            if (typeof simpleDatatables !== 'undefined' && typeof simpleDatatables.DataTable !== 'undefined') {

                const tableIds = ["search-table", "search-table1"]; // Add all unique IDs here
                tableIds.forEach(function (id) {
                    const tableElement = document.getElementById(id);
                    if (tableElement) {
                        new simpleDatatables.DataTable(`#${id}`, {
                            searchable: true,
                            sortable: true
                        });
                    }
                });
            }
        });
    </script>

    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function () {

            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

                // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }

        });
    </script>

</body>

</html>