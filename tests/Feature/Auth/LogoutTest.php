<?php


namespace Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{

    use RefreshDatabase;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'test-token']);

        $response = $this->withToken($token)->postJson('/api/logout');

        $response->assertStatus(200)->assertJson(['message' => 'Выход из системы прошёл успешно']);
        $this->assertDatabaseMissing('personal_access_tokens', ['name' => 'test-token']);
        $this->withToken($token)->getJson('/api/user')->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {

        $this->postJson('/api/logout')->assertStatus(401);

    }
}
