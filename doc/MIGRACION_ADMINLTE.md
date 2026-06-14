# Guía de Migración: De Asphalt-AGY (Inertia/Vue) a AdminLTE (Blade/jQuery)

**Proyecto origen:** Sistema de Control de Materiales e Inventarios - Asphalt-AGY  
**Para nuevo proyecto:** Laravel 13 + AdminLTE + pnpm  
**Fecha:** 2026-06-13

---

## 1. RESUMEN DEL PROYECTO

### Nombre
**Asphalt-AGY** - Sistema de Control de Materiales e Inventarios para Planta de Asfalto

### Objetivo
Gestionar el inventario físico de materiales de construcción usando el método **PEPS (Primeras Entradas, Primeras Salidas)** con control por lotes, orientado a plantas de asfalto en El Alto, Bolivia.

### Funcionalidades Core
1. **Autenticación** con 3 roles (Administrador, Operador, Visor)
2. **Catálogos:** Materiales, Unidades de Medida, Proveedores, Funcionarios, Usuarios
3. **Operaciones:** Registro de Ingresos (compras) y Salidas (despachos) con算法 PEPS
4. **Reportes:** Dashboard, Kardex por material/proyecto, consumo por obra
5. **Seguridad:** Permisos granulares por rol

### Stack Tecnológico Destino
- **Backend:** Laravel 13
- **Frontend:** AdminLTE 3.x (Blade templates + jQuery)
- **Build:** pnpm
- **DB:** MySQL

---

## 2. MODELO DE BASE DE DATOS

### 2.1 Diagrama de Relaciones

```
users ────< ingresos
users ────< salidas
unidad_medidas ────< materials
proveedors ────< ingresos
proyectos ────< detalle_ingresos
proyectos ────< salidas
funcionarios ────< salidas
materials ────< detalle_ingresos
ingresos ────< detalle_ingresos
detalle_ingresos ────< detalle_salidas
salidas ────< detalle_salidas
```

### 2.2 Diccionario de Datos (10 tablas)

#### `users` — Usuarios del sistema
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | Autoincrement |
| name | VARCHAR(155) | Nombre completo |
| username | VARCHAR(50) UNIQUE | Login |
| password | VARCHAR(255) | Bcrypt |
| role | ENUM('administrador','operador','visor') | |
| active | TINYINT(1) DEFAULT 1 | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `unidad_medidas` — Unidades de medida
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| nombre | VARCHAR(50) | Ej: "Metros Cúbicos" |
| abreviacion | VARCHAR(10) | Ej: "m³" |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `materials` — Catálogo de materiales
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| nombre | VARCHAR(100) | Ej: "Grava 3/4" |
| descripcion | TEXT NULL | |
| id_medida | BIGINT UNSIGNED FK | → unidad_medidas |
| stock_minimo | DECIMAL(12,2) DEFAULT 0.00 | Para alertas |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `proveedors` — Proveedores
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| razon_social | VARCHAR(150) | |
| nit | VARCHAR(30) UNIQUE NULL | |
| telefono | VARCHAR(20) NULL | |
| direccion | VARCHAR(255) NULL | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `proyectos` — Obras/proyectos
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| nombre | VARCHAR(150) | Ej: "Renovación Av. Brasil" |
| ubicacion | VARCHAR(255) NULL | |
| encargado | VARCHAR(100) NULL | |
| fecha_inicio | DATE NULL | |
| fecha_fin | DATE NULL | |
| estado | ENUM('activo','finalizado','pausado') DEFAULT 'activo' | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `funcionarios` — Personal autorizado
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| nombre | VARCHAR(150) | |
| cargo | VARCHAR(100) NULL | Ej: "Chofer de Volqueta" |
| area | VARCHAR(100) NULL | Ej: "Mantenimiento Vial" |
| activo | TINYINT(1) DEFAULT 1 | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `ingresos` — Cabecera de adquisiciones
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| nro_ticket | VARCHAR(50) NULL | Ticket balanza física |
| odc | VARCHAR(50) NULL | Orden de Compra |
| id_proveedor | BIGINT UNSIGNED FK NULL | → proveedors |
| id_usuario | BIGINT UNSIGNED FK | → users (quien registra) |
| fecha_adquirida | DATE | Fecha física recepción |
| observaciones | TEXT NULL | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `detalle_ingresos` — Lotes PEPS (CLAVE DEL SISTEMA)
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| id_ingreso | BIGINT UNSIGNED FK | → ingresos (CASCADE) |
| id_material | BIGINT UNSIGNED FK | → materials |
| id_proyecto | BIGINT UNSIGNED FK | → proyectos |
| cantidad_adquirida | DECIMAL(12,2) | Cantidad original |
| cantidad_actual_lote | DECIMAL(12,2) | Stock disponible (se reduce con PEPS) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Importante:** Esta tabla representa cada lote físico. `cantidad_actual_lote` es el saldo disponible. Se descuenta mediante el algoritmo PEPS.

