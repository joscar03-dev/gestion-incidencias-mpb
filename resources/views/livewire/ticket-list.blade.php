<div>
    @if(auth()->check())
        <!-- Encabezado de la sección -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Mis Tickets de Soporte
            </h2>
            <p class="text-gray-600 dark:text-gray-300">
                Gestiona y realiza seguimiento de tus solicitudes de soporte
            </p>
        </div>

        <!-- Barra de búsqueda y filtros -->
        <div class="glass-morphism p-6 rounded-xl shadow-lg mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Búsqueda -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Buscar tickets..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                </div>

                <!-- Filtros -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <select wire:model.live="status" class="px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="Abierto">Abierto</option>
                        <option value="En Progreso">En Progreso</option>
                        <option value="Cerrado">Cerrado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>

                    <select wire:model.live="priority" class="px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todas las prioridades</option>
                        <option value="Baja">Baja</option>
                        <option value="Media">Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Lista de tickets -->
        <div class="space-y-4">
            @forelse($tickets as $ticket)
                <div class="glass-morphism p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Información del ticket -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $ticket->titulo }}
                                </h3>

                                <!-- Badge de estado -->
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
                                        'Crítica' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    ];
                                @endphp

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ticket->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $ticket->estado }}
                                </span>

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$ticket->prioridad] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $ticket->prioridad }}
                                </span>
                            </div>

                            <p class="text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
                                {{ Str::limit($ticket->descripcion, 120) }}
                            </p>

                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 gap-4">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-6m-2-5a2 2 0 11-4 0 2 2 0 014 0zm-6 0a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>{{ $ticket->area->nombre ?? 'Sin área' }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="viewTicket({{ $ticket->id }})"
                                class="btn-primary bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver detalles
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No hay tickets</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        @if($search || $status || $priority)
                            No se encontraron tickets que coincidan con los filtros aplicados.
                        @else
                            Aún no has creado ningún ticket de soporte.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($tickets->hasPages())
            <div class="mt-8">
                {{ $tickets->links() }}
            </div>
        @endif

        <!-- Modal de detalles del ticket -->
        @if($showModal && $selectedTicket)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                Detalles del Ticket
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

                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $selectedTicket->titulo }}</h4>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$selectedTicket->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $selectedTicket->estado }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$selectedTicket->prioridad] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $selectedTicket->prioridad }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Descripción:</h5>
                                <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $selectedTicket->descripcion }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-1">Área:</h5>
                                    <p class="text-gray-600 dark:text-gray-300">{{ $selectedTicket->area->nombre ?? 'Sin área' }}</p>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-1">Creado:</h5>
                                    <p class="text-gray-600 dark:text-gray-300">{{ $selectedTicket->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            @if($selectedTicket->fecha_resolucion)
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-1">Fecha de Resolución:</h5>
                                    <p class="text-gray-600 dark:text-gray-300">{{ $selectedTicket->fecha_resolucion->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif

                            @if($selectedTicket->comentarios_resolucion)
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Comentarios de Resolución:</h5>
                                    <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $selectedTicket->comentarios_resolucion }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end">
                            <button
                                wire:click="closeModal"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Acceso requerido</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Debes iniciar sesión para ver tus tickets de soporte.
            </p>
        </div>
    @endif
</div>
