<?php

namespace Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
            'token_type',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        $this->actingAs($user);
    }


//    public function test_user_cannot_register_with_existing_email()
//    {
//
//        $existingUser = User::factory()->create();
//
//        $userData = [
//            'name' => 'Test User 2',
//            'email' => $existingUser->email,
//            'password' => 'password',
//        ];
//
//
//        $response = $this->postJson('/register', $userData);
//
//        $response->assertStatus(422); //  ожидаем код ошибки валидации
//
//        $this->assertDatabaseCount('users', 1); //  проверяем, что новый юзер не был создан
//
//
//    }


}
