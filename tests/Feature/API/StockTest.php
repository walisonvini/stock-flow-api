<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Product;
use App\Models\Stock;

use App\Helpers\TestHelper;

class StockTest extends TestCase
{
    use RefreshDatabase;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = TestHelper::createClientWithAddress();
    }

    public function testGetAllStock()
    {
        $products = Product::factory()->count(15)->create([
            'client_id' => $this->client->id,
        ]);
        
        foreach ($products as $product) {
            Stock::factory()->create([
                'client_id' => $this->client->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->get('/api/stock');

        $response
            ->assertStatus(200)
            ->assertjson([
                'status' => 'Success',
                'stock' => Stock::get()->toArray()
            ]);
    }

    public function testInsertQuantityInStockSuccess()
    {
        $product = Product::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $stock = Stock::factory()->create([
            'client_id' => $this->client->id,
            'product_id' => $product->id,
        ]);

        $stock->quantity += 20; 

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->put('/api/stock/'. $stock->id .'/quantity');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Succes',
                'message' => 'Stock updated successfully',
                'stock' => $stock
            ]);
    }
}
