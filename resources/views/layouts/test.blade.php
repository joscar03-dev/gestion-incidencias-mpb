<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Livewire</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Test Livewire Functionality</h1>

        @yield('content')
    </div>

    @livewireScripts

    <script>
        // Debug info
        console.log('Page loaded');
        console.log('Livewire available:', typeof Livewire !== 'undefined');
        console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Test if Livewire is working
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Livewire !== 'undefined') {
                console.log('Livewire is available');
                console.log('Livewire components:', Livewire.components);
            } else {
                console.error('Livewire is not available');
            }
        });
    </script>
</body>
</html>
