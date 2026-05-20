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
        Schema::create('detalle_ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ingreso')->constrained('ingresos')->onDelete('cascade');
            $table->foreignId('id_material')->constrained('materials')->onDelete('restrict');
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('restrict');
            $table->decimal('cantidad_adquirida', 12, 2);
            $table->decimal('cantidad_actual_lote', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ingresos');
    }
};
