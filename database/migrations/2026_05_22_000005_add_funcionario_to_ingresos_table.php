<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->foreignId('id_funcionario')->nullable()->after('id_usuario')->constrained('funcionarios')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('ingresos', function (Blueprint $table) {
            $table->dropForeign(['id_funcionario']);
            $table->dropColumn('id_funcionario');
        });
    }
};