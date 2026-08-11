<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_registro_crea_un_usuario_y_le_asigna_el_rol_customer(): void
    {
        $response = $this->post('/registro', [
            'name' => 'Ana Rodríguez',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
        ]);

        $user = User::where('email', 'ana@example.com')->first();

        $this->assertTrue($user->hasRole('customer'));

        $response->assertRedirect(route('account.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_un_usuario_registrado_puede_iniciar_sesion(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'cliente@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('account.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_no_puede_iniciar_sesion_con_la_contrasena_incorrecta(): void
    {
        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'cliente@example.com',
            'password' => 'password-mala',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_usuario_autenticado_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_se_limitan_los_intentos_de_inicio_de_sesion(): void
    {
        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => Hash::make('password123'),
        ]);

        Cache::flush();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'cliente@example.com',
                'password' => 'password-mala',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'cliente@example.com',
            'password' => 'password-mala',
        ]);

        $response->assertStatus(429);
    }
}
