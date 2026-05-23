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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_funcionario')->constrained('funcionarios')->onDelete('restrict');
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('restrict');
            $table->foreignId('id_usuario')->constrained('users')->onDelete('restrict');
            $table->string('odc', 50)->nullable();
            $table->string('uso', 255)->nullable();
            $table->date('fecha_salida');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
