@auth
<section class="py-16">
    <div class="glass-morphism p-8 rounded-2xl shadow-xl">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Reportar Incidencia
            </h2>
            <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Reporta tu incidencia rápidamente desde aquí. Tu área se asignará automáticamente según tu perfil.
            </p>
        </div>

        <form action="{{ route('tickets.quick-create') }}" method="POST" class="max-w-4xl mx-auto">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="lg:col-span-1">
                    <label for="titulo_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Título de la Incidencia *
                    </label>
                    <input type="text"
                           id="titulo_rapid"
                           name="titulo"
                           required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                           placeholder="Ej: No puedo acceder al sistema">
                </div>

                <div class="lg:col-span-1">
                    <label for="area_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Área Asignada
                    </label>
                    <div class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ auth()->user()->area->nombre ?? 'Sin área asignada' }}
                        </div>
                    </div>
                    <input type="hidden" name="area_id" value="{{ auth()->user()->area->id ?? '' }}">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Área asignada automáticamente según tu perfil
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <label for="descripcion_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Descripción del Problema *
                    </label>
                    <textarea id="descripcion_rapid"
                              name="descripcion"
                              rows="4"
                              required
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                              placeholder="Describe el problema en detalle. Incluye información sobre cuándo ocurrió, qué estabas haciendo, mensajes de error, etc."></textarea>
                </div>

                <div class="lg:col-span-1">
                    <label for="prioridad_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nivel de Prioridad
                    </label>
                    <select id="prioridad_rapid"
                            name="prioridad"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors duration-200">
                        <option value="Baja">🟢 Baja - No urgente</option>
                        <option value="Media" selected>🟡 Media - Normal</option>
                        <option value="Alta">🟠 Alta - Requiere atención</option>
                        <option value="Urgente">🔴 Urgente - Crítico</option>
                    </select>
                </div>

                <div class="lg:col-span-1 flex items-end">
                    <button type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-300">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Reportar Incidencia
                    </button>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    ¿Necesitas opciones más avanzadas o adjuntar archivos?
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium underline">
                        Usar el formulario completo
                    </a>
                </p>
            </div>
        </form>
    </div>
</section>
@endauth
