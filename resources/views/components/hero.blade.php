@auth
<section class="py-20 text-center">
    <div class="floating-animation">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg mb-8">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"/>
            </svg>
        </div>
    </div>

    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
        <span class="gradient-text">Centro de Soporte</span>
    </h1>

    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-12 leading-relaxed">
        Tu portal integral para gestionar incidencias y solicitudes de soporte técnico.
        Reporta problemas, realiza seguimiento y mantente informado del estado de tus tickets.
    </p>

    <!-- Call to Action -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-center mb-16">
        <button @click="showReportModal = true"
               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Reportar Incidencia
        </button>

        <a href="{{ url('/reportar') }}"
           class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
            <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Ver Mis Tickets
        </a>
    </div>

    <div class="inline-flex items-center px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-full">
        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
        <span class="text-sm font-medium text-green-700 dark:text-green-300">
            Bienvenido de vuelta, {{ auth()->user()->name }}
        </span>
    </div>
</section>
@else
<section class="py-20 text-center">
    <div class="floating-animation">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg mb-8">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"/>
            </svg>
        </div>
    </div>

    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
        <span class="gradient-text">Centro de Soporte</span>
    </h1>

    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-12 leading-relaxed">
        Tu portal integral para gestionar incidencias y solicitudes de soporte técnico.
        Reporta problemas, realiza seguimiento y mantente informado del estado de tus tickets.
    </p>

    <!-- Call to Action -->
    <div class="text-center mb-8">
        <div class="glass-morphism p-6 max-w-md mx-auto mb-8">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Acceso Restringido
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Solo <strong>empleados de la empresa</strong> pueden reportar incidencias y acceder al sistema de soporte.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/login') }}"
               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
                <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Iniciar Sesión
            </a>

            <a href="{{ url('/register') }}"
               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
                <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Crear Cuenta
            </a>
        </div>
    </div>
</section>
@endauth
