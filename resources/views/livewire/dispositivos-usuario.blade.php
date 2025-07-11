@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Gestión de Dispositivos
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Administra tus dispositivos, solicita nuevos equipos y reporta problemas.
            </p>
        </div>

        <!-- Mensajes de feedback -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">¡Éxito!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Información</p>
                        <p class="text-sm">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-6">
    <!-- Header con navegación por pestañas -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex px-6">
                <button
                    wire:click="$set('activeTab', 'mis-dispositivos')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'mis-dispositivos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                >
                    Mis Dispositivos ({{ $misDispositivos->count() }})
                </button>
                <button
                    wire:click="$set('activeTab', 'solicitar-dispositivo')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'solicitar-dispositivo' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                >
                    Solicitar Requerimiento
                </button>
                <button
                    wire:click="$set('activeTab', 'mis-requerimientos')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'mis-requerimientos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                >
                    Mis Requerimientos
                </button>
                <button
                    wire:click="$set('activeTab', 'mis-reportes')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 {{ $activeTab === 'mis-reportes' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                >
                    Mis Reportes
                </button>
                <button
                    wire:click="$set('activeTab', 'historial')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'historial' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                >
                    Historial
                </button>
            </nav>
        </div>
    </div>

    <!-- Contenido según pestaña activa -->
    @if($activeTab === 'mis-dispositivos')
        <!-- Mis Dispositivos -->
        <div class="space-y-4">
            @if($misDispositivos->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($misDispositivos as $dispositivo)
                        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            <!-- Imagen del dispositivo -->
                            <div class="h-48 bg-gray-50 relative overflow-hidden">
                                @if($dispositivo->imagen)
                                    <img
                                        src="{{ asset('storage/' . $dispositivo->imagen) }}"
                                        alt="{{ $dispositivo->nombre }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
                                        <div class="text-center">
                                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">Sin imagen</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Badge de estado superpuesto -->
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm
                                        {{ $dispositivo->estado === 'Asignado' ? 'bg-green-100 text-green-800 border border-green-200' :
                                           ($dispositivo->estado === 'Reparación' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-gray-100 text-gray-800 border border-gray-200') }}">
                                        {{ $dispositivo->estado }}
                                    </span>
                                </div>
                            </div>

                            <!-- Contenido de la tarjeta -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $dispositivo->nombre }}</h3>
                                        <p class="text-sm text-indigo-600 font-medium">{{ $dispositivo->categoria_dispositivo->nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-1 font-mono">S/N: {{ $dispositivo->numero_serie }}</p>
                                    </div>
                                </div>

                                @if($dispositivo->descripcion)
                                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ Str::limit($dispositivo->descripcion, 100) }}</p>
                                @endif

                                <!-- Información adicional -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span>{{ $dispositivo->area->nombre }}</span>
                                    </div>
                                    @if($dispositivo->fecha_compra)
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>Compra: {{ \Carbon\Carbon::parse($dispositivo->fecha_compra)->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Acciones -->
                                <div class="flex justify-end">
                                    @if($dispositivo->estado !== 'Reparación')
                                        <button
                                            wire:click="abrirReporteModal({{ $dispositivo->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            title="Reportar problema"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            Reportar Problema
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-orange-700 bg-orange-100 rounded-md">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            En Reparación
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <x-heroicon-o-device-phone-mobile class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes dispositivos asignados</h3>
                    <p class="text-gray-500 mb-4">Solicita un dispositivo haciendo clic en la pestaña "Solicitar Dispositivo"</p>
                    <button
                        wire:click="setActiveTab('solicitar-dispositivo')"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Solicitar Dispositivo
                    </button>
                </div>
            @endif
        </div>

    @elseif($activeTab === 'solicitar-dispositivo')
        <!-- Solicitar Requerimiento -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Enviar Requerimiento de Dispositivo</h3>
                <p class="text-sm text-gray-600">Complete el formulario para solicitar un dispositivo. Su requerimiento será revisado por el administrador.</p>
            </div>

            <form wire:submit="enviarRequerimiento" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="categoria_solicitada" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría de Dispositivo *
                        </label>
                        <select
                            wire:model="categoria_solicitada"
                            id="categoria_solicitada"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $id => $nombre)
                                <option value="{{ $id }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_solicitada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="prioridad_requerimiento" class="block text-sm font-medium text-gray-700 mb-2">
                            Prioridad *
                        </label>
                        <select
                            wire:model="prioridad_requerimiento"
                            id="prioridad_requerimiento"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="Baja">Baja</option>
                            <option value="Media">Media</option>
                            <option value="Alta">Alta</option>
                        </select>
                        @error('prioridad_requerimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="justificacion_requerimiento" class="block text-sm font-medium text-gray-700 mb-2">
                        Justificación del Requerimiento *
                    </label>
                    <textarea
                        wire:model="justificacion_requerimiento"
                        id="justificacion_requerimiento"
                        rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Explique por qué necesita este dispositivo y cómo lo utilizará..."
                    ></textarea>
                    @error('justificacion_requerimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="documento_requerimiento" class="block text-sm font-medium text-gray-700 mb-2">
                        Documento de Respaldo (Opcional)
                    </label>
                    <input
                        type="file"
                        wire:model="documento_requerimiento"
                        id="documento_requerimiento"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Tamaño máximo: 2MB
                    </p>
                    @error('documento_requerimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <x-heroicon-o-paper-airplane class="w-4 h-4 mr-2" />
                        Enviar Requerimiento
                    </button>
                </div>
            </form>
        </div>
            </form>
        </div>

    @elseif($activeTab === 'mis-requerimientos')
        <!-- Mis Requerimientos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($misRequerimientos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justificación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($misRequerimientos as $requerimiento)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $requerimiento->categoria_dispositivo->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($requerimiento->prioridad === 'Alta') bg-red-100 text-red-800
                                            @elseif($requerimiento->prioridad === 'Media') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $requerimiento->prioridad }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($requerimiento->estado === 'Aprobado') bg-green-100 text-green-800
                                            @elseif($requerimiento->estado === 'Rechazado') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $requerimiento->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $requerimiento->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs">
                                            {{ Str::limit($requerimiento->justificacion, 80) }}
                                        </div>
                                        @if($requerimiento->observaciones_admin)
                                            <div class="mt-1 text-xs text-gray-500">
                                                <strong>Admin:</strong> {{ $requerimiento->observaciones_admin }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($requerimiento->documento_requerimiento)
                                            <a href="{{ Storage::url($requerimiento->documento_requerimiento) }}"
                                               target="_blank"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <x-heroicon-o-document-arrow-down class="w-4 h-4 inline" />
                                                Ver documento
                                            </a>
                                        @else
                                            <span class="text-gray-400">Sin documento</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center">
                    <x-heroicon-o-document-text class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes requerimientos</h3>
                    <p class="text-gray-500">Aún no has realizado ningún requerimiento de dispositivo</p>
                </div>
            @endif
        </div>

    @elseif($activeTab === 'mis-reportes')
        <!-- Mis Reportes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($misTicketsDispositivos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispositivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($misTicketsDispositivos as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ $ticket->id }} - {{ $ticket->titulo }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($ticket->descripcion, 60) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $ticket->dispositivo->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $ticket->estado === 'Abierto' ? 'bg-red-100 text-red-800' :
                                               ($ticket->estado === 'En Progreso' ? 'bg-yellow-100 text-yellow-800' :
                                               ($ticket->estado === 'Cerrado' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $ticket->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $misTicketsDispositivos->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <x-heroicon-o-exclamation-triangle class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes reportes</h3>
                    <p class="text-gray-500">No has reportado problemas con dispositivos</p>
                </div>
            @endif
        </div>

    @elseif($activeTab === 'historial')
        <!-- Historial de Asignaciones -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($historialAsignaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispositivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periodo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($historialAsignaciones as $asignacion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $asignacion->dispositivo->nombre }}</div>
                                            <div class="text-sm text-gray-500">S/N: {{ $asignacion->dispositivo->numero_serie }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $asignacion->dispositivo->categoria_dispositivo->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $asignacion->fecha_asignacion->format('d/m/Y') }}</div>
                                        @if($asignacion->fecha_desasignacion)
                                            <div>{{ $asignacion->fecha_desasignacion->format('d/m/Y') }}</div>
                                        @else
                                            <div class="text-green-600">Actual</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asignacion->duracion_asignacion }} días
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asignacion->esta_activa)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activa
                                            </span>
                                            @if(!$asignacion->confirmado)
                                                <div class="mt-2">
                                                    <button
                                                        wire:click="confirmarRecepcion({{ $asignacion->id }})"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                                        onclick="return confirm('¿Confirmas que has recibido este dispositivo?')"
                                                    >
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Confirmar recepción
                                                    </button>
                                                </div>
                                            @else
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Confirmado {{ $asignacion->fecha_confirmacion ? $asignacion->fecha_confirmacion->format('d/m/Y') : '' }}
                                                    </span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Finalizada
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $historialAsignaciones->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <x-heroicon-o-clock class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay historial</h3>
                    <p class="text-gray-500">No tienes historial de asignaciones de dispositivos</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Modal para reportar problema -->
    @if($showReporteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showReporteModal') }" x-show="show" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="show"></div>
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block w-full max-w-lg my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-lg" x-show="show">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reportar Problema con Dispositivo</h3>
                </div>

                <form wire:submit="reportarProblema" class="p-6 space-y-4">
                    <div>
                        <label for="tipo_problema" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Problema</label>
                        <select wire:model="tipo_problema" id="tipo_problema" class="w-full rounded-md border-gray-300">
                            <option value="">Seleccione el tipo</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                            <option value="Conectividad">Conectividad</option>
                            <option value="Rendimiento">Rendimiento</option>
                            <option value="Otro">Otro</option>
                        </select>
                        @error('tipo_problema') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="descripcion_problema" class="block text-sm font-medium text-gray-700 mb-2">Descripción del Problema</label>
                        <textarea wire:model="descripcion_problema" id="descripcion_problema" rows="4"
                                  class="w-full rounded-md border-gray-300"
                                  placeholder="Describe detalladamente el problema..."></textarea>
                        @error('descripcion_problema') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="requiere_reemplazo" class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Requiere reemplazo temporal</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="$set('showReporteModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Reportar Problema
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

        </div>
    </div>
</div>
