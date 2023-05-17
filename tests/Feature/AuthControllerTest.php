<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use MakesHttpRequests;

    /**
     * User data array
     * @var array
     */
    public array $userData;

    public function setUp(): void
    {
        parent::setUp();

        $this->userData = [
            'firstname' => 'Test',
            'lastname' => 'Test',
            'birthDate' => '2002-05-02',
            'email' => 'test@gmail.com',
            'password' => '11111111',
        ];
    }

    public function test_user_register()
    {
        $user = User::where('email', $this->userData['email'])->first();

        $response = $this->postJson('/api/auth/register', $this->userData);

        if ($user) {
            $response
                ->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'email'
                    ]
                ]);

            $this->assertGuest('api');
        } else {
            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'token'
                ]);

            $this->assertAuthenticated('api');
        }
    }
}
