<div>
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

            @if($ticket->attachment)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Archivos Adjuntos
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        @php
                            $archivos = json_decode($ticket->attachment, true);
                        @endphp
                        @if(is_array($archivos) && count($archivos) > 0)
                            <div class="space-y-2">
                                @foreach($archivos as $archivo)
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $archivo['nombre'] }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($archivo['tamaño'] / 1024, 1) }} KB</p>
                                            </div>
                                        </div>
                                        <a
                                            href="{{ asset('storage/' . $archivo['ruta']) }}"
                                            target="_blank"
                                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                                        >
                                            Descargar
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay archivos adjuntos</p>
                        @endif
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
