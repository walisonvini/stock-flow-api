<?php

namespace App\Http\Repository\API;

use App\Models\Client;

class ProductRepository {
    
    private $client;

    public function __construct($api_token)
    {
        $this->client = Client::where('api_token', $api_token)->first();
    }

    public function getAllProducts()
    {
        return $this->client->products()->get();
    }

    public function createProduct($request)
    {
        return $this->client->products()->create($request->all());
    }

    public function getProduct($id)
    {
        $product = $this->client
            ->products()
            ->where('id', $id)
            ->orWhere('sku', $id)
            ->first();

        return $product ? $product : false;
    }

    public function updateProduct($request, $id)
    {
        $product = $this->client->products()->find($id);
        
        return $product ? $product->update($request->all()) : false;
    }

    public function deleteProduct($id)
    {
        $product = $this->client->products()->find($id);

        return $product? $product->delete() : false;
    }
}