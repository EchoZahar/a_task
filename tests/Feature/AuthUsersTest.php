<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthUsersTest extends TestCase
{
    public function testRegisterNewUser()
    {
        $response = $this->post('/api/register', [
            'name' => 'Test Case User',
            'email' => 'test1@testcase.net',
            "password" => 'password',
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJsonPath('message', "New user created  !");
    }

    public function testLogoutUser()
    {
        Sanctum::actingAs(User::where('email', 'test@testcase.net')->first(), ['limited:token']);
        $response = $this->post('/api/logout');
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJsonPath("message", "Bay, see you later !");
    }

    public function testLoginUser()
    {
        $response = $this->post('/api/login', [
            'email' => 'test@testcase.net',
            'password' => 'password',
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJsonPath('data.user.type', 'customer');
    }
}
