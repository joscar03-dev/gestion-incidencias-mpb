<div>
    <!-- Navegación de pestañas -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-8">
                <!-- Botón Mis Tickets -->
                <button
                    wire:click="showTickets"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $currentView === 'tickets' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Mis Tickets
                </button>

                <!-- Botón Crear Ticket -->
                <button
                    wire:click="showCreateTicket"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $currentView === 'create' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Ticket
                </button>

                <!-- Botón Dashboard -->
                <button
                    wire:click="showHome"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $currentView === 'home' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"/>
                    </svg>
                    Dashboard
                </button>
            </div>
        </div>
    </div>

    <!-- Contenido dinámico -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($currentView === 'tickets')
            @livewire('ticket-list', [], key('ticket-list-dashboard'))

        @elseif($currentView === 'create')
            <!-- Crear Ticket -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Crear Nuevo Ticket</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Reporta una nueva incidencia o solicitud de soporte</p>
            </div>
            @livewire('ticket-create', ['isDashboard' => true], key('ticket-create-dashboard'))

        @elseif($currentView === 'home')
            <!-- Dashboard/Home -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Dashboard</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Bienvenido al Centro de Soporte</p>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tickets Activos</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->tickets()->where('estado', '!=', 'Cerrado')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendientes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->tickets()->where('estado', 'Abierto')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Resueltos</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->tickets()->where('estado', 'Cerrado')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
                    <div class="space-y-3">
                        <button wire:click="showCreateTicket" class="w-full flex items-center p-3 text-left bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="text-green-700 dark:text-green-300">Crear Nuevo Ticket</span>
                        </button>
                        <button wire:click="showTickets" class="w-full flex items-center p-3 text-left bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="text-blue-700 dark:text-blue-300">Ver Mis Tickets</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tickets Recientes</h3>
                    <div class="space-y-3">
                        @foreach(auth()->user()->tickets()->latest()->take(3)->get() as $ticket)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->titulo, 30) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($ticket->estado === 'Abierto') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($ticket->estado === 'En Proceso') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @elseif($ticket->estado === 'Cerrado') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($ticket->estado === 'Cancelado') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                    @endif">
                                    {{ $ticket->estado }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
