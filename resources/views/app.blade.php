<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Grupos Pequeños AR') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">

    <link rel="stylesheet" href="{{ mix('css/preloader.css') }}">

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @routes
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    @inertiaHead
</head>


<body class="antialiased">
    <div name="load_container">

        @include('layout.preloader')
    </div>


    @inertia
    <!-- Contenedor de Inertia

    @env('local')
    <script src="http://localhost:8080/js/bundle.js"></script>
    @endenv
    -->
</body>

{{-- <script>
    function setTheme(theme) {
        const lightTheme = document.getElementById('light-theme');
        const darkTheme = document.getElementById('dark-theme');

        if (theme === 'light') {
            lightTheme.disabled = false;
            darkTheme.disabled = true;
            localStorage.setItem('theme', 'light');
        } else {
            lightTheme.disabled = true;
            darkTheme.disabled = false;
            localStorage.setItem('theme', 'dark');
        }
    }

    function toggleTheme() {
        const currentTheme = localStorage.getItem('theme') || 'light';
        setTheme(currentTheme === 'light' ? 'dark' : 'light');
    }

    // Set the initial theme on page load
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
    });
</script> --}}

</html>
