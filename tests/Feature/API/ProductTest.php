<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Helpers\TestHelper;

use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = TestHelper::createClientWithAddress();
    }

    public function testGetAllProducts()
    {
        $products = Product::factory()->count(15)->create([
            'client_id' => $this->client->id,
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->getJson('/api/product');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'products' => $products->toArray()
            ])
            ->assertJsonStructure([
                'status',
                'products' => [
                    '*' => [
                        'id',
                        'sku',
                        'name',
                        'price',
                        'description',
                        'category',
                        'updated_at',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function testCreateProduct()
    {
        $product = Product::factory()->make()->toArray();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/product', $product);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'Success',
                'message' => 'Product created successfully',
                'product' => $product
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'product' => [
                    'id',
                    'sku',
                    'name',
                    'price',
                    'description',
                    'category',
                    'updated_at',
                    'created_at'
                ]
            ]);

        $this->assertDatabaseHas('products', $product);
    }

    public function testProductWithDuplicateSkuIsRejected()
    {
        $product = Product::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $newProductData = Product::factory()->make()->toArray();
        $newProductData['sku'] = $product->sku;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/product', $newProductData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'Error',
                'data' => [
                    'sku' => [
                        'The sku has already been taken.'
                    ]
                ]
            ]);
    }

    public function testRequireFieldsForCreateProduct()
    {
        $product = [
            'description' => 'description product',
            'category' => 'category product'
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/product', $product);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'Error',
                'data' => [
                    'sku' => [
                        'The sku field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ],
                    'price' => [
                        'The price field is required.'
                    ]
                ]
            ]);
    }

    public function testFieldsIsRejectedForCreateProduct()
    {
        $invalidProductData = [
            'sku' => 'Fdsf3DSD#$343fdDfd4eGHvdf',
            'name' => 'Marvelous Midnight Black Velvety Earrings for Elegance',
            'price' => '$55.90',
            'description' => bin2hex(random_bytes(130)),
            'category' => bin2hex(random_bytes(30))
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/product', $invalidProductData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'Error',
                'data' => [
                    'sku' => [
                        'The sku must not be greater than 20 characters.'
                    ],
                    'name' => [
                        'The name must not be greater than 50 characters.'
                    ],
                    'price' => [
                        'The price must be a number.'
                    ],
                    'description' => [
                        'The description must not be greater than 255 characters.'
                    ],
                    'category' => [
                        'The category must not be greater than 50 characters.'
                    ]
                ]
            ]);
    }

    public function testGetProduct()
    {
        $product = Product::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->api_token,
        ];

        $responseById = $this->withHeaders($headers)->getJson('/api/product/' . $product->id);
        $responseBySku = $this->withHeaders($headers)->getJson('/api/product/' . $product->sku);

        $responseById
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'product' => $product->toArray()
            ])
            ->assertJsonStructure([
                'status',
                'product' => [
                    'id',
                    'sku',
                    'name',
                    'price',
                    'description',
                    'category',
                    'updated_at',
                    'created_at'
                ]
            ]);

        $responseBySku
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'product' => $product->toArray()
            ])
            ->assertJsonStructure([
                'status',
                'product' => [
                    'id',
                    'sku',
                    'name',
                    'price',
                    'description',
                    'category',
                    'updated_at',
                    'created_at'
                ]
            ]);
    }

    public function testClientOnlyGetsHisRegisteredProducts()
    {
        $client = TestHelper::createClientWithAddress();

        Product::factory()->create([
            'client_id' => $client->id,
        ]);

        $otherClient = TestHelper::createClientWithAddress();

        $otherProduct = Product::factory()->create([
            'client_id' => $otherClient->id,
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $client->first()->api_token,
        ])->getJson('/api/product/' . $otherProduct->id);

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error',
                'message' => 'Product not found'
            ]);
    }

    public function testUpdateProduct()
    {
        $product = Product::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $updatedData = [
            'sku' => 'New sku',
            'name' => 'New name',
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->putJson('/api/product/' . $product->id, $updatedData);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'message' => 'Product updated successfully'
            ])
            ->assertJsonStructure([
                'status',
                'message'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => 'New sku',
            'name' => 'New name',
        ]);
    }

    public function testDeleteProduct()
    {
        $product = Product::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->deleteJson('/api/product/' . $product->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'Success'
            ])
            ->assertJsonStructure([
                'status'
            ]);
        
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
