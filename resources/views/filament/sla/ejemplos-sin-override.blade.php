<div class="space-y-4">
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-sm font-medium text-yellow-800">Override Desactivado</h3>
        </div>
        <p class="mt-2 text-sm text-yellow-700">
            Este SLA tiene el override desactivado, por lo que todos los tickets usarán los mismos tiempos sin importar la prioridad o tipo.
        </p>
    </div>

    <div class="bg-gray-50 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-900 mb-3">Tiempos Fijos para Todos los Tickets</h4>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded border p-3">
                <div class="text-sm text-gray-500">Tiempo de Respuesta</div>
                <div class="text-2xl font-bold text-blue-600">{{ $sla->tiempo_respuesta }} min</div>
                <div class="text-sm text-gray-500">({{ round($sla->tiempo_respuesta / 60, 1) }} horas)</div>
            </div>
            <div class="bg-white rounded border p-3">
                <div class="text-sm text-gray-500">Tiempo de Resolución</div>
                <div class="text-2xl font-bold text-green-600">{{ $sla->tiempo_resolucion }} min</div>
                <div class="text-sm text-gray-500">({{ round($sla->tiempo_resolucion / 60, 1) }} horas)</div>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-700">
            <strong>Nota:</strong> Para habilitar diferentes tiempos según prioridad y tipo de ticket, active la opción "Permitir Override por Prioridad" en la configuración del SLA.
        </p>
    </div>
</div>
