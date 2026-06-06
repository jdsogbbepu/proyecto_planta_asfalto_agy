# Revisión de Código - Planilla de Asfalto El Alto

> Proyecto: Sistema de gestión de materiales y despacho para planta de asfalto
> Stack: Laravel + Inertia.js + Vue
> Fecha inicio: 2026-06-05

---

## Resumen Ejecutivo

| Skill | Estado | Issues | Severidad |
|-------|--------|---------|-----------|
| `diagnose` | ✅ Completada | 6 | HIGH: 1, MEDIUM: 3, LOW: 2 |
| `error-handling-patterns` | ✅ Completada | 5 | HIGH: 1, MEDIUM: 2, LOW: 2 |
| `systematic-debugging` | ✅ Completada | 10 | HIGH: 2, MEDIUM: 4, LOW: 4 |
| `vercel-react-best-practices` | ✅ Completada | 10 | CRITICAL: 1, HIGH: 1, MEDIUM: 4, LOW: 4 |
| `improve-codebase-architecture` | ⏳ Pendiente | - | - |

**Total acumulado: 31 issues** (CRITICAL: 1, HIGH: 6, MEDIUM: 13, LOW: 11)

---

## Skill 1: Diagnose

**Bugs y problemas de lógica encontrados:**

### BUG-001 · HIGH
- **Archivo:** `app/Http/Controllers/SalidaController.php:201-231`
- **Método:** `destroy()`
- **Problema:** Condición de carrera (race condition). Sin `lockForUpdate()` ni transacción a nivel de fila.
- **Impacto:** Si dos usuarios llaman `DELETE /despachos/{id}` en paralelo, el stock se restaura 2 veces.
- **Predicción:** Stock final = `stock_original + cantidad_salida * 2`
- **Recomendación:** Usar `DB::transaction()` con `lockForUpdate()` en el lote antes de restaurar

---

### BUG-002 · MEDIUM
- **Archivo:** `app/Http/Controllers/IngresoController.php:130-132`
- **Método:** `store()`
- **Problema:** Posible null reference en `$material->nombre` y `$proyecto->nombre`
- **Código:**
  ```php
  $material = Material::find($item['id_material']);
  $proyecto = Proyecto::find($item['id_proyecto']);
  $resumenItems[] = "{$nroRegistro}: {$item['cantidad']} {$material->nombre} para {$proyecto->nombre}";
  ```
- **Impacto:** Si la relación se elimina entre validación y uso → 500 error
- **Recomendación:** Usar relación ya cargada o validar null con fallback

---

### BUG-003 · MEDIUM
- **Archivo:** `app/Models/DetalleIngreso.php:56-64`
- **Problema:** Doble fuente de verdad para el stock del lote
- **Descripción:** El sistema tiene `cantidad_actual_lote` (almacenado) Y `cantidad_utilizada` (computado). Si no están sincronizados, el inventario se rompe.
- **Recomendación:** Eliminar `cantidad_utilizada` computado y usar solo `cantidad_adquirida - cantidad_actual_lote`

---

### BUG-004 · MEDIUM
- **Archivo:** `app/Http/Controllers/IngresoController.php` y `SalidaController.php`
- **Problema:** Falta autorización específica en `destroy()`
- **Descripción:** No hay re-autenticación ni Policy de Laravel para confirmar eliminación
- **Impacto:** Usuario podría eliminar por error sin confirmación
- **Recomendación:** Implementar Policy con `authorize()` o agregar paso de confirmación

---

### BUG-005 · LOW
- **Todos los destroy** en controllers con FK
- **Problema:** Sin constraints a nivel base de datos para evitar stock negativo
- **Recomendación:** Añadir CHECK constraint en migration

---

### BUG-006 · LOW
- **Archivo:** `app/Http/Controllers/BitacoraController.php` y otros
- **Problema:** IP spoofing posible — `$request->ip()` no está validado ni sanitizado
- **Recomendación:** Guardar IP real del servidor proxy o validar formato

---

