<div x-data="{ highlighted: false }" 
     x-init="
        $wire.on('scroll-to-survey', (data) => {
            if (data.ticketId == {{ $ticket->id }}) {
                $el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                highlighted = true;
                setTimeout(() => highlighted = false, 3000);
            }
        })
     "
     :class="highlighted ? 'ring-4 ring-blue-300 ring-opacity-50' : ''">
    
    @if($showSurvey && !$submitted)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-4 shadow-sm transition-all duration-300"
             :class="highlighted ? 'bg-blue-100 border-blue-400' : ''">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">
                        📋 Encuesta de Satisfacción - Ticket #{{ $ticket->id }} - {{ $ticket->titulo }}
                    </h3>
                    <p class="text-sm text-blue-600 mb-4">
                        Su ticket ha sido resuelto. Por favor, ayúdenos a mejorar calificando nuestro servicio.
                    </p>
                </div>
                <button wire:click="closeSurvey" class="text-blue-400 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="submitSurvey" class="space-y-6">
                <!-- Pregunta 1: Rating general -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        1. ¿Cómo califica el servicio recibido? *
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            5 => '😄 Excelente',
                            4 => '😊 Bueno', 
                            3 => '😐 Regular',
                            2 => '😞 Malo',
                            1 => '😡 Muy malo'
                        ] as $value => $label)
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model="rating" value="{{ $value }}" 
                                       class="sr-only peer">
                                <div class="px-4 py-2 border-2 rounded-lg transition-all duration-200
                                           peer-checked:border-blue-500 peer-checked:bg-blue-50 
                                           hover:border-blue-300 hover:bg-blue-25
                                           {{ $rating == $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <span class="text-sm font-medium">{{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('rating') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Pregunta 2: Tiempo de resolución -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        2. ¿El tiempo de resolución fue adecuado? *
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            'muy_rapido' => '⚡ Muy rápido',
                            'adecuado' => '✅ Adecuado',
                            'regular' => '🕐 Regular',
                            'muy_lento' => '🐌 Muy lento'
                        ] as $value => $label)
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model="timeSatisfaction" value="{{ $value }}" 
                                       class="sr-only peer">
                                <div class="px-4 py-2 border-2 rounded-lg transition-all duration-200
                                           peer-checked:border-green-500 peer-checked:bg-green-50 
                                           hover:border-green-300 hover:bg-green-25
                                           {{ $timeSatisfaction == $value ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                    <span class="text-sm font-medium">{{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('timeSatisfaction') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Pregunta 3: Comentarios -->
                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                        3. Comentarios adicionales (opcional)
                    </label>
                    <textarea wire:model="comments" id="comments" rows="3" 
                              placeholder="¿Algo que podríamos mejorar? ¿Qué le gustó más del servicio?"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                              maxlength="500"></textarea>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ strlen($comments) }}/500 caracteres
                    </div>
                    @error('comments') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" wire:click="closeSurvey" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Ahora no
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        📤 Enviar Encuesta
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($submitted)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">
                        ¡Gracias por su feedback!
                    </h3>
                    <div class="mt-1 text-sm text-green-700">
                        Su opinión nos ayuda a mejorar nuestro servicio.
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
</div>
