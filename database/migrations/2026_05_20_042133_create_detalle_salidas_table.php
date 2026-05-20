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
        Schema::create('detalle_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_salida')->constrained('salidas')->onDelete('cascade');
            $table->foreignId('id_detalle_ingreso')->constrained('detalle_ingresos')->onDelete('restrict');
            $table->decimal('cantidad_salida', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_salidas');
    }
};
