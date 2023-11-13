<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Client;
use App\Models\Address;

class ClientTest extends TestCase
{
    use RefreshDatabase;
  
    public function testGetClientWithAddress()
    {
        $address = Address::factory()->create();

        $client = Client::factory()->create([
            'address_id' => $address->first()->id
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $client->first()->api_token,
        ])->getJson('/api/client');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'client' => [
                    'name' => $client->first()->name,
                    'tax_id' => $client->first()->tax_id,
                    'phone' => $client->first()->phone,
                    'address' => [
                        'street' => $address->first()->street,
                        'number' => $address->first()->number,
                        'neighborhood' => $address->first()->neighborhood,
                        'city' => $address->first()->city,
                        'state' => $address->first()->state,
                        'country' => $address->first()->country,
                        'postal_code' => $address->first()->postal_code,
                    ],
                ]
            ])
            ->assertJsonStructure([
                'status',
                'client' => [
                    'name',
                    'tax_id',
                    'phone',
                    'address' => [
                        'street',
                        'number',
                        'neighborhood',
                        'city',
                        'state',
                        'country',
                        'postal_code',
                    ],
                ]
            ]);
    }
}
