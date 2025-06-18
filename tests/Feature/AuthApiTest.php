<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'ahmad User',
            'email' => 'ahmad@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'access_token',
                     'token_type'
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@gmail.com',
        ]);
    }

    public function test_user_can_login_via_api()
    {
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => Hash::make('user4321'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@gmail.com',
            'password' => 'user4321',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'access_token',
                     'token_type'
                 ]);
    }

    // public function test_login_with_invalid_credentials()
    // {
    //     $user = User::factory()->create([
    //         'email' => 'user@gmail.com',
    //         'password' => Hash::make('user4321'),
    //     ]);

    //     $response = $this->postJson('/api/login', [
    //         'email' => 'user@gmail.com',
    //         'password' => 'wawawawwa',
    //     ]);

    //     $response->assertStatus(401)
    //              ->assertJson([
    //                  'message' => 'Invalid credentials',
    //              ]);
    // }
}
