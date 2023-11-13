<?php

namespace App\Helpers;

use App\Models\Address;
use App\Models\Client;

class TestHelper
{
    public static function createClientWithAddress()
    {
        $address = Address::factory()->count(1)->create();
        $client = Client::factory()->count(1)->create([
            'address_id' => $address->first()->id
        ]);
        
        return $client->first();
    }
}