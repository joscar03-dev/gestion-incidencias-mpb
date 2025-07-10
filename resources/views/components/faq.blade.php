<section class="py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Preguntas Frecuentes
        </h2>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Encuentra respuestas rápidas a las consultas más comunes sobre nuestro sistema de soporte
        </p>
    </div>

    <div class="max-w-4xl mx-auto">
        <div x-data="{ openFaq: null }" class="space-y-4">
            <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                <button @click="openFaq === 1 ? openFaq = null : openFaq = 1"
                        class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Cómo puedo crear una cuenta?</span>
                    <svg :class="{'rotate-180': openFaq === 1}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-6 pb-6">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            Para crear una cuenta, haz clic en "Crear Cuenta" en la parte superior de la página. Solo los empleados de la empresa pueden registrarse con su correo corporativo.
                        </p>
                    </div>
                </div>
            </div>

            <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                <button @click="openFaq === 2 ? openFaq = null : openFaq = 2"
                        class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Dónde puedo ver mis tickets?</span>
                    <svg :class="{'rotate-180': openFaq === 2}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-6 pb-6">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            Una vez autenticado, puedes ver todos tus tickets haciendo clic en "Ver Mis Tickets" o visitando el panel de control para gestionar y dar seguimiento a tus solicitudes.
                        </p>
                    </div>
                </div>
            </div>

            <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                <button @click="openFaq === 3 ? openFaq = null : openFaq = 3"
                        class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Cuánto tiempo tarda la respuesta?</span>
                    <svg :class="{'rotate-180': openFaq === 3}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-6 pb-6">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            Los tiempos de respuesta varían según el tipo de solicitud: Soporte Técnico (30 min), Incidencias Generales (1 hora), Servicios Administrativos (2 horas). Los tickets urgentes son priorizados.
                        </p>
                    </div>
                </div>
            </div>

            <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                <button @click="openFaq === 4 ? openFaq = null : openFaq = 4"
                        class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Cómo puedo adjuntar archivos?</span>
                    <svg :class="{'rotate-180': openFaq === 4}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-6 pb-6">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            Para adjuntar archivos como capturas de pantalla o documentos, utiliza el "formulario completo" en lugar del reporte rápido. Allí encontrarás la opción para subir archivos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
