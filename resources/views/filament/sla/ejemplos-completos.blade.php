<div class="space-y-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-sm font-medium text-green-800">Sistema Híbrido Activado</h3>
        </div>
        <p class="mt-2 text-sm text-green-700">
            Los tiempos de SLA se calculan dinámicamente según la prioridad y tipo de ticket.
        </p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-2">Tiempos Base</h4>
            <div class="space-y-1 text-sm">
                <div>Respuesta: <strong>{{ $sla->tiempo_respuesta }} min</strong></div>
                <div>Resolución: <strong>{{ $sla->tiempo_resolucion }} min</strong></div>
            </div>
        </div>
        <div class="bg-purple-50 rounded-lg p-4">
            <h4 class="font-semibold text-purple-900 mb-2">Fórmula de Cálculo</h4>
            <div class="text-sm text-purple-700">
                <strong>Tiempo Final = Base × Factor Prioridad × Factor Tipo</strong>
            </div>
        </div>
    </div>

    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tipo</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Prioridad</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">Factor</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">Respuesta</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">Resolución</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($ejemplos as $ejemplo)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($ejemplo['tipo'] === 'Incidente') bg-red-100 text-red-800
                            @elseif($ejemplo['tipo'] === 'General') bg-blue-100 text-blue-800
                            @elseif($ejemplo['tipo'] === 'Requerimiento') bg-yellow-100 text-yellow-800
                            @else bg-purple-100 text-purple-800
                            @endif">
                            {{ $ejemplo['tipo'] }}
                        </span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($ejemplo['prioridad'] === 'Critico') bg-red-100 text-red-800
                            @elseif($ejemplo['prioridad'] === 'Alto') bg-orange-100 text-orange-800
                            @elseif($ejemplo['prioridad'] === 'Medio') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ $ejemplo['prioridad'] }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center text-sm font-mono">{{ $ejemplo['factor_combinado'] }}</td>
                    <td class="px-3 py-2 text-center">
                        <div class="text-sm font-medium text-gray-900">{{ $ejemplo['respuesta_horas'] }}h</div>
                        <div class="text-xs text-gray-500">{{ $ejemplo['respuesta_min'] }} min</div>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <div class="text-sm font-medium text-gray-900">{{ $ejemplo['resolucion_horas'] }}h</div>
                        <div class="text-xs text-gray-500">{{ $ejemplo['resolucion_min'] }} min</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 mb-3">Factores de Multiplicación</h4>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-2">Por Prioridad:</h5>
                <ul class="text-sm space-y-1">
                    <li>🔴 <strong>Crítica:</strong> 0.2 (20% del tiempo)</li>
                    <li>🟠 <strong>Alta:</strong> 0.5 (50% del tiempo)</li>
                    <li>🟡 <strong>Media:</strong> 1.0 (100% del tiempo)</li>
                    <li>🟢 <strong>Baja:</strong> 1.5 (150% del tiempo)</li>
                </ul>
            </div>
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-2">Por Tipo:</h5>
                <ul class="text-sm space-y-1">
                    <li>🚨 <strong>Incidente:</strong> 0.6 (60% del tiempo)</li>
                    <li>💬 <strong>General:</strong> 0.8 (80% del tiempo)</li>
                    <li>📋 <strong>Requerimiento:</strong> 1.2 (120% del tiempo)</li>
                    <li>⚙️ <strong>Cambio:</strong> 1.5 (150% del tiempo)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
