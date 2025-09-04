<div wire:poll.30s="refreshSurveys">
    @if($ticketsPendingSurvey->count() > 0)
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-1 border border-blue-200 dark:border-blue-800">
                <div class="bg-white dark:bg-gray-800 rounded-md p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📋 Encuestas de Satisfacción Pendientes</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ayúdanos a mejorar calificando el servicio recibido</p>
                        </div>
                        <div class="ml-auto flex items-center space-x-2">
                            <button wire:click="refreshSurveys" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                    title="Actualizar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                {{ $ticketsPendingSurvey->count() }} pendiente{{ $ticketsPendingSurvey->count() !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($ticketsPendingSurvey as $ticket)
                            @livewire('ticket-satisfaction-survey', ['ticket' => $ticket], key('survey-' . $ticket->id))
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