## Skill 2: Error-Handling Patterns

**Patrones de manejo de errores encontrados:**

### ERR-001 · HIGH
- **Archivos:** Todos los controllers con `catch (\Exception $e)`
- **Problema:** **No se usa `Log::error()` ni ningún logging en los catch blocks**
- **Impacto:** Los errores se pierden sin registro. No hay forma de auditar qué falló en producción.
- **Recomendación:**
  ```php
  catch (\Exception $e) {
      \Illuminate\Support\Facades\Log::error('SalidaController::destroy failed', [
          'salida_id' => $salida->id,
          'user_id' => auth()->id(),
          'exception' => $e,
      ]);
      DB::rollBack();
      return redirect()->back()->with('error', 'Error al revertir: ' . $e->getMessage());
  }
  ```

---

### ERR-002 · MEDIUM
- **Archivos:** Todos los controllers
- **Problema:** Los catch solo capturan `\Exception`, nunca `\Throwable`
- **Impacto:** Errores PHP como `TypeError`, `ArgumentCountError` o `ParseError` no se capturan
- **Recomendación:** Cambiar `catch (\Exception $e)` por `catch (\Throwable $e)`

---

### ERR-003 · MEDIUM
- **Archivo:** `resources/js/Pages/Ingresos/Create.vue:82-89`
- **Problema:** `console.log` y `alert()` de debug en producción
- **Código:**
  ```javascript
  // Debug: mostrar datos antes de enviar
  console.log('Datos a enviar:', {...});
  alert('Enviando datos... revisa la consola para ver qué se envía');
  ```
- **Recomendación:** Eliminar alerts y logs de debug antes de producción

---

### ERR-004 · LOW
- **Archivos:** Vue components
- **Problema:** Errores solo van a `console.error`, no se reportan al servidor
- **Recomendación:** Implementar reportería de errores client-side a un endpoint de logs

---

### ERR-005 · LOW
- **Archivos:** `app/Http/Middleware/PermissionMiddleware.php:40`, `RoleMiddleware.php:41`
- **Problema:** `abort(403)` no loguea intentos de acceso denegado
- **Recomendación:** Agregar Log::warning cuando se deniega acceso

---

## Skill 3: Systematic-Debugging

**Edge cases y código problemático encontrado:**

### DBG-001 · HIGH
- **Archivo:** `app/Models/DetalleIngreso.php:76-85`
- **Método:** `generarNroRegistro()`
- **Problema:** Race condition — dos requests concurrentes pueden generar el mismo `nro_registro`
- **Código:**
  ```php
  $ultimo = static::where('nro_registro', 'like', 'RGTR-' . $anio . '-%')
      ->orderBy('id', 'desc')->first();
  $sigienteNumero = $ultimo ? ((int) str_replace(...) + 1 : 1;
  ```
- **Recomendación:** Usar bloqueo de tabla o generar nro_registro atómico con DB::select + LOCK

---

### DBG-002 · HIGH
- **Archivos:** `database/migrations/2026_05_20_042132_create_detalle_ingresos_table.php`, `2026_05_20_042133_create_detalle_salidas_table.php`
- **Problema:** No hay CHECK constraint en `cantidad_actual_lote` ni `cantidad_salida`
- **Impacto:** Valores negativos pueden entrar a la DB por código bugueado o datos corruptos
- **Recomendación:** Añadir `$table->decimal(..., 12, 2)->unsigned()` y CHECK constraint

---

### DBG-003 · MEDIUM
- **Archivo:** `resources/js/Pages/Salidas/Create.vue:88-95`
- **Método:** `toggleLote(lote)`
- **Problema:** Mutación directa del array reactivo `lotesDisponibles` — anti-pattern en Vue
- **Impacto:** Puede causar inconsistencias en la reactividad de Vue 3
- **Recomendación:** Usar `lotesDisponibles.value = [...]` para crear nueva referencia

---

