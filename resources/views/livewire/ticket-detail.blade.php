<div wire:poll.5s="loadTicket">
    @if($ticket)
        <div class="space-y-6">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $ticket->titulo }}
                    </h2>
                    <div class="flex items-center gap-3">
                        @php
                            $statusColors = [
                                'Abierto' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'En Progreso' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'Cerrado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'Cancelado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                            ];

                            $priorityColors = [
                                'Baja' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                'Media' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'Alta' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                'Critica' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                            ];
                        @endphp

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$ticket->estado] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $ticket->estado }}
                        </span>

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $priorityColors[$ticket->prioridad] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $ticket->prioridad }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Área
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $ticket->area->nombre ?? 'Sin área' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha de Creación
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : 'No disponible' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Última Actualización
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                        </p>
                    </div>

                    @if($ticket->fecha_resolucion)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Fecha de Resolución
                            </label>
                            <p class="text-gray-900 dark:text-white">
                                @php
                                    try {
                                        if ($ticket->fecha_resolucion instanceof \Carbon\Carbon) {
                                            echo $ticket->fecha_resolucion->format('d/m/Y H:i');
                                        } elseif (is_string($ticket->fecha_resolucion)) {
                                            echo \Carbon\Carbon::parse($ticket->fecha_resolucion)->format('d/m/Y H:i');
                                        } else {
                                            echo 'Fecha no válida';
                                        }
                                    } catch (\Exception $e) {
                                        echo 'Fecha no disponible';
                                    }
                                @endphp
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descripción
                </label>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $ticket->descripcion }}</p>
                </div>
            </div>

            @if($ticket->comentarios_resolucion)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Comentarios de Resolución
                    </label>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $ticket->comentarios_resolucion }}</p>
                    </div>
                </div>
            @endif

            @if($ticket->estado === 'Cerrado' && $ticket->comentario)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Solución del Problema
                    </label>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $ticket->comentario }}</p>
                    </div>
                </div>
            @endif

            @if($ticket->comments && $ticket->comments->count() > 0)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Comentarios del Ticket
                        </label>
                        <div class="flex items-center gap-2">
                            <div wire:loading.delay.shorter class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Actualizando...
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                🔄 Auto-actualización cada 5s
                            </span>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="space-y-4">
                            @foreach($ticket->comments->sortByDesc('created_at') as $comment)
                                <div class="bg-white dark:bg-gray-600 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ strtoupper(substr($comment->author->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $comment->author->name ?? 'Usuario' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($comment->parent_id)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Respuesta
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                                        {{ $comment->body }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario para agregar comentarios -->
            @if($ticket->estado !== 'Cerrado' && $ticket->estado !== 'Cancelado')
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Agregar Comentario
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <form wire:submit.prevent="addComment">
                            <div class="mb-4">
                                <textarea
                                    wire:model="newComment"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    rows="3"
                                    placeholder="Escribe tu comentario aquí..."
                                ></textarea>
                                @error('newComment')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove>Agregar Comentario</span>
                                    <span wire:loading>Agregando...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if($ticket->attachment && count($ticket->attachment) > 0)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Archivos Adjuntos
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="space-y-2">
                            @foreach($ticket->attachment as $archivo)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            @php
                                                // Obtener el nombre del archivo desde la ruta
                                                $filename = is_string($archivo) ? basename($archivo) : (isset($archivo['nombre']) ? $archivo['nombre'] : 'Archivo');
                                                $filePath = is_string($archivo) ? $archivo : (isset($archivo['ruta']) ? $archivo['ruta'] : $archivo);
                                            @endphp
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $filename }}</p>
                                            @if(is_array($archivo) && isset($archivo['tamaño']))
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($archivo['tamaño'] / 1024, 1) }} KB</p>
                                            @endif
                                        </div>
                                    </div>
                                    <a
                                        href="{{ asset('storage/' . $filePath) }}"
                                        target="_blank"
                                        class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                                    >
                                        Descargar
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botones de acción -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    @if($ticket->estado !== 'Cerrado' && $ticket->estado !== 'Cancelado')
                        <button
                            wire:click="cancelTicket"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200"
                            wire:confirm="¿Estás seguro de que quieres cancelar este ticket?"
                        >
                            Cancelar Ticket
                        </button>
                    @endif

                    <button
                        wire:click="$refresh"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                    >
                        Actualizar
                    </button>
                </div>

                <div class="flex space-x-3">
                    <button
                        wire:click="exportTicketPdf"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2"
                        title="Exportar ticket a PDF"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Exportar PDF
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Ticket no encontrado</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                El ticket que buscas no existe o no tienes permisos para verlo.
            </p>
        </div>
    @endif
</div>
