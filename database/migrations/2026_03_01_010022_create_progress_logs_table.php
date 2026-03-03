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
        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->enum('activity_type', ['rutina', 'juego']);
            $table->unsignedBigInteger('reference_id');
            $table->enum('result', ['completado', 'incompleto', 'excelente'])->default('completado');
            $table->integer('score')->default(0);
            $table->integer('attempts')->default(1);
            $table->integer('time_spent_sec')->nullable();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_logs');
    }
};
