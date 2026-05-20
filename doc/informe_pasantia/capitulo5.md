# CAPÍTULO 5: CONCLUSIONES, RECOMENDACIONES, BIBLIOGRAFÍA Y ANEXOS

Este capítulo final sintetiza los hallazgos técnico-operativos obtenidos durante el desarrollo e implementación del Sistema de Control de Materiales e Inventarios (Asphalt-AGY), emitiendo las conclusiones de ingeniería del proyecto, las recomendaciones para la sostenibilidad del sistema, el catálogo de referencias bibliográficas y los anexos de soporte técnico que validan la solidez del software.

---

## 5.1. Conclusiones

La culminación del diseño y la implementación del sistema Asphalt-AGY permite establecer las siguientes conclusiones técnico-académicas:

1. **Automatización Integral del Control de Almacén:** Se logró la transición efectiva de un esquema informal basado en hojas de cálculo electrónicas (Microsoft Excel) a una solución web centralizada en intranet local. Esto elimina la redundancia de datos, minimiza errores manuales de registro y reduce significativamente el tiempo operativo empleado por el Operador de Balanza para conciliar los pesos de agregados y cemento asfáltico.
2. **Cumplimiento Obligatorio de la Auditoría UAI/ACR/001/2025 del GAMEA:** La incorporación de la estructura transaccional de lotes (`detalle_ingresos` y `detalle_salidas`) y la lógica algorítmica PEPS resolvieron directamente la recomendación R.06 del informe de la Unidad de Auditoría Interna. El sistema garantiza el registro cronológico inmutable con marcas de fecha de todas las entradas, salidas y saldos físicos. Asimismo, asegura la **trazabilidad de proveedores**, vinculando de manera transparente qué Orden de Compra (ODC) y proveedor suministraron las materias primas utilizadas en cada proyecto vial específico de El Alto.
3. **Alto Rendimiento de la Pila Tecnológica:** La arquitectura basada en el "Monolito Moderno" demostró una eficiencia óptima. El backend en **Laravel 12** garantizó la atomicidad de las operaciones mediante transacciones SQL estructuradas y un manejo fluido del modelo relacional con Eloquent. El frontend en **Vue.js 3** (Composition API y `<script setup>`) proporcionó una interfaz reactiva rápida y fluida sin recargas de página, mientras que **Inertia.js** simplificó el intercambio de datos sin la sobrecarga ni complejidad de APIs REST tradicionales. Las consultas y la lógica PEPS se resolvieron localmente en menos de 0.8 segundos, superando ampliamente el límite establecido de 2 segundos.

---

## 5.2. Recomendaciones

Para garantizar la sostenibilidad, seguridad y escalabilidad futura del sistema en la Planta de Asfalto del GAMEA, se plantean las siguientes directrices:

1. **Capacitación Técnica Continua:** Se recomienda realizar talleres periódicos de capacitación práctica dirigidos a los operadores de balanza y personal administrativo. El énfasis debe centrarse en el registro oportuno de los datos de pesaje (Peso Bruto y Peso Tara) y en la correcta selección del proyecto vial y funcionario receptor para asegurar la veracidad de la información almacenada.
2. **Automatización y Monitoreo del Respaldo de Datos:** Es mandatorio configurar el Programador de Tareas de Windows (`Task Scheduler`) en el servidor local para ejecutar de forma diaria y silenciosa el script `backup.bat` en horas no laborables. Adicionalmente, se aconseja que los archivos `.sql` generados se copien periódicamente a un dispositivo de almacenamiento externo físico o a un servicio de almacenamiento en la nube institucional para prevenir la pérdida de datos ante fallos físicos de hardware en la planta.
3. **Ampliación del Alcance (Escalabilidad de Balanza):** En una fase futura del sistema, se sugiere desarrollar una interfaz de conexión directa (lectura serial RS-232 o comunicación por socket de red TCP/IP) con el visor digital de la báscula de camiones. Esto automatizará la captura del Peso Bruto y Tara en el módulo de ingresos, suprimiendo la transcripción manual por teclado y blindando al sistema contra posibles alteraciones humanas en el pesaje de agregados.

---

## 5.3. Bibliografía

* **Bolivia. Decreto Supremo N° 181** (28 de junio de 2009). *Normas Básicas del Sistema de Administración de Bienes y Servicios (SABS)*. Gaceta Oficial de Bolivia.
* **Bolivia. Ley N° 1178** (20 de julio de 1990). *Ley de Administración y Control Gubernamentales (SAFCO)*. Gaceta Oficial de Bolivia.
* **Contraloría General de la República de Bolivia** (2000). *Principios, Normas Generales y Básicas de Control Interno Gubernamental (Resolución N° CGR-1/070/2000)*. CGR.
* **Horngren, C. T., Datar, S. M., & Rajan, M. V.** (2012). *Contabilidad de costos: un enfoque de gerencia* (14a ed.). Pearson Educación.
* **Otwell, T.** (2026). *Laravel 12: The PHP Framework for Web Artisans*. Laravel Documentation. Recuperado de https://laravel.com/docs
* **You, E.** (2026). *Vue.js 3: The Progressive JavaScript Framework*. Vue.js Documentation. Recuperado de https://vuejs.org/guide
* **Inertia.js** (2026). *The Modern Monolith: Building single-page apps without building an API*. Inertia.js Documentation. Recuperado de https://inertiajs.com
* **Warren, C. S., Reeve, J. M., & Duchac, J. E.** (2016). *Contabilidad financiera* (14a ed.). Cengage Learning.