#### `salidas` — Cabecera de despachos
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| id_funcionario | BIGINT UNSIGNED FK | → funcionarios (quien recibe) |
| id_proyecto | BIGINT UNSIGNED FK | → proyectos (destino) |
| id_usuario | BIGINT UNSIGNED FK | → users (quien registra) |
| uso | VARCHAR(255) NULL | Descripción del uso |
| fecha_salida | DATE | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `detalle_salidas` — Detalle de lotes consumidos
| Campo | Tipo | Notas |
|-------|------|-------|
| id | BIGINT UNSIGNED PK | |
| id_salida | BIGINT UNSIGNED FK | → salidas (CASCADE) |
| id_detalle_ingreso | BIGINT UNSIGNED FK | → detalle_ingresos (lote origen) |
| cantidad_salida | DECIMAL(12,2) | Cantidad descontada de ese lote |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### 2.3 Reglas de Integridad
1. **Eliminación en cascada:** Al borrar `ingreso` o `salida`, se borran sus detalles
2. **Restricción de borrado:** No se puede eliminar material/proyecto/proveedor/funcionario si tiene movimientos asociados

---

## 3. REQUISITOS FUNCIONALES (RF)

### 3.1 Seguridad y Acceso
| RF | Descripción |
|----|-------------|
| RF-01 | Login con username + contraseña |
| RF-02 | 3 roles: Administrador (full), Operador (operaciones), Visor (solo lectura) |
| RF-03 | Administrador gestiona usuarios (crear, editar, desactivar) |

### 3.2 Catálogos
| RF | Descripción |
|----|-------------|
| RF-04 | CRUD Materiales (nombre, descripción, unidad medida, stock mínimo) |
| RF-05 | CRUD Funcionarios (nombre, cargo, área) |
| RF-06 | CRUD Proveedores |

### 3.3 Operaciones
| RF | Descripción |
|----|-------------|
| RF-07 | CRUD Proyectos (nombre, ubicación, encargado, fechas, estado) |
| RF-08 | Registrar Ingreso: N° ticket, ODC, proveedor, material, cantidad, fecha, observaciones |
| RF-09 | Registrar Salida PEPS: proyecto, material, cantidad, funcionario, uso |

### 3.4 Reportes
| RF | Descripción |
|----|-------------|
| RF-10 | Dashboard: stock total, alertas (semáforo), proyectos activos |
| RF-11 | Kardex: historial movements por material (entradas, salidas, saldos) filtrado por fechas |
| RF-12 | Consumo por proyecto |
| RF-13 | Exportar a PDF |

---

## 4. LÓGICA PEPS (ALGORITMO CRÍTICO)

### 4.1 Cómo funciona

Al registrar una **salida** (despacho):

