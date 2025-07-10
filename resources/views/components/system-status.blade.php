<section class="py-16">
    <div class="glass-morphism p-8 rounded-2xl shadow-xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                        <div class="absolute inset-0 w-4 h-4 bg-green-500 rounded-full animate-ping opacity-75"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Estado del Sistema</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Todos los servicios operativos</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Última actualización: {{ now()->format('d/m/Y H:i') }}
                </div>
                <div class="text-xs text-gray-400 dark:text-gray-500">
                    Próxima verificación: {{ now()->addMinutes(5)->format('H:i') }}
                </div>
            </div>
        </div>
    </div>
</section>