### DBG-004 · MEDIUM
- **Archivo:** `app/Http/Controllers/SalidaController.php:138`
- **Problema:** La validación solo tiene `['required', 'numeric', 'gt:0']` — no valida que `cantidad_salida <= stock_planta`
- **Impacto:** Un usuario puede enviar una cantidad mayor al stock y romper el inventario
- **Recomendación:** Añadir validación `max:stock_planta` o verificar en el backend

---

### DBG-005 · MEDIUM
- **Archivo:** `app/Http/Controllers/FuncionarioController.php:63-66`
- **Método:** `destroy()`
- **Problema:** El catch block hace fallback deactivate pero retorna mensaje de "éxito"
- **Código:**
  ```php
  } catch (\Exception $e) {
      $funcionario->update(['activo' => false]);
      return redirect()->route('funcionarios.index')
          ->with('success', 'El funcionario tiene registros asociados...'); // ← Éxito
  }
  ```
- **Recomendación:** Usar mensaje diferente (warning) o hacer el redirect->with('warning', ...)

---

### DBG-006 · MEDIUM
- **Archivo:** `app/Http/Controllers/PermisoController.php:64`
- **Método:** `update()`
- **Problema:** No valida que cada `$permission` exista en la lista de `permisosDisponibles`
- **Impacto:** Un request malicioso podría insertar permisos arbitrarios en la BD
- **Recomendación:** Validar que cada permission esté en `in_array($permission, $permisosDisponibles)`

---

### DBG-007 · LOW
- **Archivo:** `app/Http/Controllers/ReporteController.php`
- **Problema:** Queries ejecutadas sin transacción — `buildProjectReport` y `buildDateReport` pueden dejar datos inconsistentes
- **Recomendación:** Envolver queries relacionadas en `DB::transaction()`

---

### DBG-008 · LOW
- **Archivo:** `app/Http/Controllers/ReporteController.php:23-24`
- **Problema:** Usa `->first()?->id` para proyecto/material por defecto sin validar que ese id siga existiendo
- **Recomendación:** Validar que `$selectedProyectoId` y `$selectedMaterialId` existan antes de usar

---

### DBG-009 · LOW
- **Archivos:** Todas las bitácoras (BitácoraActividad::create)
- **Problema:** `$request->ip()` sin validar formato ni proxy X-Forwarded-For
- **Recomendación:** Validar formato IP o usar middleware de confianza de proxy

---

### DBG-010 · LOW
- **Archivo:** `resources/js/Pages/Salidas/Create.vue:292`
- **Problema:** Validación visual de cantidad > stock (clase `text-[#ff8c94]`) pero no previene el submit
- **Recomendación:** El computed `isFormInvalid` debería incluir esta validación

---

## Skill 4: Vercel React Best Practices

**Optimización de rendimiento Vue/React:**

### PERF-001 · CRITICAL
- **Archivo:** `resources/js/Components/CityMap.vue:2-5`
- **Problema:** Leaflet (~250kb) importado al inicio del archivo
- **Impacto:** Bundle de Leaflet carga en TODAS las páginas aunque solo se usa en Dashboard
- **Recomendación:** Usar `defineAsyncComponent` + `<Suspense>` para carga perezosa

---

### PERF-002 · HIGH
- **Archivo:** `resources/js/Pages/Dashboard.vue:20-24`
- **Problema:** `setInterval` cada 2s actualiza 3 refs causando re-renders innecesarios
- **Recomendación:** Reducir frecuencia a 5s+ o usar `requestAnimationFrame` throttleado

---

### PERF-003 · MEDIUM
- **Archivo:** `resources/js/Layouts/AuthenticatedLayout.vue`
- **Problema:** 20+ expresiones `v-if="$page.props.auth.permissions.includes(...)"` recalculan cada render
- **Recomendación:** Crear computed `hasPermission(permiso)` para cachear resultados

---

### PERF-004 · MEDIUM
- **Archivos:** `CityMap.vue`, `WeatherWidget.vue`
- **Problema:** No usan `defineAsyncComponent` para carga perezosa
- **Recomendación:** Envolver en `<Suspense>` con fallback skeleton

