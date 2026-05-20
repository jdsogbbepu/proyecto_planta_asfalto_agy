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
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only user management
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
});

// Catalog resources protected by roles
Route::middleware(['auth'])->group(function () {
    // Read operations (all authorized roles: administrador, operador, visor)
    Route::middleware('role:administrador,operador,visor')->group(function () {
        Route::get('materials', [MaterialController::class, 'index'])->name('materials.index');
        Route::get('proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
        Route::get('funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
        Route::get('ingresos', [IngresoController::class, 'index'])->name('ingresos.index');
        Route::get('despachos', [SalidaController::class, 'index'])->name('despachos.index');
        Route::get('kardex', [ReporteController::class, 'index'])->name('kardex.index');
    });

    // Write operations (only administrador and operador)
    Route::middleware('role:administrador,operador')->group(function () {
        Route::post('materials', [MaterialController::class, 'store'])->name('materials.store');
        Route::put('materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
        Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

        Route::post('unidad-medidas', [UnidadMedidaController::class, 'store'])->name('unidad-medidas.store');
        Route::put('unidad-medidas/{unidad_medida}', [UnidadMedidaController::class, 'update'])->name('unidad-medidas.update');
        Route::delete('unidad-medidas/{unidad_medida}', [UnidadMedidaController::class, 'destroy'])->name('unidad-medidas.destroy');

        Route::post('proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::put('proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

        Route::post('proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
        Route::put('proyectos/{proyecto}', [ProyectoController::class, 'update'])->name('proyectos.update');
        Route::delete('proyectos/{proyecto}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

        Route::post('funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
        Route::put('funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
        Route::delete('funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');

        Route::get('ingresos/crear', [IngresoController::class, 'create'])->name('ingresos.create');
        Route::post('ingresos', [IngresoController::class, 'store'])->name('ingresos.store');
        Route::delete('ingresos/{ingreso}', [IngresoController::class, 'destroy'])->name('ingresos.destroy');

        Route::get('despachos/crear', [SalidaController::class, 'create'])->name('despachos.create');
        Route::post('despachos', [SalidaController::class, 'store'])->name('despachos.store');
        Route::delete('despachos/{salida}', [SalidaController::class, 'destroy'])->name('despachos.destroy');
    });
});

require __DIR__.'/auth.php';
