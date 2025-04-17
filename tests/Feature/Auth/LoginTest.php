<?php

namespace Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $loginData = ['email' => $user->email, 'password' => 'password'];
        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token',  'token_type']);
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $loginData = ['email' => $user->email, 'password' => 'wrong-password'];
        $response = $this->postJson('/api/v1/login', $loginData);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_login_with_incorrect_email(): void
    {
        $loginData = ['email' => 'wrong-email', 'password' => 'password'];

        $this->postJson('/api/v1/login',  $loginData)->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    }

    public function test_user_cannot_login_without_required_fields(): void
    {
        $this->postJson('/api/v1/login', [])->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

    }
}