---

### PERF-005 · MEDIUM
- **Archivo:** `resources/js/Pages/Reportes/Kardex.vue`
- **Problema:** Arrays grandes sin virtualización
- **Recomendación:** Para >100 filas, usar `vue-virtual-scroller`

---

### PERF-006 · LOW
- **Archivo:** `resources/js/Components/WeatherWidget.vue:33-48`
- **Problema:** `getTrendIcon/getTrendClass` recalculan cada render
- **Recomendación:** Convertir a computed properties

---

### PERF-007 · LOW
- **Archivo:** `resources/js/composables/useWeather.js:91-96`
- **Problema:** `hourlyForecast` hace slice+map cada fetch
- **Recomendación:** Limitar datos a solo lo necesario

---

### PERF-008 · LOW
- **Archivo:** `resources/js/Pages/Dashboard.vue`
- **Problema:** `toLocaleString()` llamado múltiples veces en template
- **Recomendación:** Pre-calcular valores en computed properties

---

### PERF-009 · LOW
- **Archivo:** `resources/js/Components/CityMap.vue:144-147`
- **Problema:** `maxPopulation` calcula `Math.max(...)` en cada acceso
- **Recomendación:** Guardar como computed memoizado

---

### PERF-010 · LOW
- **Archivo:** `resources/js/Pages/Salidas/Create.vue:291`
- **Problema:** Input con `:disabled="lote.selected"` no previene edición
- **Recomendación:** Agregar validación adicional en `isLoteValid`

---

## Skill 5: Improve Codebase Architecture

*Pendiente...*

---

## Plan de Arreglos Priorizados

### Críticos (Arreglar primero)
- [ ] PERF-001: Lazy load de Leaflet (CityMap.vue)
- [ ] ERR-001: Agregar logging a todos los catch blocks
- [ ] BUG-001: Fix race condition en SalidaController::destroy
- [ ] DBG-001: Fix race condition en generarNroRegistro()
- [ ] DBG-002: CHECK constraint para evitar stock negativo
- [ ] ERR-002: Capturar \Throwable en vez de solo \Exception

### Altos (Arreglar esta semana)
- [ ] PERF-002: Reducir frecuencia del setInterval en Dashboard
- [ ] BUG-002: Null check en IngresoController::store
- [ ] BUG-003: Unificar fuente de verdad del stock
- [ ] BUG-004: Agregar Policy/autorización para destroy
- [ ] DBG-004: Validar cantidad_salida <= stock_planta en backend
- [ ] DBG-006: Validar permisos en PermisoController::update
- [ ] PERF-003: Memoizar permission checks en AuthenticatedLayout

### Medios (Siguiente sprint)
- [ ] PERF-004: Agregar defineAsyncComponent a componentes pesados
- [ ] PERF-005: Virtualización en Kardex.vue para tablas grandes
- [ ] ERR-003: Eliminar console.log/alert de debug
- [ ] ERR-004: Reporting de errores client-side
- [ ] ERR-005: Log intentos de acceso denegado
- [ ] BUG-006: Validar IP en bitácora
- [ ] DBG-003: Corregir mutación de array reactivo en Vue
- [ ] DBG-005: Mensaje warning en FuncionarioController::destroy fallback
- [ ] DBG-007: Envolver queries del ReporteController en transacción
- [ ] DBG-008: Validar existencia de proyecto/material por defecto
- [ ] DBG-010: Prevenir submit cuando cantidad > stock

### Bajas (Backlog)
- [ ] PERF-006: Memoizar getTrendIcon/getTrendClass
- [ ] PERF-007: Limitar hourlyForecast en useWeather
- [ ] PERF-008: Pre-calcular toLocaleString en Dashboard
- [ ] PERF-009: Memoizar maxPopulation en CityMap
- [ ] PERF-010: Validación extra en Salidas/Create.vue

---

*Reporte actualizado · 4 skills completadas de 5 · Total: 31 issues*