```
1. Recibir: id_material, cantidad_total, id_proyecto, id_funcionario, uso
2. Buscar lotes disponibles:
   SELECT * FROM detalle_ingresos 
   WHERE id_material = X AND cantidad_actual_lote > 0 
   ORDER BY created_at ASC
3. Para cada lote (del más antiguo al más nuevo):
   a. Si cantidad_total == 0 → FIN
   b. Si lote.cantidad_actual_lote >= cantidad_total:
      - lote.cantidad_actual_lote -= cantidad_total
      - Crear detalle_salida con cantidad_total
      - cantidad_total = 0 → FIN
   c. Si lote.cantidad_actual_lote < cantidad_total:
      - cantidad_total -= lote.cantidad_actual_lote
      - Crear detalle_salida con lote.cantidad_actual_lote
      - lote.cantidad_actual_lote = 0
      - Continuar al siguiente lote
4. Crear registro en tabla 'salidas'
5. Si cantidad_total > 0 después de todos los lotes → ERROR "Stock insuficiente"
```

### 4.2 Validaciones importantes
- `cantidad_salida` debe ser > 0
- `cantidad_salida` no debe exceder `stock_planta` disponible
- Usar transacción DB + `lockForUpdate()` para evitar race conditions

### 4.3 Cálculo de stock planta
```sql
SELECT COALESCE(SUM(cantidad_actual_lote), 0) 
FROM detalle_ingresos 
WHERE id_material = X
```

---

## 5. ROLES Y PERMISOS

### 5.1 Roles

| Rol | Descripción |
|-----|-------------|
| administrador | CRUD total: usuarios, materiales, catálogos, operaciones, reportes |
| operador | CRUD proyectos, ingresos, salidas, reportes. NO puede alterar catálogos ni usuarios |
| visor | Solo lectura: reportes y consulta de stock |

### 5.2 Permisos granulares (recomendado)

```
gestionar_usuarios      → ver, crear, editar, eliminar usuarios
gestionar_materiales    → ver, crear, editar, eliminar materiales
gestionar_funcionarios  → ver, crear, editar, eliminar funcionarios
gestionar_proveedores   → ver, crear, editar, eliminar proveedores
gestionar_proyectos     → ver, crear, editar, eliminar proyectos
gestionar_ingresos      → ver, crear, eliminar ingresos
gestionar_salidas       → ver, crear, eliminar salidas (despachos)
ver_kardex             → acceder al reporte Kardex
ver_dashboard          → acceder al dashboard
```

### 5.3 Middleware ejemplo

```php
// app/Http/Middleware/CheckPermission.php
public function handle($request, Closure $next, $permission)
{
    if (!in_array($permission, session('permissions') ?? [])) {
        abort(403, 'No tienes permiso para esta acción');
    }
    return $next($request);
}
```

---

## 6. ESTRUCTURA DE RUTAS RECOMENDADA

```php
// routes/web.php

Auth::routes();

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Protegidas por auth + roles
Route::middleware(['auth'])->group(function () {
    
    // Users (solo admin)
    Route::middleware(['role:administrador'])->group(function () {
        Route::resource('users', UserController::class);
    });
    
    // Catálogos (admin + operador)
    Route::middleware(['role:administrador,operador'])->group(function () {
        Route::resource('materials', MaterialController::class);
        Route::resource('funcionarios', FuncionarioController::class);
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('proyectos', ProyectoController::class);
    });
    
    // Operaciones
    Route::middleware(['role:administrador,operador'])->group(function () {
        Route::resource('ingresos', IngresoController::class)->except(['edit', 'update']);
        Route::resource('salidas', SalidaController::class)->except(['edit', 'update']);
    });
    
    // Reportes (todos los roles autenticados)
    Route::get('kardex', [ReporteController::class, 'kardex'])->name('kardex.index');
    Route::get('reportes/consumo-proyecto/{id}', [ReporteController::class, 'consumoProyecto']);
});
```

---

## 7. CONTROLADORES PRINCIPALES

### 7.1 DashboardController
```php
// Datos para el dashboard:
// - materials: lista con stock_actual calculado
// - proyectosConInventario: proyectos con inventario por material
// - recentMovements: últimos 10 movimientos (ingresos + salidas)
```

### 7.2 IngresoController
```php
store(Request $request):
  1. Validar request
  2. Crear registro en tabla 'ingresos'
  3. Crear registro en tabla 'detalle_ingresos' (lote)
  4. Flash success + redirect

destroy($id):
  1. Obtener ingreso con detalle_ingresos
  2. Eliminar (cascade borra detalle_ingresos)
  3. Redirect con success
```

