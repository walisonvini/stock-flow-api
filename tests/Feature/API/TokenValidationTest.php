<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\TestHelper;

use Tests\TestCase;

class TokenValidationTest extends TestCase
{
    use RefreshDatabase;

    public function testValidTokenAccess()
    {
        $client = TestHelper::createClientWithAddress();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $client->first()->api_token,
        ])->getJson('/api/client');
        
        $response->assertStatus(200);
    }

    public function testTokenNotFoundAccess()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
        ])->getJson('/api/client');

        $response->assertStatus(404)
                 ->assertJson([
            'status' => 'Error',
            'message' => 'Token not found'
        ]);
    }

    public function testInvalidTokenAccess()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'YshzZNDu5nC3wXBxdON5jtm7JyZN7r',
        ])->getJson('/api/client');

        $response->assertStatus(422)
                 ->assertJson([
            'status' => 'Error',
            'message' => 'Invalid token'
        ]);
    }
}
