<?php

namespace App\Http\Repository\API;

use App\Models\Client;
use App\Models\Item;

class ItemRepository {
    
    protected $client;
    private $item;

    public function __construct($api_token)
    {
        $this->client = Client::where('api_token', $api_token)->first();
        $this->item = new Item();
    }

    private function getProduct($product_id)
    {
        return $this->client->products()->find($product_id);
    }

    private function getItemByToken($identifier)
    {
        return $this->client->products()
                        ->join('items', 'products.id', '=', 'items.product_id')
                        ->where('items.identifier', $identifier)
                        ->select('items.*')
                        ->first() ?? false;
    }

    public function createItem($request) 
    {
        $product = $this->getProduct($request->product_id);

        if($product) return $this->item->create($request->all());
            
        return false;
    }

    public function getAllItems($product_id)
    {
        $product = $this->getProduct($product_id);

        if($product) return $this->item->where('product_id', $product_id)->get();

        return false;
    }

    public function getItem($identifier)
    {
        return $this->getItemByToken($identifier);
    }

    public function updateItem($identifier, $request)
    {
        $item = $this->getItemByToken($identifier);

        if($item) return $this->item->where('identifier', $identifier)->update($request->all());

        return false;
    }

    public function deleteItem($identifier)
    {
        $item = $this->getItemByToken($identifier);

        if($item) return $this->item->where('identifier', $identifier)->delete();
        
        return false;
    }
}