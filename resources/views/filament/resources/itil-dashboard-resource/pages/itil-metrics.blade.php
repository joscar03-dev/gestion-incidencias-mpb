<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Widgets principales -->
        {{-- <div class="grid grid-cols-1 gap-6">
            @foreach ($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div> --}}

        <!-- Métricas detalladas - Reorganizado profesionalmente -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Análisis de tiempo de resolución -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                {{-- <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Métricas de Tiempo de Resolución</h3>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div> --}}
                @php
                    $resolutionMetrics = $this->getViewData()['resolution_metrics'];
                @endphp
                {{-- <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Tiempo Promedio:</span>
                        <span class="font-semibold text-blue-600">{{ round($resolutionMetrics['mean_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Tiempo Mediano:</span>
                        <span class="font-semibold text-green-600">{{ round($resolutionMetrics['median_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Tiempo Mínimo:</span>
                        <span class="font-semibold text-emerald-600">{{ round($resolutionMetrics['min_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Tiempo Máximo:</span>
                        <span class="font-semibold text-red-600">{{ round($resolutionMetrics['max_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                </div> --}}
            </div>

            <!-- Análisis de carga de trabajo -->
            {{-- <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Carga de Trabajo del Equipo</h3>
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 616 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                </div>
                @php
                    $workload = $this->getViewData()['workload_analysis'];
                @endphp
                <div class="space-y-3">
                    @foreach (array_slice($workload, 0, 5) as $analyst)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $analyst['user_name'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $analyst['open_tickets'] }} abiertos •
                                    {{ $analyst['resolved_tickets'] }} resueltos •
                                    {{ $analyst['escalated_tickets'] }} escalados
                                </div>
                                <div class="text-xs text-gray-400">
                                    Total: {{ $analyst['total_tickets'] }} tickets asignados
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ $analyst['resolution_rate'] }}%</div>
                                <div class="w-24 bg-gray-200 rounded-full h-2 mb-1">
                                    <div class="{{ $analyst['resolution_rate'] >= 80 ? 'bg-green-500' : ($analyst['resolution_rate'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }} h-2 rounded-full"
                                         style="width: {{ $analyst['resolution_rate'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Escalación: {{ $analyst['escalation_rate'] }}%
                                </div>
                                @if($analyst['escalation_rate'] > 0)
                                    <div class="w-24 bg-gray-200 rounded-full h-1">
                                        <div class="{{ $analyst['escalation_rate'] >= 20 ? 'bg-red-500' : ($analyst['escalation_rate'] >= 10 ? 'bg-yellow-500' : 'bg-green-500') }} h-1 rounded-full"
                                             style="width: {{ min($analyst['escalation_rate'], 100) }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($workload) > 5)
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-500">
                            Mostrando los 5 técnicos con mayor carga de trabajo.
                            Total de técnicos activos: {{ count($workload) }}
                        </p>
                    </div>
                @endif
            </div> --}}
        </div>

        <!-- Métricas de satisfacción del usuario - Mejorado profesionalmente -->
        {{-- <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Métricas de Satisfacción del Usuario</h3>
                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            @php
                $satisfaction = $this->getViewData()['user_satisfaction'];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $satisfaction['satisfaction_score'] }}%</div>
                    <div class="text-sm font-medium text-blue-800">Puntuación General</div>
                    <div class="text-xs text-blue-600 mt-1">Satisfacción promedio</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $satisfaction['total_surveys'] }}</div>
                    <div class="text-sm font-medium text-green-800">Total Encuestas</div>
                    <div class="text-xs text-green-600 mt-1">Respuestas recopiladas</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ $satisfaction['response_rate'] }}%</div>
                    <div class="text-sm font-medium text-purple-800">Tasa Respuesta</div>
                    <div class="text-xs text-purple-600 mt-1">Participación del usuario</div>
                </div>
                <div class="text-center p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                    <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $satisfaction['net_promoter_score'] }}</div>
                    <div class="text-sm font-medium text-indigo-800">Net Promoter Score</div>
                    <div class="text-xs text-indigo-600 mt-1">Recomendación del servicio</div>
                </div>
            </div>
        </div> --}}
    </div>
</x-filament-panels::page>
