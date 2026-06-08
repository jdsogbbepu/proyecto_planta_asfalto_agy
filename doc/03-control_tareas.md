# Control de Tareas - Sistema "Planta de Asfalto AGY"

## Estado: ✅ COMPLETADO

---

## Sprint 1: Inicialización y Estructura Base
- [x] Limpieza del espacio de trabajo (eliminar archivos PHP legacy).
- [x] Inicialización del proyecto Laravel 12.
- [x] Configuración de dependencias (Vite, Vue 3, Inertia.js, Tailwind CSS).
- [x] Conexión de base de datos MySQL en `.env`.
- [x] Creación y personalización de las 10 migraciones de base de datos.
- [x] Definición de Modelos Eloquent y sus relaciones.
- [x] Ajustar la autenticación de Laravel Breeze de email a `username`.
- [x] Validación sintáctica y compilación de recursos frontend con Vite.

## Sprint 2: Control de Acceso y Gestión de Usuarios (US-01, US-02)
- [x] Aplicar tema industrial oscuro de alta fidelidad a la vista de Login.
- [x] Seeders de base de datos con administrador por defecto (`admin`/`admin123`).
- [x] CRUD de administración de usuarios en el Panel (solo Administradores).
- [x] Control lógico de usuarios activos/inactivos.
- [x] Middlewares de protección de rutas basados en Roles (`administrador`, `operador`, `visor`).

## Sprint 3: Gestión de Catálogos (US-03, US-04, US-05, US-06)
- [x] CRUD de Unidades de Medida y Materiales (Cemento Asfáltico, emulsiones, agregados).
- [x] CRUD de Proveedores.
- [x] CRUD de Proyectos/Obras Municipales (estado Activo/Pausado/Finalizado).
- [x] CRUD de Funcionarios Receptores.

## Sprint 4: Control de Almacén e Ingresos (US-07, US-08)
- [x] Registro de Ingresos de Materiales (Ticket de Balanza, ODC, Proveedor, Obra).
- [x] Creación de lotes cronológicos de ingreso con saldo inicial.
- [x] Vista bento de inventario general por proyecto.

## Sprint 5: Despachos y Algoritmo Transaccional PEPS (US-09)
- [x] Formulario de Salida de Materiales con validaciones físicas en tiempo real.
- [x] Implementación de la transacción SQL atómica para el algoritmo PEPS.
- [x] Pruebas unitarias/de integración del algoritmo (descuentos secuenciales).

## Sprint 6: Panel Bento Grid y Alertas (US-10)
- [x] Dashboard Bento Grid de monitoreo con paleta oscura industrial.
- [x] Alertas de stock crítico semaforizadas en base a stock mínimo de reorden.
- [x] Reportes analíticos de movimiento e inventario valorado (PEPS).

## Sprint 7: Pruebas y Despliegue Offline
- [x] Ejecución del plan de pruebas transaccionales (PEPS).
- [x] Configuración del servidor en red Intranet local por IP.
- [x] Script automatizado de backup diario MySQL en Windows (`.bat`).

## Sprint 8: Mejoras, Permisos Dinámicos, Reportes y Bitácora

### Base de Datos y Modelos
- [x] Tabla `role_permissions` (migración + modelo `RolePermission.php`)
- [x] Tabla `bitacora_actividades` (migración + modelo `BitacoraActividad.php`)
- [x] Campo `codigo_interno` en `materials` (migración + `$fillable`)

### Backend
- [x] `PermissionMiddleware.php` con verificación dinámica de permisos
- [x] Registro de middleware `permission` en `bootstrap/app.php`
- [x] `PermisoController.php` (index + update)
- [x] `BitacoraController.php` (index con búsqueda)
- [x] Rutas protegidas con `permission:nombre_permiso` en `routes/web.php`
- [x] `DatabaseSeeder.php` con permisos predeterminados (admin/operador/visor)
- [x] `HandleInertiaRequests.php` comparte `permissions` con frontend
- [x] `MaterialController.php` validando `codigo_interno`

### Frontend
- [x] `Materials/Index.vue` — columna y campo `codigo_interno`
- [x] `Permisos/Index.vue` — matriz de roles vs permisos con checkboxes
- [x] `Bitacora/Index.vue` — tabla de auditoría con paginación y búsqueda
- [x] `Salidas/Create.vue` — bug de stock corregido (`Number()` en `getAvailableStock`)
- [x] `Kardex.vue` — toggle de orientación (Horizontal/Vertical), botón imprimir, CSS `@media print` con formato oficial, header/footer de impresión
- [x] `AuthenticatedLayout.vue` — navegación con permisos para Permisos y Bitácora

### Pruebas y Verificación
- [x] `php artisan test` — `PepsAlgorithmTest` ✅
- [x] `php artisan test` — `PermissionMiddlewareTest` ✅ (7 tests)
- [ ] Verificación manual pendiente (ver abajo)

---

## Verificación Manual Pendiente

### 1. Prueba de Bug de Stock en Salidas/Create.vue
- [ ] Iniciar sesión como **operador**
- [ ] Ir a "Despachos / Crear"
- [ ] Seleccionar un proyecto con stock disponible
- [ ] Intentar ingresar una cantidad **mayor** al stock disponible
- [ ] Verificar que aparece el mensaje de error y el botón "Registrar Salida" está deshabilitado

### 2. Prueba de Permisos Dinámicos
- [ ] Iniciar sesión como **administrador**
- [ ] Ir a "Permisos"
- [ ] Desmarcar el permiso `gestionar_salidas` para el rol **operador**
- [ ] Guardar cambios
- [ ] Iniciar sesión como **operador**
- [ ] Intentar acceder a `/despachos/crear` → debe dar **403**
- [ ] Volver como administrador y **volver a marcar** el permiso
- [ ] Verificar que operador vuelve a tener acceso

### 3. Prueba de Impresión en Kardex
- [ ] Iniciar sesión como cualquier rol con acceso a reportes
- [ ] Ir a "Kardex Físico"
- [ ] Seleccionar un proyecto y material con datos
- [ ] Hacer clic en **"Horizontal"** y luego en **"Imprimir / PDF"**
- [ ] Verificar que el documento se muestra en orientación Horizontal
- [ ] Repetir con **"Vertical"** y verificar orientación

### 4. Prueba de Bitácora de Auditoría
- [ ] Iniciar sesión como **administrador**
- [ ] Ir a "Bitácora"
- [ ] Registrar un nuevo material (como administrador)
- [ ] Verificar que la acción aparece en la bitácora con nombre, acción, fecha e IP

### 5. Prueba de Código Interno en Materiales
- [ ] Ir a "Materiales"
- [ ] Crear un nuevo material con código interno (ej. "CA-6070")
- [ ] Verificar que el código aparece en la tabla y en el modal de edición

---

## Credenciales de Prueba (Seeders)

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin | admin123 | administrador |
| operador | operador123 | operador |
| visor | visor123 | visor |

## Comandos de Verificación

```bash
# Ejecutar pruebas
php artisan test

# Ejecutar solo pruebas de PEPS
php artisan test --filter=PepsAlgorithmTest

# Ejecutar solo pruebas de permisos
php artisan test --filter=PermissionMiddlewareTest

# Ver rutas registradas
php artisan route:list

# Ver migraciones pendientes
php artisan migrate:status
```