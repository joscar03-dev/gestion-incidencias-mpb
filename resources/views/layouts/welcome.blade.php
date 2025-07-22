<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">

    <title>@yield('title', 'Centro de Soporte - Sistema de Gestión de Incidencias')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @include('components.styles')

    @stack('styles')
</head>
<body class="antialiased min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    @include('components.navigation')

    <main class="pt-16 min-h-screen bg-pattern">
        @include('components.success-message')
        @include('components.error-message')

        @yield('content')
    </main>

    @include('components.footer')
    @include('components.toast')
    @include('components.report-modal')

    <!-- Modal de Autenticación -->
    @livewire('auth-modal')

    @livewireScripts    <!-- Scripts adicionales para mejorar compatibilidad -->
    <script>
        // Configuración simple para Alpine
        document.addEventListener('livewire:load', function () {
            console.log('Livewire cargado');
        });
    </script>

    @stack('scripts')
</body>
</html>
