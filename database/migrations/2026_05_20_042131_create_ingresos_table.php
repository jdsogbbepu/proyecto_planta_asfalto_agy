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
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->string('nro_ticket', 50)->nullable();
            $table->string('odc', 50)->nullable();
            $table->foreignId('id_proveedor')->nullable()->constrained('proveedors')->onDelete('restrict');
            $table->foreignId('id_usuario')->constrained('users')->onDelete('restrict');
            $table->date('fecha_adquirida');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