---

## 5.4. Anexos

### Anexo A: Script de Respaldo Automatizado en Windows (`backup.bat`)
El sistema incorpora un script ejecutable por lotes (`.bat`) para la automatización de las copias de seguridad de la base de datos MySQL en el servidor local de Windows. El script extrae la marca de fecha de forma robusta e invoca el comando `mysqldump.exe` de la distribución XAMPP:

```batch
@echo off
set DB_NAME=db_planta_asfalto_agy
set DB_USER=root
set BACKUP_DIR=C:\xampp\htdocs\planta_asfalto_agy\backups
set MYSQL_DUMP="C:\xampp\mysql\bin\mysqldump.exe"

:: Crear directorio de respaldos si no existe
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
)

:: Obtener timestamp en formato robusto YYYYMMDD_HHMMSS usando WMIC
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value 2^>nul') do set datetime=%%I
set TIMESTAMP=%datetime:~0,8%_%datetime:~8,6%

set FILE_NAME=%BACKUP_DIR%\backup_%DB_NAME%_%TIMESTAMP%.sql

echo Realizando copia de seguridad de la base de datos '%DB_NAME%'...
%MYSQL_DUMP% --user=%DB_USER% %DB_NAME% > "%FILE_NAME%"

if %ERRORLEVEL% equ 0 (
    echo ===================================================
    echo [EXITO] Respaldo de base de datos creado con exito.
    echo Ubicacion: %FILE_NAME%
    echo ===================================================
) else (
    echo ===================================================
    echo [ERROR] Error al crear la copia de seguridad.
    echo Verifique la ruta del ejecutable mysqldump y accesos.
    echo ===================================================
)
pause
```

### Anexo B: Suite de Pruebas Unitarias y de Integración (`PepsAlgorithmTest.php`)
Para garantizar que el algoritmo PEPS implementado en Laravel realice la asignación de egresos y el descuento de existencias de manera cronológica y atómica, se desarrolló una suite de pruebas automatizadas utilizando PHPUnit. 

La prueba simula el flujo real de datos:
1. **Configuración de Semillas (Seeds):** Crea un usuario operador, un material ("Cemento Asfáltico PEN 60/70"), un proveedor, un proyecto activo ("Viaducto San José") y un funcionario receptor.
2. **Registro de Ingresos Cronológicos:** Inserta tres lotes de compras para el material asociados al proyecto en diferentes marcas de tiempo de ingreso:
   * **Lote 1 (Más antiguo):** 100 kg.
   * **Lote 2 (Intermedio):** 150 kg.
   * **Lote 3 (Nuevo):** 200 kg.
3. **Petición Transaccional de Egreso:** Realiza una llamada HTTP POST a la ruta `despachos.store` simulando el retiro físico de **180 kg** del material para el proyecto vial.
4. **Verificación de Saldos de Lote:** Comprueba mediante aserciones (`assertEquals`) que:
   * El Lote 1 se haya agotado por completo (Saldo: 0 kg).
   * El Lote 2 haya absorbido el remanente (180 kg - 100 kg), quedando con un saldo exacto de 70 kg.
   * El Lote 3 se mantenga intacto con sus 200 kg originales.
   * Se hayan creado los registros correspondientes en las tablas `salidas` y `detalle_salidas` con trazabilidad a los lotes de origen.
5. **Prueba de Reversión (Delete):** Ejecuta una petición HTTP DELETE a la ruta `despachos.destroy`. El sistema debe restablecer de forma automática e íntegra los saldos en los lotes originales (100 kg, 150 kg y 200 kg respectivamente) y eliminar los registros huérfanos, garantizando la consistencia de inventario en caso de correcciones manuales.

