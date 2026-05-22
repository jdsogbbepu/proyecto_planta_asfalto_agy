<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas protegidas generales por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard (requiere ver_dashboard)
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:ver_dashboard')
        ->name('dashboard');

    // Perfil (no requiere permiso dinámico, solo auth)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de Usuarios (gestionar_usuarios)
    Route::middleware('permission:gestionar_usuarios')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });

    // Módulo de Roles y Permisos (gestionar_permisos)
    Route::middleware('permission:gestionar_permisos')->group(function () {
        Route::get('permisos', [PermisoController::class, 'index'])->name('permisos.index');
        Route::post('permisos', [PermisoController::class, 'update'])->name('permisos.update');
    });

    // Módulo de Bitácora de Auditoría (ver_bitacora)
    Route::middleware('permission:ver_bitacora')->group(function () {
        Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

    // Módulo de Materiales y Unidades (gestionar_materiales)
    Route::middleware('permission:gestionar_materiales')->group(function () {
        Route::get('materials', [MaterialController::class, 'index'])->name('materials.index');
        Route::post('materials', [MaterialController::class, 'store'])->name('materials.store');
        Route::put('materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
        Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

        Route::post('unidad-medidas', [UnidadMedidaController::class, 'store'])->name('unidad-medidas.store');
        Route::put('unidad-medidas/{unidad_medida}', [UnidadMedidaController::class, 'update'])->name('unidad-medidas.update');
        Route::delete('unidad-medidas/{unidad_medida}', [UnidadMedidaController::class, 'destroy'])->name('unidad-medidas.destroy');
    });

    // Módulo de Proveedores (gestionar_proveedores)
    Route::middleware('permission:gestionar_proveedores')->group(function () {
        Route::get('proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::post('proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::put('proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    });

    // Módulo de Proyectos (gestionar_proyectos)
    Route::middleware('permission:gestionar_proyectos')->group(function () {
        Route::get('proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
        Route::post('proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
        Route::put('proyectos/{proyecto}', [ProyectoController::class, 'update'])->name('proyectos.update');
        Route::delete('proyectos/{proyecto}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');
    });

    // Módulo de Funcionarios (gestionar_funcionarios)
    Route::middleware('permission:gestionar_funcionarios')->group(function () {
        Route::get('funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
        Route::post('funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
        Route::put('funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
        Route::delete('funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
    });

    // Módulo de Ingresos de Almacén (gestionar_ingresos)
    Route::middleware('permission:gestionar_ingresos')->group(function () {
        Route::get('ingresos', [IngresoController::class, 'index'])->name('ingresos.index');
        Route::get('ingresos/crear', [IngresoController::class, 'create'])->name('ingresos.create');
        Route::post('ingresos', [IngresoController::class, 'store'])->name('ingresos.store');
        Route::delete('ingresos/{ingreso}', [IngresoController::class, 'destroy'])->name('ingresos.destroy');
    });

    // Módulo de Despachos / Salidas (gestionar_salidas)
    Route::middleware('permission:gestionar_salidas')->group(function () {
        Route::get('despachos', [SalidaController::class, 'index'])->name('despachos.index');
        Route::get('despachos/crear', [SalidaController::class, 'create'])->name('despachos.create');
        Route::post('despachos', [SalidaController::class, 'store'])->name('despachos.store');
        Route::delete('despachos/{salida}', [SalidaController::class, 'destroy'])->name('despachos.destroy');
    });

    // Reportes / Kardex (ver_reportes)
    Route::middleware('permission:ver_reportes')->group(function () {
        Route::get('kardex', [ReporteController::class, 'index'])->name('kardex.index');
    });
});

require __DIR__.'/auth.php';
