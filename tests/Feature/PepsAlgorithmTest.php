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
