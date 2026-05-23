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
        Schema::table('detalle_ingresos', function (Blueprint $table) {
            $table->string('nro_registro', 20)->nullable()->after('id');
            $table->date('fecha_lote')->nullable()->after('nro_registro');
            $table->foreignId('id_proveedor')->nullable()->constrained('proveedors')->onDelete('restrict')->after('id_proyecto');
            $table->string('acciones_planificadas', 255)->nullable()->after('cantidad_actual_lote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_ingresos', function (Blueprint $table) {
            $table->dropColumn(['nro_registro', 'fecha_lote', 'id_proveedor', 'acciones_planificadas']);
        });
    }
};