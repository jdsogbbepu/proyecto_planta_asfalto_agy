<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function seedOperadorPermissions(): void
    {
        $permisos = [
            'ver_dashboard',
            'gestionar_materiales',
            'gestionar_proveedores',
            'gestionar_proyectos',
            'gestionar_funcionarios',
            'gestionar_ingresos',
            'gestionar_salidas',
            'ver_reportes',
        ];
        foreach ($permisos as $permiso) {
            RolePermission::create(['role' => 'operador', 'permission' => $permiso]);
        }
    }

    private function seedVisorPermissions(): void
    {
        $permisos = [
            'ver_dashboard',
            'ver_reportes',
        ];
        foreach ($permisos as $permiso) {
            RolePermission::create(['role' => 'visor', 'permission' => $permiso]);
        }
    }

    public function test_administrador_has_access_to_all_routes(): void
    {
        $admin = User::factory()->create(['role' => 'administrador']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_operador_with_permission_can_access_despachos(): void
    {
        $this->seedOperadorPermissions();
        $operador = User::factory()->create(['role' => 'operador']);

        $response = $this->actingAs($operador)->get('/despachos');
        $response->assertStatus(200);
    }

    public function test_operador_without_gestionar_usuarios_cannot_access_users(): void
    {
        $this->seedOperadorPermissions();
        $operador = User::factory()->create(['role' => 'operador']);

        $response = $this->actingAs($operador)->get('/users');
        $response->assertStatus(403);
    }

    public function test_visor_without_gestionar_salidas_cannot_access_despachos_create(): void
    {
        $this->seedVisorPermissions();
        $visor = User::factory()->create(['role' => 'visor']);

        $response = $this->actingAs($visor)->get('/despachos/crear');
        $response->assertStatus(403);
    }

    public function test_visor_can_access_kardex(): void
    {
        $this->seedVisorPermissions();
        $visor = User::factory()->create(['role' => 'visor']);

        $response = $this->actingAs($visor)->get('/kardex');
        $response->assertStatus(200);
    }

    public function test_dynamic_permission_change_takes_effect_immediately(): void
    {
        $this->seedVisorPermissions();
        $visor = User::factory()->create(['role' => 'visor']);

        $this->actingAs($visor)->get('/despachos')->assertStatus(403);

        RolePermission::create(['role' => 'visor', 'permission' => 'gestionar_salidas']);

        $this->actingAs($visor)->get('/despachos')->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}