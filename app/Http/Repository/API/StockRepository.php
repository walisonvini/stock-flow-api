<?php

namespace App\Http\Repository\API;

use App\Models\Client;
use App\Models\Product;
use App\Models\Stock;

class StockRepository 
{
    private $client;
    private $product;
    private $stock;

    public function __construct($api_token)
    {
        $this->client = Client::where('api_token', $api_token)->first();
        $this->product = new Product();
        $this->stock = new Stock();
    }

    public function getAllStock()
    {
        return $this->stock->join('products', 'stock.product_id', '=', 'products.id')
        ->where('products.client_id', $this->client->id)
        ->select('products.sku', 'products.name', 'stock.*')
        ->get();
    }

    public function updateStockQuantity($sku)
    {
        return $this->client->products()->where('sku', $sku)->first();
    }
}