### 7.3 SalidaController (PEPS)
```php
store(Request $request):
  1. Validar: cantidad > 0, stock disponible
  2. DB::transaction(function() use ($request) {
       // ALGORITMO PEPS aquí (sección 4.1)
       // Crear registro en 'salidas'
       // Crear registros en 'detalle_salidas'
       // Actualizar cantidad_actual_lote en cada lote
     });
  3. Flash success + redirect

destroy($id):
  1. DB::transaction con lockForUpdate()
  2. Restaurar stock a los lotes original
  3. Eliminar salida (cascade elimina detalle_salidas)
```

### 7.4 ReporteController
```php
kardex(Request $request):
  // Filtros: material_id, proyecto_id, fecha_desde, fecha_hasta
  // Return: vista con movimientos del kardex
  
consumoProyecto($id):
  // Return: materiales consumidos por un proyecto específico
```

---

## 8. VISTAS PRINCIPALES (AdminLTE)

### 8.1 Layout Principal
```
resources/views/layouts/adminlte.blade.php
├── Sidebar: navegación con menú
├── Navbar: usuario, notificaciones
├── Content: @yield('content')
└── Footer
```

### 8.2 Páginas
```
resources/views/
├── auth/
│   ├── login.blade.php
│   └── register.blade.php (deshabilitado)
├── layouts/
│   └── adminlte.blade.php
├── dashboard/
│   └── index.blade.php
├── materials/
│   ├── index.blade.php (tabla + acciones)
│   ├── create.blade.php
│   └── edit.blade.php
├── ingresos/
│   ├── index.blade.php
│   ├── create.blade.php (formulario complejo)
│   └── show.blade.php (detalle + kardex)
├── salidas/
│   ├── index.blade.php
│   ├── create.blade.php (selección lote PEPS)
│   └── show.blade.php
├── proyectos/
├── funcionarios/
├── proveedores/
├── users/
├── reportes/
│   ├── kardex.blade.php
│   └── consumo-proyecto.blade.php
└── bitacora/
    └── index.blade.php
```

---

## 9. COMPONENTES ADMINLTE A USAR

### 9.1 Widgets comunes
```blade
{{-- Card stat --}}
<div class="small-box bg-info">
  <div class="inner">
    <h3>150</h3>
    <p>Nuevos ingresos</p>
  </div>
  <div class="icon"><i class="fas fa-truck"></i></div>
</div>

{{-- Tabla DataTables --}}
<table id="example1" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Material</th>
      <th>Cantidad</th>
    </tr>
  </thead>
</table>

{{-- Alerta --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
  </div>
@endif

{{-- Formulario con validación --}}
@if($errors->any())
  <div class="alert alert-danger">
    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif
```

---

## 10. CONFIGURACIÓN ADMINLTE

### 10.1 Instalación
```bash
composer require jeroennoten/laravel-adminlte
php artisan adminlte:install --only=main
php artisan adminlte:plugins install
```

### 10.2 Configuración de menú (config/adminlte.php)
```php
'sidebar' => [
    'logo' => '<b>Asphalt</b>-AGY',
    'menu' => [
        // Dashboard
        ['header' => 'PANEL'],
        ['text' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/dashboard'],
        
        // Catálogos
        ['header' => 'CATÁLOGOS'],
        ['text' => 'Materiales', 'icon' => 'fas fa-box', 'url' => '/materials'],
        ['text' => 'Unidades de Medida', 'icon' => 'fas fa-ruler', 'url' => '/unidad-medidas'],
        ['text' => 'Funcionarios', 'icon' => 'fas fa-users', 'url' => '/funcionarios'],
        ['text' => 'Proveedores', 'icon' => 'fas fa-truck', 'url' => '/proveedores'],
        
        // Operaciones
        ['header' => 'OPERACIONES'],
        ['text' => 'Proyectos', 'icon' => 'fas fa-hard-hat', 'url' => '/proyectos'],
        ['text' => 'Ingresos', 'icon' => 'fas fa-arrow-down', 'url' => '/ingresos'],
        ['text' => 'Salidas / Despachos', 'icon' => 'fas fa-arrow-up', 'url' => '/salidas'],
        
        // Reportes
        ['header' => 'REPORTES'],
        ['text' => 'Kardex', 'icon' => 'fas fa-book', 'url' => '/kardex'],
        ['text' => 'Bitácora', 'icon' => 'fas fa-clipboard-list', 'url' => '/bitacora'],
        
        // Config (admin)
        ['header' => 'ADMINISTRACIÓN'],
        ['text' => 'Usuarios', 'icon' => 'fas fa-user-cog', 'url' => '/users', 'can' => 'gestionar_usuarios'],
    ],
],
```

