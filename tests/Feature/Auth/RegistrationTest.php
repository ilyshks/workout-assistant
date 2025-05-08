<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password12345!',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)->assertJsonStructure([
            'data' =>[
            'access_token',
            'token_type']
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        $this->actingAs($user);
    }

    public function test_user_cannot_register_with_invalid_email(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password12345!',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    #[DataProvider('invalidPasswords')]
    public function test_user_cannot_register_with_weak_password(string $password)
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'password' => $password,

        ];

        $response = $this->postJson('/api/v1/register', $userData);
        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public static function invalidPasswords(): array
    {

        return [
            'Too short' => ['short'],
            'No uppercase' => ['password123'],
            'No lowercase' => ['PASSWORD123'],
            'No number' => ['Password!'],
            'No special character' => ['Password123'],
        ];

    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        $existingUser = User::factory()->create();

        $userData = [
            'name' => 'Test User 2',
            'email' => $existingUser->email,
            'password' => 'P@$$w0rd1',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_without_required_fields() {
        $response = $this->postJson('/api/v1/register', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['name',  'email', 'password']);

    }


}
