<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
<script src="//unpkg.com/alpinejs" defer></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body 
    class="antialiased"
    x-data="{ dark: (localStorage.getItem('theme') ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark' }"
    x-init="$watch('dark', val => { document.documentElement.classList.toggle('dark', val); localStorage.setItem('theme', val ? 'dark' : 'light') }); document.documentElement.classList.toggle('dark', dark);"
>
<nav class="fixed top-0 left-0 p-6 z-20 flex items-center space-x-3">
    <span class="inline-flex items-center">
        <svg class="w-9 h-9 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white" style="font-family: 'Fira Sans', 'Segoe UI', Arial, sans-serif;">
            Centro de Soporte
        </span>
    </span>
</nav>
    <div class="fixed top-4 right-4 z-50">
        <button
            @click="dark = !dark"
            class="rounded-full p-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
            :aria-label="dark ? 'Modo claro' : 'Modo oscuro'"
            title="Cambiar modo"
        >
            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
            </svg>
        </button>
    </div>
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-end z-10 space-x-4">
                @auth
                    <a href="{{ url('/reportar/tickets/create') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                        Dashboard
                    </a>
                @else
                    <a href="{{ url('/login') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                        Login
                    </a>
                    <a href="{{ url('/register') }}"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                        Register
                    </a>
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <svg viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-auto bg-gray-100 dark:bg-gray-900">
                    <path
                        d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z"
                        fill="#FF2D20" />
                </svg>
            </div>

            <div class="mt-16">
                <div class="flex flex-col items-center justify-center bg-gradient-to-br from-green-100 via-white to-green-200 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-3xl shadow-2xl p-12 border border-green-200 dark:border-gray-700">
                    <h2 class="text-4xl font-extrabold text-green-700 dark:text-green-300 mb-4 drop-shadow-lg font-mono" style="font-family: 'Fira Mono', 'Consolas', monospace;">
                        ¿Necesitas ayuda?
                    </h2>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-8 font-sans">Estamos aquí para ayudarte. Envía tu consulta y te responderemos lo antes posible.</p>
                    <a href="{{ url('/reportar/tickets/create') }}"
                       class="inline-block px-10 py-4 bg-gradient-to-r from-green-500 via-green-600 to-emerald-500 text-white font-bold rounded-full shadow-lg hover:scale-105 hover:from-emerald-500 hover:to-green-600 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-offset-2">
                        Crear Ticket
                    </a>
                    
                </div>
            </div>
            <!-- Preguntas Generales (Accordion) -->
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Preguntas Generales</h2>
                <div x-data="{ open: null }" class="space-y-4">
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow">
                        <button @click="open === 1 ? open = null : open = 1" class="w-full flex justify-between items-center p-4 focus:outline-none">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">¿Cómo puedo crear una cuenta?</span>
                            <svg :class="{'rotate-180': open === 1}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === 1" x-collapse class="px-4 pb-4 text-gray-700 dark:text-gray-300">
                            Puedes crear una cuenta haciendo clic en el botón "Register" en la parte superior derecha y completando el formulario.
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow">
                        <button @click="open === 2 ? open = null : open = 2" class="w-full flex justify-between items-center p-4 focus:outline-none">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">¿Dónde puedo ver mis tickets?</span>
                            <svg :class="{'rotate-180': open === 2}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === 2" x-collapse class="px-4 pb-4 text-gray-700 dark:text-gray-300">
                            Una vez autenticado, accede al Dashboard para ver y gestionar tus tickets.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Problemas Técnicos (Accordion) -->
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Problemas Técnicos</h2>
                <div x-data="{ open: null }" class="space-y-4">
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow">
                        <button @click="open === 1 ? open = null : open = 1" class="w-full flex justify-between items-center p-4 focus:outline-none">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">No puedo iniciar sesión</span>
                            <svg :class="{'rotate-180': open === 1}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === 1" x-collapse class="px-4 pb-4 text-gray-700 dark:text-gray-300">
                            Verifica que tu correo y contraseña sean correctos. Si olvidaste tu contraseña, utiliza la opción de recuperación.
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow">
                        <button @click="open === 2 ? open = null : open = 2" class="w-full flex justify-between items-center p-4 focus:outline-none">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">No recibo notificaciones</span>
                            <svg :class="{'rotate-180': open === 2}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === 2" x-collapse class="px-4 pb-4 text-gray-700 dark:text-gray-300">
                            Revisa tu bandeja de spam o verifica que tu correo esté correctamente registrado en tu perfil.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Explora por Categorías -->
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Explora por Categorías</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow p-6 flex flex-col items-center hover:scale-105 transition">
                        <div class="h-12 w-12 bg-green-100 dark:bg-green-800/20 flex items-center justify-center rounded-full mb-4">
                            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L6 21h12l-3.75-4M12 3v14"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Soporte Técnico</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Problemas con hardware, software o acceso a sistemas.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow p-6 flex flex-col items-center hover:scale-105 transition">
                        <div class="h-12 w-12 bg-green-100 dark:bg-green-800/20 flex items-center justify-center rounded-full mb-4">
                            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Incidencias Generales</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Reporta cualquier otro tipo de incidencia o solicitud.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg shadow p-6 flex flex-col items-center hover:scale-105 transition">
                        <div class="h-12 w-12 bg-green-100 dark:bg-green-800/20 flex items-center justify-center rounded-full mb-4">
                            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Solicitudes Administrativas</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Trámites, permisos y otros servicios administrativos.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
                <div class="text-center text-sm sm:text-start">
                    &nbsp;
                </div>

                <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-end sm:ms-0">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </div>
</body>

</html>