---

## 11. MIGRACIONES ESENCIALES

### 11.1 Migraciones a crear (en orden)
1. `create_users_table`
2. `create_unidad_medidas_table`
3. `create_materials_table`
4. `create_proveedors_table`
5. `create_proyectos_table`
6. `create_funcionarios_table`
7. `create_ingresos_table`
8. `create_detalle_ingresos_table`
9. `create_salidas_table`
10. `create_detalle_salidas_table`

### 11.2 Relaciones en migraciones
```php
// detalle_ingresos
$table->foreignId('id_material')->constrained('materials');
$table->foreignId('id_proyecto')->constrained('proyectos');
$table->foreignId('id_ingreso')->constrained('ingresos')->onDelete('cascade');

// detalle_salidas
$table->foreignId('id_salida')->constrained('salidas')->onDelete('cascade');
$table->foreignId('id_detalle_ingreso')->constrained('detalle_ingresos');
```

---

## 12. SEEDERS RECOMENDADOS

### 12.1 Unidades de medida base
```php
[
    ['nombre' => 'Kilogramo', 'abreviacion' => 'kg'],
    ['nombre' => 'Metro Cúbico', 'abreviacion' => 'm³'],
    ['nombre' => 'Litro', 'abreviacion' => 'L'],
    ['nombre' => 'Tonelada', 'abreviacion' => 'T'],
    ['nombre' => 'Unidad', 'abreviacion' => 'und'],
];
```

### 12.2 Usuario admin por defecto
```php
[
    'name' => 'Administrador',
    'username' => 'admin',
    'password' => Hash::make('password'), // Cambiar inmediatamente
    'role' => 'administrador',
    'active' => 1,
];
```

---

## 13. NOTES Y WARNINGS

### 13.1 Bugs conocidos del proyecto original (corregir en nuevo)
- **Race condition** en `SalidaController::destroy` - usar `lockForUpdate()`
- **Sin logging** en catch blocks - agregar `Log::error()`
- **Validación insuficiente** cantidad_salida <= stock disponible
- **Null checks** faltantes en relaciones

### 13.2 Mejoras recomendadas para Laravel 13
- Usar **Policies** de Laravel para autorización
- Usar **Form Requests** para validación
- Implementar **db:transaction** en todas las operaciones PEPS
- Agregar **CHECK constraints** en BD para evitar negativos
- Implementar **Job/Queue** para operaciones pesadas

---

## 14. CHECKLIST DE MIGRACIÓN

- [ ] Crear proyecto Laravel 13 con AdminLTE
- [ ] Configurar autenticación (Breeze o manual)
- [ ] Crear migraciones en orden
- [ ] Crear Models con relaciones
- [ ] Implementar middleware de roles/permisos
- [ ] Crear controllers CRUD para cada recurso
- [ ] Implementar lógica PEPS en SalidaController
- [ ] Crear vistas Blade con AdminLTE
- [ ] Configurar menú AdminLTE
- [ ] Implementar Dashboard
- [ ] Implementar Kardex con filtros
- [ ] Agregar seeders
- [ ] Probar flujo completo: ingreso → salida PEPS
- [ ] Exportar a PDF (usar DomPDF o similar)

---

*Documento generado para migración de Asphalt-AGY a Laravel 13 + AdminLTE*