<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnidadMedida;
use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Funcionario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed default Users
        User::create([
            'name' => 'Administrador de Planta',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'administrador',
            'active' => true,
        ]);

        User::create([
            'name' => 'Operador de Báscula',
            'username' => 'operador',
            'password' => Hash::make('operador123'),
            'role' => 'operador',
            'active' => true,
        ]);

        User::create([
            'name' => 'Auditor Externo',
            'username' => 'visor',
            'password' => Hash::make('visor123'),
            'role' => 'visor',
            'active' => true,
        ]);

        // 2. Seed Units of Measure
        $kg = UnidadMedida::create(['nombre' => 'Kilogramo', 'abreviacion' => 'kg']);
        $lt = UnidadMedida::create(['nombre' => 'Litro', 'abreviacion' => 'L']);
        $m3 = UnidadMedida::create(['nombre' => 'Metro cúbico', 'abreviacion' => 'm3']);
        $t = UnidadMedida::create(['nombre' => 'Tonelada', 'abreviacion' => 't']);

        // 3. Seed Materials
        Material::create([
            'nombre' => 'Cemento Asfáltico PEN 60/70',
            'descripcion' => 'Cemento asfáltico convencional de penetración para mezclas asfálticas.',
            'id_medida' => $kg->id,
            'stock_minimo' => 15000.00
        ]);

        Material::create([
            'nombre' => 'Emulsión Asfáltica CSS-1h',
            'descripcion' => 'Emulsión asfáltica catiónica de rotura lenta para riegos de liga.',
            'id_medida' => $lt->id,
            'stock_minimo' => 10000.00
        ]);

        Material::create([
            'nombre' => 'Grava Triturada 3/4"',
            'descripcion' => 'Agregado grueso para dosificación de mezcla asfáltica en caliente.',
            'id_medida' => $kg->id,
            'stock_minimo' => 25000.00
        ]);

        Material::create([
            'nombre' => 'Arena Limpia de Río',
            'descripcion' => 'Agregado fino tamizado libre de limos y arcillas.',
            'id_medida' => $kg->id,
            'stock_minimo' => 20000.00
        ]);

        // 4. Seed Suppliers
        Proveedor::create([
            'razon_social' => 'YPFB Refinación S.A.',
            'nit' => '1020304021',
            'telefono' => '22812400',
            'direccion' => 'Zona Senkata, Carretera a Oruro, El Alto'
        ]);

        Proveedor::create([
            'razon_social' => 'Agregados del Altiplano S.R.L.',
            'nit' => '4050607024',
            'telefono' => '71520304',
            'direccion' => 'Canteras de Achocalla, La Paz'
        ]);

        // 5. Seed Projects / Public Works
        Proyecto::create([
            'nombre' => 'Doble Vía Viacha - Pavimento Flexible Lote A',
            'ubicacion' => 'Distrito 8, El Alto',
            'encargado' => 'Ing. Carlos Flores Mamani',
            'fecha_inicio' => '2026-02-01',
            'fecha_fin' => '2026-10-31',
            'estado' => 'activo'
        ]);

        Proyecto::create([
            'nombre' => 'Avenida Juan Pablo II - Mantenimiento Vial',
            'ubicacion' => 'Distrito 6, Av. Juan Pablo II, El Alto',
            'encargado' => 'Ing. Sonia Quispe Quisbert',
            'fecha_inicio' => '2026-05-10',
            'fecha_fin' => '2026-07-15',
            'estado' => 'activo'
        ]);

        Proyecto::create([
            'nombre' => 'Viaducto San José - Distribuidor',
            'ubicacion' => 'Distrito 3, Plaza San José, El Alto',
            'encargado' => 'Ing. Wilfredo Choque',
            'fecha_inicio' => '2025-09-01',
            'fecha_fin' => '2026-04-30',
            'estado' => 'finalizado'
        ]);

        // 6. Seed Officers
        Funcionario::create([
            'nombre' => 'Ing. Milton Quispe Choque',
            'cargo' => 'Supervisor de Obra Municipal',
            'area' => 'Dirección de Infraestructura Pública',
            'activo' => true
        ]);

        Funcionario::create([
            'nombre' => 'Ing. Nancy Alanoca Vargas',
            'cargo' => 'Residente de Fiscalización Vial',
            'area' => 'Unidad de Mantenimiento de Vías',
            'activo' => true
        ]);
    }
}
