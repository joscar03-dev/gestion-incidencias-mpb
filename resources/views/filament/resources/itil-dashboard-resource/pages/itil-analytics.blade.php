<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Análisis de tendencias -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Análisis de Tendencias (Últimos 30 días)</h3>
            @php
                $trends = $this->getViewData()['trend_analysis'];
            @endphp
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <p class="text-gray-600">Gráfico de tendencias interactivo</p>
                    <p class="text-sm text-gray-500">{{ count($trends) }} días de datos disponibles</p>
                </div>
            </div>
        </div>

        <!-- Distribución por categorías -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Categorías ITIL</h3>
                @php
                    $categories = $this->getViewData()['category_distribution'];
                @endphp
                <div class="space-y-3">
                    @foreach ($categories as $key => $category)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    @php
                                        $total = array_sum(array_column($categories, 'count'));
                                        $percentage = $total > 0 ? ($category['count'] / $total) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="ml-4 text-sm font-medium text-gray-900">
                                {{ $category['count'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Métricas de disponibilidad del servicio -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Disponibilidad del Servicio</h3>
                @php
                    $availability = $this->getViewData()['service_availability'];
                @endphp
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-green-600">{{ $availability['availability_percentage'] }}%</div>
                        <div class="text-sm text-gray-600">Disponibilidad Total</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $availability['uptime_hours'] }}h</div>
                            <div class="text-xs text-gray-600">Tiempo Activo</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ $availability['downtime_hours'] }}h</div>
                            <div class="text-xs text-gray-600">Tiempo Inactivo</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-center pt-4 border-t">
                        <div>
                            <div class="text-lg font-bold text-indigo-600">{{ round($availability['mttr'], 2) }}h</div>
                            <div class="text-xs text-gray-600">MTTR (Tiempo Medio Reparación)</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-purple-600">{{ $availability['mtbf'] }}h</div>
                            <div class="text-xs text-gray-600">MTBF (Tiempo Medio Entre Fallos)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis de carga de trabajo detallado -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Análisis Detallado de Carga de Trabajo</h3>
            @php
                $workload = $this->getViewData()['workload_analysis'];
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Técnico
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tickets Abiertos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Tickets
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resueltos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tasa Resolución
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($workload as $analyst)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($analyst['user_name'], 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $analyst['user_name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $analyst['open_tickets'] > 10 ? 'bg-red-100 text-red-800' : 
                                           ($analyst['open_tickets'] > 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $analyst['open_tickets'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $analyst['total_tickets'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $analyst['resolved_tickets'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $analyst['resolution_rate'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $analyst['resolution_rate'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($analyst['resolution_rate'] >= 80)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Excelente
                                        </span>
                                    @elseif ($analyst['resolution_rate'] >= 60)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Bueno
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Necesita Mejora
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Métricas de satisfacción del usuario -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Análisis de Satisfacción del Usuario</h3>
            @php
                $satisfaction = $this->getViewData()['user_satisfaction'];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $satisfaction['satisfaction_score'] }}%</div>
                    <div class="text-sm font-medium text-blue-800">Satisfacción General</div>
                    <div class="mt-2 w-full bg-blue-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $satisfaction['satisfaction_score'] }}%"></div>
                    </div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $satisfaction['total_surveys'] }}</div>
                    <div class="text-sm font-medium text-green-800">Encuestas Totales</div>
                    <div class="text-xs text-green-600 mt-1">Últimos 30 días</div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ $satisfaction['response_rate'] }}%</div>
                    <div class="text-sm font-medium text-purple-800">Tasa de Respuesta</div>
                    <div class="mt-2 w-full bg-purple-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $satisfaction['response_rate'] }}%"></div>
                    </div>
                </div>

                <div class="text-center p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                    <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $satisfaction['net_promoter_score'] }}</div>
                    <div class="text-sm font-medium text-indigo-800">Net Promoter Score</div>
                    <div class="text-xs text-indigo-600 mt-1">Escala 1-10</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
