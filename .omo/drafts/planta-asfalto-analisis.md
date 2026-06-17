# Draft: Planta Asfalto AGY - Análisis de Módulos de Despacho y Materiales

## Resumen del Proyecto

**Planta Asfalto AGY** es un sistema de gestión de materiales y despacho para una planta de asfalto del municipio de El Alto, Bolivia. 

**Stack**: Laravel 11 + Inertia.js + Vue 3 + Tailwind CSS

---

## Flujo de Negocio Actual

### 1. Catálogo de Materiales (`Materiales/Index.vue`)
- CRUD de materiales con código interno, nombre, descripción
- Cada material tiene unidad de medida y stock mínimo de alerta
- **No tiene seguimiento de stock global** - el stock se calcula por lote

### 2. Ingresos de Almacén (`Ingresos/Create.vue` → `Ingresos/Index.vue`)
**Propósito**: Registrar la llegada de materiales a la planta

**Flujo**:
1. Seleccionar proyecto destino
2. Ingresar ODC (Orden de Compra)
3. Agregar filas: material + cantidad + proveedor + fecha_lote
4. Se genera **nro_registro** (formato: `RGTR-YYYY-XXXX`)
5. Se crea un **lote** en `detalle_ingresos` con:
   - `cantidad_adquirida` = cantidad inicial
   - `cantidad_actual_lote` = cantidad inicial (se decrementa con despachos)

**Modelo de datos**:
```
Ingreso (cabecera)
├── nro_ticket, odc, id_proveedor, id_funcionario, fecha_adquirida
└── DetalleIngreso (lotes) [1:N]
    ├── nro_registro (RGTR-YYYY-XXXX)
    ├── id_material, id_proyecto, id_proveedor
    ├── cantidad_adquirida, cantidad_actual_lote
    ├── fecha_lote, acciones_planificadas
    └── DetalleSalida (consumos) [1:N]
        └── cantidad_salida
```

### 3. Despachos/Salidas (`Salidas/Create.vue` → `Salidas/Index.vue`)
**Propósito**: Registrar el consumo de materiales por proyecto (conciliación)

**Flujo**:
1. Seleccionar proyecto → carga lotes disponibles (PEPS)
2. Seleccionar funcionario receptor
3. Marcar lotes a consumir e ingresar cantidades
4. Validación: `cantidad_salida <= stock_planta`
5. Al guardar:
   - Crea registro en `salidas`
   - Crea `detalle_salidas` por cada lote consumido
   - **Decrementa `cantidad_actual_lote`** del lote

**Cálculo de Stock**:
```php
$stockPlanta = cantidad_adquirida - sum(cantidad_salida de todos los detalle_salida)
// Se storea en cantidad_actual_lote y se actualiza en cada despacho
```

---

## Arquitectura de Archivos Clave

### Backend (Laravel)
| Archivo | Responsabilidad |
|---------|----------------|
| `SalidaController.php` | CRUD despachos, getLotesPorProyecto, store, destroy |
| `IngresoController.php` | CRUD ingresos, store, destroy |
| `MaterialController.php` | CRUD materiales |
| `DetalleIngreso.php` | Modelo lote - generador nro_registro, accessor stockPlanta |
| `DetalleSalida.php` | Modelo consumo - apunta a lote origen |
| `BitacoraActividad.php` | Auditoría de todos los cambios |

### Frontend (Vue 3 + Inertia)
| Archivo | Responsabilidad |
|---------|----------------|
| `Salidas/Create.vue` | Formulario de despacho con selección de lotes |
| `Salidas/Index.vue` | Lista de despachos con expandible por lote |
| `Materials/Index.vue` | Tabla materiales + modal CRUD |
| `Ingresos/Create.vue` | Formulario multi-row de ingresos |
| `Ingresos/Index.vue` | Lista de ingresos con detalle de lotes |

---

## Issues Identificados (del Code Review)

### 🔴 CRITICAL / HIGH
1. **BUG-001**: Race condition en `SalidaController::destroy()` - Sin `lockForUpdate()` en la reversión,可能导致stock duplicado
2. **BUG-003**: Doble fuente de verdad - `cantidad_actual_lote` (stored) vs `cantidad_utilizada` (computed)
3. **DBG-001**: Posible duplicación de `nro_registro` en `DetalleIngreso::generarNroRegistro()` - usa Cache::lock pero el patrón podría mejorarse

### 🟡 MEDIUM
4. **BUG-002**: Null reference en `IngresoController::store()` línea 130-132 - `$material->nombre` y `$proyecto->nombre` sin validar null
5. **DBG-004**: Validación `cantidad_salida <= stock_planta` solo en frontend, no en backend (línea 138 de store solo tiene `gt:0`)

### 🟢 LOW
6. **ERR-003**: `console.log` y `alert` de debug en `Ingresos/Create.vue:82-89`
7. **DBG-003**: Mutación directa de array reactivo en `Salidas/Create.vue:88-95`

---

## Gaps de Funcionalidad (Observaciones)

### Módulo Materiales
- ❌ No muestra stock total por material (suma de todos los lotes)
- ❌ No hay alertas visuales cuando stock < stock_minimo
- ❌ No hay histórico de movimientos por material

### Módulo Despachos
- ❌ No permite editar un despacho ya registrado
- ❌ No permite eliminar un lote específico de un despacho (reverse parcial)
- ❌ No hay forma de ver todos los despachos de un lote específico
- ⚠️ La validación de cantidad > stock es solo visual (frontend)

### Flujo PEPS
- ⚠️ El sistema dice ser PEPS pero realmente asigna lotes específicos en cada despacho - no consume automáticamente del más antiguo

---

## Permisos del Sistema
```php
ver_dashboard        // Ver panel principal
gestionar_usuarios    // CRUD usuarios
gestionar_permisos    // Asignar permisos a roles
ver_bitacora         // Ver auditoría
gestionar_materiales  // CRUD materiales y unidades
gestionar_proveedores // CRUD proveedores
gestionar_proyectos   // CRUD proyectos
gestionar_funcionarios // CRUD funcionarios
gestionar_ingresos    // Crear/eliminar ingresos
gestionar_salidas     // Crear/revertir despachos
ver_reportes          // Ver Kardex
```

---

## Notas de Investigación

### Punto de Decisión: Stock Calculation
El stock de un lote se calcula como:
```
stock_planta = cantidad_adquirida - SUM(cantidad_salida de detalle_salidas)
```

Sin embargo, `cantidad_actual_lote` se actualiza decrementally en cada despacho (en lugar de calcularse dinámicamente). Esto crea una doble fuente de verdad que podría desincronizarse.

### Punto de Decisión: PEPS
El sistema menciona "PEPS" (First In, First Out) pero la implementación no fuerza consumo automático del lote más antiguo. El usuario selecciona manualmente qué lotes consume en cada despacho.

---

## Preguntas Abiertas para Mejoras

1. ¿Qué mejoras específicas quieres en el módulo de **despacho**?
2. ¿Qué mejoras específicas quieres en el módulo de **materiales**?
3. ¿Necesitas alertas de stock bajo?
4. ¿Quieres permitir edición de despachos existentes?
5. ¿Quieres agregar un dashboard de KPI para materiales?