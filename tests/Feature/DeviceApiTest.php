<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_list_their_devices()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Device::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/devices');

        $response->assertStatus(200)
                 ->assertJsonStructure(['devices']);
    }

    /** @test */
    public function user_can_create_device()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Sensor PH',
            'description' => 'Untuk memantau pH air',
            'location' => 'Kolam A'
        ];

        $response = $this->postJson('/api/devices', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'device',
                     'device_code'
                 ]);

        $this->assertDatabaseHas('devices', [
            'name' => 'Sensor PH',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function user_can_view_specific_device_and_its_readings()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $device = Device::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/devices/{$device->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'device',
                     'readings'
                 ]);
    }

    /** @test */
    public function user_can_update_their_device()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $device = Device::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Device'
        ]);

        $response = $this->putJson("/api/devices/{$device->id}", [
            'name' => 'Updated Device',
            'description' => 'Updated desc',
            'location' => 'Updated location'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Device']);

        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'name' => 'Updated Device'
        ]);
    }

    /** @test */
    public function user_can_delete_their_device()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $device = Device::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/devices/{$device->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Device deleted successfully.']);

        $this->assertDatabaseMissing('devices', ['id' => $device->id]);
    }
}
