<div>
    @if(auth()->check())
        @if($isDashboard)
            <!-- Formulario completo para el dashboard -->
            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Título -->
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Título del Ticket *
                    </label>
                    <input
                        type="text"
                        id="titulo"
                        wire:model="titulo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('titulo') border-red-500 @enderror"
                        placeholder="Ingresa un título descriptivo para tu ticket"
                        maxlength="255"
                    >
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descripción del Problema *
                    </label>
                    <textarea
                        id="descripcion"
                        wire:model="descripcion"
                        rows="5"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                        placeholder="Describe detalladamente el problema o solicitud..."
                        maxlength="1000"
                    ></textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Ticket -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tipo de Ticket *
                    </label>
                    <select
                        id="tipo"
                        wire:model.live="tipo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tipo') border-red-500 @enderror"
                    >
                        <option value="">Selecciona el tipo</option>
                        <option value="Incidencia">🚨 Incidencia - Interrupciones o fallos del servicio</option>
                        <option value="Problema">🔧 Problema - Causa subyacente de múltiples incidencias</option>
                        <option value="Requerimiento">📝 Requerimiento - Solicitudes de nuevos servicios o elementos</option>
                        <option value="Cambio">🔄 Cambio - Modificaciones en servicios o configuraciones</option>
                    </select>
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Selecciona el tipo que mejor describa tu solicitud
                    </p>
                </div>

                <!-- Categoría de Dispositivo (solo para Requerimientos) -->
                @if($tipo === 'Requerimiento')
                    <div>
                        <label for="categoria_dispositivo_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoría de Dispositivo *
                        </label>
                        <select
                            id="categoria_dispositivo_id"
                            wire:model="categoria_dispositivo_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('categoria_dispositivo_id') border-red-500 @enderror"
                        >
                            <option value="">Selecciona una categoría</option>
                            @foreach($categorias as $id => $nombre)
                                <option value="{{ $id }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_dispositivo_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Esta información se utilizará para crear automáticamente una solicitud de dispositivo
                        </p>
                    </div>
                @endif

                <!-- Prioridad y Área -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prioridad -->
                    <div>
                        <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Prioridad *
                        </label>
                        <select
                            id="prioridad"
                            wire:model="prioridad"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('prioridad') border-red-500 @enderror"
                        >
                            <option value="Baja">🟢 Baja</option>
                            <option value="Media">🟡 Media</option>
                            <option value="Alta">🟠 Alta</option>
                            <option value="Critica">🔴 Crítica</option>
                        </select>
                        @error('prioridad')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Área (Pre-asignada) -->
                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Área *
                        </label>
                        @if(auth()->user()->area_id)
                            <input
                                type="text"
                                value="{{ auth()->user()->area->nombre ?? 'Área no asignada' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-600 dark:border-gray-600 dark:text-white"
                                readonly
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Área asignada automáticamente</p>
                        @else
                            <select
                                id="area_id"
                                wire:model="area_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('area_id') border-red-500 @enderror"
                            >
                                <option value="">Selecciona un área</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('area_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Subida de archivos -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Archivos Adjuntos (Opcional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6">
                        <input
                            type="file"
                            wire:model="archivos"
                            multiple
                            class="hidden"
                            id="file-upload"
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip,.rar,.xls,.xlsx,.ppt,.pptx"
                        >
                        <label
                            for="file-upload"
                            class="cursor-pointer flex flex-col items-center justify-center"
                        >
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Click para subir archivos o arrastra aquí
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                JPG, PNG, PDF, DOC, TXT, ZIP, XLS, PPT (Max: 10MB por archivo)
                            </span>
                        </label>
                    </div>

                    <!-- Lista de archivos seleccionados -->
                    @if(!empty($archivos))
                        <div class="mt-4 space-y-2">
                            @foreach($archivos as $index => $archivo)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $archivo->getClientOriginalName() }}</span>
                                        <span class="text-xs text-gray-500 ml-2">({{ number_format($archivo->getSize() / 1024, 1) }} KB)</span>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="removeFile({{ $index }})"
                                        class="text-red-500 hover:text-red-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @error('archivos.*')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="resetForm"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Limpiar
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 flex items-center gap-2"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Crear Ticket</span>
                        <span wire:loading>Creando...</span>
                    </button>
                </div>
            </form>
        @else
            <!-- Botón para abrir el modal (modo tradicional) -->
            <button
                wire:click="openModal"
                class="btn-primary bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Nuevo Ticket
            </button>
        @endif

        <!-- Modal (solo cuando no está en dashboard) -->
        @if($showModal && !$isDashboard)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                Crear Nuevo Ticket
                            </h3>
                            <button
                                wire:click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="p-6 space-y-6">
                            <!-- Título -->
                            <div>
                                <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Título del Ticket *
                                </label>
                                <input
                                    type="text"
                                    id="titulo"
                                    wire:model="titulo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('titulo') border-red-500 @enderror"
                                    placeholder="Ingresa un título descriptivo para tu ticket"
                                    maxlength="255"
                                >
                                @error('titulo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Área -->
                            <div>
                                <label for="area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Área *
                                </label>
                                <select
                                    id="area_id"
                                    wire:model="area_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('area_id') border-red-500 @enderror"
                                >
                                    <option value="">Selecciona un área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prioridad -->
                            <div>
                                <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Prioridad *
                                </label>
                                <select
                                    id="prioridad"
                                    wire:model="prioridad"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('prioridad') border-red-500 @enderror"
                                >
                                    <option value="Baja">🟢 Baja</option>
                                    <option value="Media">🟡 Media</option>
                                    <option value="Alta">🟠 Alta</option>
                                    <option value="Critica">🔴 Crítica</option>
                                </select>
                                @error('prioridad')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de Ticket -->
                            <div>
                                <label for="tipo_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo de Ticket *
                                </label>
                                <select
                                    id="tipo_modal"
                                    wire:model.live="tipo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tipo') border-red-500 @enderror"
                                >
                                    <option value="">Selecciona el tipo</option>
                                    <option value="Incidencia">🚨 Incidencia</option>
                                    <option value="Problema">🔧 Problema</option>
                                    <option value="Requerimiento">📝 Requerimiento</option>
                                    <option value="Cambio">🔄 Cambio</option>
                                </select>
                                @error('tipo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Categoría de Dispositivo (solo para Requerimientos) -->
                            @if($tipo === 'Requerimiento')
                                <div>
                                    <label for="categoria_dispositivo_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Categoría de Dispositivo *
                                    </label>
                                    <select
                                        id="categoria_dispositivo_id_modal"
                                        wire:model="categoria_dispositivo_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('categoria_dispositivo_id') border-red-500 @enderror"
                                    >
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categorias as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria_dispositivo_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Descripción -->
                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Descripción *
                                </label>
                                <textarea
                                    id="descripcion"
                                    wire:model="descripcion"
                                    rows="5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                                    placeholder="Describe detalladamente el problema o solicitud..."
                                    maxlength="1000"
                                ></textarea>
                                <div class="flex justify-between mt-1">
                                    @error('descripcion')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Describe el problema con el mayor detalle posible.</p>
                                    @enderror
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ strlen($descripcion) }}/1000
                                    </p>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            Consejos para un mejor soporte
                                        </h4>
                                        <ul class="mt-2 text-sm text-blue-700 dark:text-blue-300 space-y-1">
                                            <li>• Proporciona pasos detallados para reproducir el problema</li>
                                            <li>• Incluye capturas de pantalla si es necesario</li>
                                            <li>• Menciona el navegador o sistema operativo que usas</li>
                                            <li>• Indica si el problema ocurre siempre o solo a veces</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 flex items-center gap-2"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading>
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove>Crear Ticket</span>
                                    <span wire:loading>Creando...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Acceso requerido</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Debes iniciar sesión para crear un ticket de soporte.
            </p>
        </div>
    @endif
</div>