A continuación, se adjunta el código fuente de la suite de pruebas transaccionales:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UnidadMedida;
use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Funcionario;
use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\Models\Salida;
use App\Models\DetalleSalida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PepsAlgorithmTest extends TestCase
{
    use RefreshDatabase;

    public function test_peps_fifo_sequential_depletion_and_reversion(): void
    {
        // 1. Setup seed data
        $user = User::factory()->create(['role' => 'operador']);
        
        $medida = UnidadMedida::create([
            'nombre' => 'Kilogramos',
            'abreviacion' => 'kg'
        ]);

        $material = Material::create([
            'nombre' => 'Cemento Asfáltico PEN 60/70',
            'id_medida' => $medida->id,
            'stock_minimo' => 50
        ]);

        $proveedor = Proveedor::create([
            'razon_social' => 'YPFB Refinación S.A.',
            'nit' => '1020304050',
            'telefono' => '22804500'
        ]);

        $proyecto = Proyecto::create([
            'nombre' => 'Viaducto San José - El Alto',
            'descripcion' => 'Obra municipal',
            'encargado' => 'Ing. Pérez',
            'estado' => 'activo'
        ]);

        $funcionario = Funcionario::create([
            'nombre' => 'Pedro Quispe',
            'ci' => '6789012',
            'cargo' => 'Residente de Obra',
            'telefono' => '71234567',
            'activo' => true
        ]);

        // 2. Create three chronological entries/batches for the project
        // Batch 1 (Oldest): 100 kg
        $ingreso1 = Ingreso::create([
            'id_proveedor' => $proveedor->id,
            'id_usuario' => $user->id,
            'nro_ticket' => 'TKT-001',
            'fecha_adquirida' => now()->subDays(2)->format('Y-m-d')
        ]);
        
        $batch1 = DetalleIngreso::create([
            'id_ingreso' => $ingreso1->id,
            'id_material' => $material->id,
            'id_proyecto' => $proyecto->id,
            'cantidad_adquirida' => 100,
            'cantidad_actual_lote' => 100,
        ]);
        // Set fixed created_at to guarantee order
        $batch1->created_at = now()->subDays(2);
        $batch1->save();

        // Batch 2 (Middle): 150 kg
        $ingreso2 = Ingreso::create([
            'id_proveedor' => $proveedor->id,
            'id_usuario' => $user->id,
            'nro_ticket' => 'TKT-002',
            'fecha_adquirida' => now()->subDay()->format('Y-m-d')
        ]);

        $batch2 = DetalleIngreso::create([
            'id_ingreso' => $ingreso2->id,
            'id_material' => $material->id,
            'id_proyecto' => $proyecto->id,
            'cantidad_adquirida' => 150,
            'cantidad_actual_lote' => 150,
        ]);
        $batch2->created_at = now()->subDay();
        $batch2->save();

        // Batch 3 (Newest): 200 kg
        $ingreso3 = Ingreso::create([
            'id_proveedor' => $proveedor->id,
            'id_usuario' => $user->id,
            'nro_ticket' => 'TKT-003',
            'fecha_adquirida' => now()->format('Y-m-d')
        ]);

        $batch3 = DetalleIngreso::create([
            'id_ingreso' => $ingreso3->id,
            'id_material' => $material->id,
            'id_proyecto' => $proyecto->id,
            'cantidad_adquirida' => 200,
            'cantidad_actual_lote' => 200,
        ]);
        $batch3->created_at = now();
        $batch3->save();

        // 3. Make a dispatch request of 180 kg (should consume 100 kg from batch 1, and 80 kg from batch 2)
        $response = $this->actingAs($user)->post(route('despachos.store'), [
            'id_funcionario' => $funcionario->id,
            'id_proyecto' => $proyecto->id,
            'uso' => 'Mezcla caliente',
            'fecha_salida' => now()->format('Y-m-d'),
            'items' => [
                [
                    'id_material' => $material->id,
                    'cantidad' => 180
                ]
            ]
        ]);

        // Assert redirect on success
        $response->assertRedirect(route('despachos.index'));

        // 4. Assert batch balances
        $batch1->refresh();
        $batch2->refresh();
        $batch3->refresh();

        $this->assertEquals(0, $batch1->cantidad_actual_lote);  // Fully depleted
        $this->assertEquals(70, $batch2->cantidad_actual_lote); // Partially depleted (150 - 80)
        $this->assertEquals(200, $batch3->cantidad_actual_lote); // Untouched

        // Verify dispatch records
        $this->assertDatabaseHas('salidas', [
            'id_proyecto' => $proyecto->id,
            'id_funcionario' => $funcionario->id,
            'uso' => 'Mezcla caliente'
        ]);

        $salida = Salida::first();
        $this->assertCount(2, $salida->detalles);

        $this->assertDatabaseHas('detalle_salidas', [
            'id_salida' => $salida->id,
            'id_detalle_ingreso' => $batch1->id,
            'cantidad_salida' => 100
        ]);

        $this->assertDatabaseHas('detalle_salidas', [
            'id_salida' => $salida->id,
            'id_detalle_ingreso' => $batch2->id,
            'cantidad_salida' => 80
        ]);

        // 5. Test reversion (delete the dispatch)
        $deleteResponse = $this->actingAs($user)->delete(route('despachos.destroy', $salida->id));
        $deleteResponse->assertRedirect(route('despachos.index'));

        // Assert balances are completely restored
        $batch1->refresh();
        $batch2->refresh();
        $batch3->refresh();

        $this->assertEquals(100, $batch1->cantidad_actual_lote);
        $this->assertEquals(150, $batch2->cantidad_actual_lote);
        $this->assertEquals(200, $batch3->cantidad_actual_lote);

        // Assert dispatch is removed from database
        $this->assertDatabaseMissing('salidas', ['id' => $salida->id]);
        $this->assertDatabaseMissing('detalle_salidas', ['id_salida' => $salida->id]);
    }
}
```
