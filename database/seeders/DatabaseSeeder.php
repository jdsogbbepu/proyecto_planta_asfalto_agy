<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnidadMedida;
use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Funcionario;
use App\Models\RolePermission;
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

        // 2. Seed Role Permissions
        $permisosDisponibles = [
            'ver_dashboard',
            'gestionar_usuarios',
            'gestionar_materiales',
            'gestionar_proveedores',
            'gestionar_proyectos',
            'gestionar_funcionarios',
            'gestionar_ingresos',
            'gestionar_salidas',
            'ver_reportes',
            'gestionar_permisos',
            'ver_bitacora',
        ];

        // Administrador tiene todos los permisos
        foreach ($permisosDisponibles as $permiso) {
            RolePermission::create([
                'role' => 'administrador',
                'permission' => $permiso,
            ]);
        }

        // Operador tiene permisos de operación y ver reportes/dashboard
        $permisosOperador = [
            'ver_dashboard',
            'gestionar_materiales',
            'gestionar_proveedores',
            'gestionar_proyectos',
            'gestionar_funcionarios',
            'gestionar_ingresos',
            'gestionar_salidas',
            'ver_reportes',
        ];
        foreach ($permisosOperador as $permiso) {
            RolePermission::create([
                'role' => 'operador',
                'permission' => $permiso,
            ]);
        }

        // Visor tiene permisos únicamente de visualización de dashboard y reportes
        $permisosVisor = [
            'ver_dashboard',
            'ver_reportes',
        ];
        foreach ($permisosVisor as $permiso) {
            RolePermission::create([
                'role' => 'visor',
                'permission' => $permiso,
            ]);
        }

        // 3. Seed Units of Measure
        $kg = UnidadMedida::create(['nombre' => 'Kilogramo', 'abreviacion' => 'kg']);
        $lt = UnidadMedida::create(['nombre' => 'Litro', 'abreviacion' => 'L']);
        $m3 = UnidadMedida::create(['nombre' => 'Metro cúbico', 'abreviacion' => 'm3']);
        $t = UnidadMedida::create(['nombre' => 'Tonelada', 'abreviacion' => 't']);

        // 4. Seed Materials
        Material::create([
            'codigo_interno' => 'CA-6070',
            'nombre' => 'Cemento Asfáltico PEN 60/70',
            'descripcion' => 'Cemento asfáltico convencional de penetración para mezclas asfálticas.',
            'id_medida' => $kg->id,
            'stock_minimo' => 15000.00
        ]);

        Material::create([
            'codigo_interno' => 'EM-CSS1H',
            'nombre' => 'Emulsión Asfáltica CSS-1h',
            'descripcion' => 'Emulsión asfáltica catiónica de rotura lenta para riegos de liga.',
            'id_medida' => $lt->id,
            'stock_minimo' => 10000.00
        ]);

        Material::create([
            'codigo_interno' => 'GR-34',
            'nombre' => 'Grava Triturada 3/4"',
            'descripcion' => 'Agregado grueso para dosificación de mezcla asfáltica en caliente.',
            'id_medida' => $kg->id,
            'stock_minimo' => 25000.00
        ]);

        Material::create([
            'codigo_interno' => 'AR-RIO',
            'nombre' => 'Arena Limpia de Río',
            'descripcion' => 'Agregado fino tamizado libre de limos y arcillas.',
            'id_medida' => $kg->id,
            'stock_minimo' => 20000.00
        ]);

        // 5. Seed Suppliers
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

        // 6. Seed Projects / Public Works
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

        // 7. Seed Officers
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
