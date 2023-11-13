<?php

namespace App\Http\Repository\API;

use App\Models\Client;

class ClientRepository {
    
    protected $client;

    public function __construct($api_token)
    {
        $this->client = Client::where('api_token', $api_token)->first();
    }

    public function getClientWithAddress() 
    {
        return $this->client->with('address')->first();
    }
}