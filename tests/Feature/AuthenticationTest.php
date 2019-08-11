<?php

namespace Tests\Feature;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $user = new User(['name' => 'Test account', 'email' => 'fake@email.com', 'phone_number' => '08123569321', 'address' => '7227 Bleecker Street', 'password' => 'secret']);
        $user->save();
    }
    /**
     * A basic registration test.
     *
     * @test
     */
    public function it_will_register_a_user()
    {
        $response = $this->post('api/register', ['name' => 'Fake 2', 'email' => 'fake2@email.com', 'phone_number' => '08125569321', 'address' => '7227 Bleecker Street', 'password' => 'secret']);
        $response->assertJsonStructure(['success', 'token',]);
    }

    /**
     * A basic login test
     *
     * @test
     *
     *
     * */
    public function it_will_log_a_user_in()
    {
        $response = $this->post('api/login', ['email' => 'fake@email.com', 'password' => 'secret']);
        $response->assertJsonStructure(['success', 'token',]);
    }

    /**
     * Basic Unauthorized user login test
     *
     * @test
     *
     * */
    public function it_will_not_log_in_a_non_validated_user()
    {
        $response = $this->post('api/login', ['email' => 'fake@email.com', 'password' => 'secretive']);
        $response->assertJsonStructure(['success', 'message',]);
    }
}
