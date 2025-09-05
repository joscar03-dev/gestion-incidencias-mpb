<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketSatisfaction;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;

class SatisfactionSampleSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener tickets cerrados y usuarios
        $tickets = Ticket::whereIn('estado', ['Cerrado', 'Cancelado'])->take(50)->get();
        $users = User::all();

        if ($tickets->isEmpty() || $users->isEmpty()) {
            $this->command->info('No hay tickets cerrados o usuarios para crear encuestas de muestra.');
            return;
        }

        // Generar encuestas de los últimos 60 días con distribución realista
        $satisfactionData = [];

        for ($i = 0; $i < 80; $i++) {
            $ticket = $tickets->random();
            $user = $users->random();

            // Verificar que no exista ya una encuesta para este ticket y usuario
            $exists = TicketSatisfaction::where('ticket_id', $ticket->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($exists) continue;

            // Distribución realista del NPS:
            // 60% Promotores (4-5)
            // 25% Neutros (3)  
            // 15% Detractores (1-2)
            $ratingDistribution = [
                1 => 5,   // 5% Muy malo
                2 => 10,  // 10% Malo
                3 => 25,  // 25% Regular
                4 => 35,  // 35% Bueno
                5 => 25,  // 25% Excelente
            ];

            $rating = $this->getWeightedRandom($ratingDistribution);

            // Tiempo de satisfacción correlacionado con rating
            $timeSatisfaction = match ($rating) {
                5 => collect(['muy_rapido', 'adecuado'])->random(),
                4 => collect(['muy_rapido', 'adecuado', 'regular'])->random(),
                3 => collect(['adecuado', 'regular'])->random(),
                2 => collect(['regular', 'muy_lento'])->random(),
                1 => collect(['muy_lento', 'regular'])->random(),
            };

            // Comentarios basados en el rating
            $comments = $this->generateComment($rating);

            // Fecha aleatoria en los últimos 60 días
            $createdAt = Carbon::now()->subDays(rand(0, 60));

            $satisfactionData[] = [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'rating' => $rating,
                'time_satisfaction' => $timeSatisfaction,
                'comments' => $comments,
                'submitted_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        // Insertar en lotes para mejor performance
        if (!empty($satisfactionData)) {
            TicketSatisfaction::insert($satisfactionData);
            $this->command->info('Se crearon ' . count($satisfactionData) . ' encuestas de satisfacción de muestra.');
        } else {
            $this->command->info('No se pudieron crear nuevas encuestas (posibles duplicados).');
        }
    }

    private function getWeightedRandom($weights)
    {
        $total = array_sum($weights);
        $random = rand(1, $total);

        $cumulative = 0;
        foreach ($weights as $value => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $value;
            }
        }

        return array_key_first($weights);
    }

    private function generateComment($rating)
    {
        $comments = [
            5 => [
                'Excelente servicio, muy rápido y profesional.',
                'Resolvieron mi problema inmediatamente. ¡Gracias!',
                'Servicio de primera calidad, altamente recomendado.',
                'Muy satisfecho con la atención recibida.',
                'Perfecto, no puedo pedir más.',
            ],
            4 => [
                'Buen servicio, resolvieron el problema.',
                'Estoy satisfecho con la solución.',
                'Buena atención, aunque tardaron un poco.',
                'Servicio eficiente y profesional.',
                'Todo correcto, gracias por la ayuda.',
            ],
            3 => [
                'El servicio fue regular, nada excepcional.',
                'Solucionaron el problema pero tardaron.',
                'Servicio aceptable.',
                'Está bien, pero puede mejorar.',
                'Cumplieron con lo esperado.',
            ],
            2 => [
                'El servicio no fue lo que esperaba.',
                'Tardaron mucho en resolver el problema.',
                'La atención podría ser mejor.',
                'No estoy muy satisfecho con el resultado.',
                'Tuvieron que contactarme varias veces.',
            ],
            1 => [
                'Muy mal servicio, tardan demasiado.',
                'No resolvieron mi problema adecuadamente.',
                'Servicio deficiente, muy insatisfecho.',
                'Tuve que llamar varias veces sin respuesta.',
                'Necesitan mejorar urgentemente.',
            ],
        ];

        $ratingComments = $comments[$rating] ?? ['Sin comentarios.'];

        // 70% probabilidad de tener comentario, 30% sin comentario
        return rand(1, 10) <= 7 ? collect($ratingComments)->random() : '';
    }
}
