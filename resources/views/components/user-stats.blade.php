@auth
<section class="py-16">
    <div class="glass-morphism p-8 rounded-2xl shadow-xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Resumen de tus Tickets
            </h2>
            <p class="text-gray-600 dark:text-gray-300">
                Estado actual de todas tus solicitudes de soporte
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center card-hover">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->count() }}</div>
                    <div class="text-sm font-medium opacity-90">Total de Tickets</div>
                </div>
            </div>

            <div class="text-center card-hover">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->where('estado', 'Abierto')->count() }}</div>
                    <div class="text-sm font-medium opacity-90">Tickets Abiertos</div>
                </div>
            </div>

            <div class="text-center card-hover">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->where('estado', 'En Progreso')->count() }}</div>
                    <div class="text-sm font-medium opacity-90">En Progreso</div>
                </div>
            </div>

            <div class="text-center card-hover">
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->where('estado', 'Cerrado')->count() }}</div>
                    <div class="text-sm font-medium opacity-90">Completados</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endauth
