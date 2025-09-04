<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_satisfactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que responde
            $table->tinyInteger('rating')->comment('1-5: Muy malo a Excelente');
            $table->enum('time_satisfaction', ['muy_rapido', 'adecuado', 'regular', 'muy_lento'])
                ->comment('Satisfacción con tiempo de resolución');
            $table->text('comments')->nullable()->comment('Comentarios opcionales');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            // Evitar múltiples respuestas del mismo usuario al mismo ticket
            $table->unique(['ticket_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_satisfactions');
    }
};
