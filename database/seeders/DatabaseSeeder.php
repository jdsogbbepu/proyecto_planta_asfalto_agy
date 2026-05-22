<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnidadMedida;
use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Funcionario;
use App\Models\RolePermission;
use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\Models\Salida;
use App\Models\DetalleSalida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================================
        // 1. USUARIOS Y PERMISOS
        // ============================================================

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

        User::create([
            'name' => 'Jefe de Almacén',
            'username' => 'jefe',
            'password' => Hash::make('jefe123'),
            'role' => 'operador',
            'active' => true,
        ]);

        // Permisos disponibles
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

        foreach ($permisosDisponibles as $permiso) {
            RolePermission::create(['role' => 'administrador', 'permission' => $permiso]);
        }

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
            RolePermission::create(['role' => 'operador', 'permission' => $permiso]);
        }

        $permisosVisor = [
            'ver_dashboard',
            'ver_reportes',
        ];
        foreach ($permisosVisor as $permiso) {
            RolePermission::create(['role' => 'visor', 'permission' => $permiso]);
        }

        // ============================================================
        // 2. UNIDADES DE MEDIDA
        // ============================================================

        $kg    = UnidadMedida::create(['nombre' => 'Kilogramo',      'abreviacion' => 'kg']);
        $lt    = UnidadMedida::create(['nombre' => 'Litro',            'abreviacion' => 'L']);
        $m3    = UnidadMedida::create(['nombre' => 'Metro cúbico',     'abreviacion' => 'm3']);
        $t     = UnidadMedida::create(['nombre' => 'Tonelada',         'abreviacion' => 't']);
        $gl    = UnidadMedida::create(['nombre' => 'Galón',            'abreviacion' => 'gal']);
        $sac   = UnidadMedida::create(['nombre' => 'Saco',             'abreviacion' => 'saco']);
        $mm    = UnidadMedida::create(['nombre' => 'Milímetro',        'abreviacion' => 'mm']);

        // ============================================================
        // 3. PROVEEDORES
        // ============================================================

        $prov1 = Proveedor::create([
            'razon_social' => 'YPFB Refinación S.A.',
            'nit' => '1020304021',
            'telefono' => '22812400',
            'direccion' => 'Zona Senkata, Carretera a Oruro, El Alto',
        ]);

        $prov2 = Proveedor::create([
            'razon_social' => 'Agregados del Altiplano S.R.L.',
            'nit' => '4050607024',
            'telefono' => '71520304',
            'direccion' => 'Canteras de Achocalla, La Paz',
        ]);

        $prov3 = Proveedor::create([
            'razon_social' => 'Importadora de Asfaltos Bolívar',
            'nit' => '1021765438',
            'telefono' => '24567890',
            'direccion' => 'Zona Mercado, Av. 6 de Marzo, El Alto',
        ]);

        $prov4 = Proveedor::create([
            'razon_social' => 'Hormigones y Pavimentos S.A.',
            'nit' => '3021890231',
            'telefono' => '22118900',
            'direccion' => 'Zona Kilómetro 7, Carretera a Viacha',
        ]);

        // ============================================================
        // 4. MATERIALES
        // ============================================================

        $mat1 = Material::create([
            'codigo_interno' => 'CA-6070',
            'nombre' => 'Cemento Asfáltico PEN 60/70',
            'descripcion' => 'Cemento asfáltico convencional de penetración para mezclas asfálticas en caliente.',
            'id_medida' => $kg->id,
            'stock_minimo' => 15000.00,
        ]);

        $mat2 = Material::create([
            'codigo_interno' => 'CA-5080',
            'nombre' => 'Cemento Asfáltico PEN 50/70',
            'descripcion' => 'Cemento asfáltico de penetración media para temperaturas ambientes altas.',
            'id_medida' => $kg->id,
            'stock_minimo' => 10000.00,
        ]);

        $mat3 = Material::create([
            'codigo_interno' => 'EM-CSS1H',
            'nombre' => 'Emulsión Asfáltica CSS-1h',
            'descripcion' => 'Emulsión asfáltica catiónica de rotura lenta para riegos de liga y imprimación.',
            'id_medida' => $lt->id,
            'stock_minimo' => 10000.00,
        ]);

        $mat4 = Material::create([
            'codigo_interno' => 'EM-CRS1H',
            'nombre' => 'Emulsión Asfáltica CRS-1h',
            'descripcion' => 'Emulsión asfáltica catiónica de rotura rápida para tratamientos superficiales.',
            'id_medida' => $lt->id,
            'stock_minimo' => 8000.00,
        ]);

        $mat5 = Material::create([
            'codigo_interno' => 'GR-34',
            'nombre' => 'Grava Triturada 3/4"',
            'descripcion' => 'Agregado grueso triturado de 19 mm para dosificación de mezcla asfáltica en caliente.',
            'id_medida' => $kg->id,
            'stock_minimo' => 25000.00,
        ]);

        $mat6 = Material::create([
            'codigo_interno' => 'GR-12',
            'nombre' => 'Grava Triturada 1/2"',
            'descripcion' => 'Agregado grueso triturado de 12.5 mm para capas de rodadura.',
            'id_medida' => $kg->id,
            'stock_minimo' => 20000.00,
        ]);

        $mat7 = Material::create([
            'codigo_interno' => 'AR-RIO',
            'nombre' => 'Arena Limpia de Río',
            'descripcion' => 'Agregado fino tamizado libre de limos y arcillas, origen aluvial.',
            'id_medida' => $kg->id,
            'stock_minimo' => 20000.00,
        ]);

        $mat8 = Material::create([
            'codigo_interno' => 'FILLER-C',
            'nombre' => 'Filler Calcáreo',
            'descripcion' => 'Polvo mineral calcáreo para mejora de graduación en mezclas asfálticas.',
            'id_medida' => $kg->id,
            'stock_minimo' => 5000.00,
        ]);

        $mat9 = Material::create([
            'codigo_interno' => 'ADH-ES',
            'nombre' => 'AdhesivoBituminoso AntiSegrego',
            'descripcion' => 'Aditivo mejorar adherencia entre agregado y asfalto, anti-segregación.',
            'id_medida' => $lt->id,
            'stock_minimo' => 2000.00,
        ]);

        // ============================================================
        // 5. PROYECTOS / OBRAS MUNICIPALES
        // ============================================================

        $proy1 = Proyecto::create([
            'nombre' => 'Doble Vía Viacha - Pavimento Flexible Lote A',
            'ubicacion' => 'Distrito 8, Carretera a Viacha Km 12, El Alto',
            'encargado' => 'Ing. Carlos Flores Mamani',
            'fecha_inicio' => '2026-02-01',
            'fecha_fin' => '2026-10-31',
            'estado' => 'activo',
        ]);

        $proy2 = Proyecto::create([
            'nombre' => 'Avenida Juan Pablo II - Mantenimiento Vial',
            'ubicacion' => 'Distrito 6, Av. Juan Pablo II entre Av. 6 de Marzo y C. 1, El Alto',
            'encargado' => 'Ing. Sonia Quispe Quisbert',
            'fecha_inicio' => '2026-05-10',
            'fecha_fin' => '2026-07-15',
            'estado' => 'activo',
        ]);

        $proy3 = Proyecto::create([
            'nombre' => 'Viaducto San José - Distribuidor Vial',
            'ubicacion' => 'Distrito 3, Plaza San José, El Alto',
            'encargado' => 'Ing. Wilfredo Choque Layme',
            'fecha_inicio' => '2025-09-01',
            'fecha_fin' => '2026-04-30',
            'estado' => 'finalizado',
        ]);

        $proy4 = Proyecto::create([
            'nombre' => 'Calle 25 de Julio - Recapado Asfáltico',
            'ubicacion' => 'Distrito 12, Zona 25 de Julio, El Alto',
            'encargado' => 'Ing. René Copa Mamani',
            'fecha_inicio' => '2026-04-01',
            'fecha_fin' => '2026-08-30',
            'estado' => 'activo',
        ]);

        $proy5 = Proyecto::create([
            'nombre' => 'Acceso Aeropuerto Internacional - Mezcla SMA',
            'ubicacion' => 'Distrito 2, Acceso El Alto - La Paz, El Alto',
            'encargado' => 'Ing. Patricia Condori de Tapia',
            'fecha_inicio' => '2026-03-15',
            'fecha_fin' => '2026-09-15',
            'estado' => 'pausado',
        ]);

        // ============================================================
        // 6. FUNCIONARIOS / RECEPCIONISTAS
        // ============================================================

        $func1 = Funcionario::create([
            'nombre' => 'Ing. Milton Quispe Choque',
            'cargo' => 'Supervisor de Obra Municipal',
            'area' => 'Dirección de Infraestructura Pública',
            'activo' => true,
        ]);

        $func2 = Funcionario::create([
            'nombre' => 'Ing. Nancy Alanoca Vargas',
            'cargo' => 'Residente de Fiscalización Vial',
            'area' => 'Unidad de Mantenimiento de Vías',
            'activo' => true,
        ]);

        $func3 = Funcionario::create([
            'nombre' => 'Tec. Roberto Mamani Choque',
            'cargo' => 'Encargado de Laboratorio de Suelos y Asfaltos',
            'area' => 'Dirección de Infraestructura Pública',
            'activo' => true,
        ]);

        $func4 = Funcionario::create([
            'nombre' => 'Ing. Grover Nina Huanca',
            'cargo' => 'Residente de Obra - Doble Vía Viacha',
            'area' => 'Proyecto Doble Vía Viacha',
            'activo' => true,
        ]);

        $func5 = Funcionario::create([
            'nombre' => 'Ing. Elena Choquehua',
            'cargo' => 'Asistente de Fiscalización',
            'area' => 'Unidad de Mantenimiento de Vías',
            'activo' => false,
        ]);

        // ============================================================
        // 7. INGRESOS / LOTES (STOCK INICIAL DE PRUEBA)
        // ============================================================

        $admin = User::where('username', 'admin')->first();

        // --- Lotes para PROYECTO 1: Doble Vía Viacha (Lote A) ---
        // Ingreso 1: Cemento asfáltico PEN 60/70 - 25,000 kg
        $ing1 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0001',
            'odc' => 'OC-AL-2026-0045',
            'id_proveedor' => $prov1->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(30)->format('Y-m-d'),
            'observaciones' => 'Lote conforme a especificaciones técnicas.',
        ]);

        $lote1 = DetalleIngreso::create([
            'id_ingreso' => $ing1->id,
            'id_material' => $mat1->id,
            'id_proyecto' => $proy1->id,
            'cantidad_adquirida' => 25000.00,
            'cantidad_actual_lote' => 25000.00,
        ]);

        // Ingreso 2: Grava 3/4" - 45,000 kg
        $ing2 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0002',
            'odc' => 'OC-AL-2026-0045',
            'id_proveedor' => $prov2->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(28)->format('Y-m-d'),
            'observaciones' => 'Material de cantera Achocalla, ensayos OK.',
        ]);

        DetalleIngreso::create([
            'id_ingreso' => $ing2->id,
            'id_material' => $mat5->id,
            'id_proyecto' => $proy1->id,
            'cantidad_adquirida' => 45000.00,
            'cantidad_actual_lote' => 45000.00,
        ]);

        // Ingreso 3: Arena de río - 30,000 kg
        $ing3 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0003',
            'odc' => 'OC-AL-2026-0045',
            'id_proveedor' => $prov2->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(25)->format('Y-m-d'),
            'observaciones' => 'Arena lavada, libre de materia orgánica.',
        ]);

        DetalleIngreso::create([
            'id_ingreso' => $ing3->id,
            'id_material' => $mat7->id,
            'id_proyecto' => $proy1->id,
            'cantidad_adquirida' => 30000.00,
            'cantidad_actual_lote' => 30000.00,
        ]);

        // Ingreso 4: Emulsión CSS-1h - 8,000 L
        $ing4 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0004',
            'odc' => 'OC-AL-2026-0051',
            'id_proveedor' => $prov1->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(20)->format('Y-m-d'),
        ]);

        DetalleIngreso::create([
            'id_ingreso' => $ing4->id,
            'id_material' => $mat3->id,
            'id_proyecto' => $proy1->id,
            'cantidad_adquirida' => 8000.00,
            'cantidad_actual_lote' => 8000.00,
        ]);

        // --- Lotes para PROYECTO 2: Avenida Juan Pablo II ---
        // Ingreso 5: Cemento PEN 50/70 - 18,000 kg
        $ing5 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0005',
            'odc' => 'OC-AL-2026-0058',
            'id_proveedor' => $prov3->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(15)->format('Y-m-d'),
        ]);

        DetalleIngreso::create([
            'id_ingreso' => $ing5->id,
            'id_material' => $mat2->id,
            'id_proyecto' => $proy2->id,
            'cantidad_adquirida' => 18000.00,
            'cantidad_actual_lote' => 18000.00,
        ]);

        // Ingreso 6: Grava 1/2" - 22,000 kg
        $ing6 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0006',
            'odc' => 'OC-AL-2026-0058',
            'id_proveedor' => $prov4->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(12)->format('Y-m-d'),
        ]);

        DetalleIngreso::create([
            'id_ingreso' => $ing6->id,
            'id_material' => $mat6->id,
            'id_proyecto' => $proy2->id,
            'cantidad_adquirida' => 22000.00,
            'cantidad_actual_lote' => 22000.00,
        ]);

        // Ingreso 7: Arena - 15,000 kg (lote parcialmente consumido como demo)
        $ing7 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0007',
            'odc' => 'OC-AL-2026-0058',
            'id_proveedor' => $prov2->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(10)->format('Y-m-d'),
        ]);

        $lote7 = DetalleIngreso::create([
            'id_ingreso' => $ing7->id,
            'id_material' => $mat7->id,
            'id_proyecto' => $proy2->id,
            'cantidad_adquirida' => 15000.00,
            'cantidad_actual_lote' => 15000.00,
        ]);

        // --- Lotes para PROYECTO 3: Viaducto San José (finalizado) ---
        // Ingreso 8: Mix completo - 20,000 kg cemento
        $ing8 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0008',
            'odc' => 'OC-AL-2025-0099',
            'id_proveedor' => $prov1->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(90)->format('Y-m-d'),
            'observaciones' => 'Lote final para cierre de proyecto.',
        ]);

        DetalleIngreso::create([
            'id_ingreso' => $ing8->id,
            'id_material' => $mat1->id,
            'id_proyecto' => $proy3->id,
            'cantidad_adquirida' => 20000.00,
            'cantidad_actual_lote' => 20000.00,
        ]);

        // ============================================================
        // 8. UN LOTE PARCIALMENTE CONSUMIDO (para probar PEPS visualmente)
        // ============================================================

        // Ingreso 9: Segundo lote de cemento para proyecto 1
        $ing9 = Ingreso::create([
            'nro_ticket' => 'TKT-2026-0009',
            'odc' => 'OC-AL-2026-0062',
            'id_proveedor' => $prov1->id,
            'id_usuario' => $admin->id,
            'fecha_adquirida' => now()->subDays(5)->format('Y-m-d'),
        ]);

        $lote9 = DetalleIngreso::create([
            'id_ingreso' => $ing9->id,
            'id_material' => $mat1->id,
            'id_proyecto' => $proy1->id,
            'cantidad_adquirida' => 20000.00,
            'cantidad_actual_lote' => 12500.00, // parcialmente consumido
        ]);

        // Crear una Salida de prueba para simular consumo parcial del lote
        $salidaDemo = \App\Models\Salida::create([
            'id_funcionario' => $func1->id,
            'id_proyecto' => $proy1->id,
            'id_usuario' => $admin->id,
            'uso' => 'Capa de rodadura - Tramo 0+000 a 0+500',
            'fecha_salida' => now()->subDays(3)->format('Y-m-d'),
        ]);

        \App\Models\DetalleSalida::create([
            'id_salida' => $salidaDemo->id,
            'id_detalle_ingreso' => $lote9->id,
            'cantidad_salida' => 7500.00,
        ]);
    }
}