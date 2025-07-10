@auth
<!-- Enhanced Modal -->
<div x-show="showReportModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
        <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50 backdrop-blur-sm" @click="showReportModal = false"></div>

        <div class="inline-block w-full max-w-2xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform glass-morphism shadow-2xl rounded-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 rounded-full p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Reportar Incidencia</h3>
                            <p class="text-red-100 text-sm">Rápido y sencillo</p>
                        </div>
                    </div>
                    <button @click="showReportModal = false" class="text-white hover:text-red-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form action="{{ url('/reportar/tickets') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="titulo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Título de la Incidencia *
                            </label>
                            <input type="text"
                                   id="titulo"
                                   name="titulo"
                                   x-model="reportForm.titulo"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                   placeholder="Describe brevemente el problema">
                        </div>

                        <div>
                            <label for="area_modal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
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
                                Área asignada automáticamente según tu perfil
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Descripción del Problema *
                        </label>
                        <textarea id="descripcion"
                                  name="descripcion"
                                  x-model="reportForm.descripcion"
                                  rows="4"
                                  required
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                  placeholder="Describe el problema en detalle. Incluye cuándo ocurrió, qué estabas haciendo, mensajes de error, etc."></textarea>
                    </div>

                    <div>
                        <label for="prioridad" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Nivel de Prioridad
                        </label>
                        <select id="prioridad"
                                name="prioridad"
                                x-model="reportForm.prioridad"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-colors duration-200">
                            <option value="Baja">🟢 Baja - No urgente</option>
                            <option value="Media">🟡 Media - Normal</option>
                            <option value="Alta">🟠 Alta - Requiere atención</option>
                            <option value="Urgente">🔴 Urgente - Crítico</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                @click="showReportModal = false"
                                class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 rounded-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg focus:outline-none focus:ring-4 focus:ring-red-300">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Reportar Incidencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth
