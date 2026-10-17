@php($c = config('hnbloom'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-ad-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', $c['name'] ?? 'Laravel') }}</title>

        <script>
            /* Même thème (clair/sombre) que l'administration, appliqué avant l'affichage */
            try { var t = localStorage.getItem('hn-admin-theme'); if (t) document.documentElement.setAttribute('data-ad-theme', t); } catch (e) {}
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@vite(['resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/hb-app.css') }}">
    </head>
    <body class="hb-app font-sans antialiased">
        <div class="hb-shell min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="hb-header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if(session('success') || session('status') || session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    @if(session('success'))<div class="hb-flash hb-flash-ok">{{ session('success') }}</div>@endif
                    @if(session('status') && !session('success'))<div class="hb-flash hb-flash-ok">{{ session('status') }}</div>@endif
                    @if(session('error'))<div class="hb-flash hb-flash-bad">{{ session('error') }}</div>@endif
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
