<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Helpers\TestHelper;

use App\Models\Item;
use App\Models\Product;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    private $client;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = TestHelper::createClientWithAddress();

        $this->product = Product::factory()->create([
            'client_id' => $this->client->first()->id
        ]);
    }

    public function testCreateItem()
    {
        $item = Item::factory()->make([
            'product_id' => $this->product->first()->id
        ])->toArray();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/item', $item);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'Success',
                'message' => 'Item created successfully',
                'item' => $item
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'item' => [
                    'id',
                    'product_id',
                    'expiration_date',
                    'shelf',
                    'aisle',
                    'level',
                    'condition',
                    'status',
                    'identifier',
                    'updated_at',
                    'created_at'
                ]
            ]);

        $this->assertDatabaseHas('items', $item);
    }

    public function testItemWithDuplicateIdentifierIsRejected()
    {
        $item = Item::factory()->create([
            'product_id' => $this->product->first()->id
        ]);

        $newItemData = Item::factory()->make(['product_id' => $this->product->first()->id])->toArray();
        $newItemData['identifier'] = $item->identifier;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/item', $newItemData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'Error',
                'data' => [
                    'identifier' => [
                        'The identifier has already been taken.'
                    ]
                ]
            ]);
    }

    public function testRequireFieldsForCreateItem()
    {
        $item = [
            'condition' => 'item condition'
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/item', $item);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'Error',
                'data' => [
                    'identifier' => [
                        'The identifier field is required.'
                    ],
                    'shelf' => [
                        'The shelf field is required.'
                    ],
                    'aisle' => [
                        'The aisle field is required.'
                    ],
                    'level' => [
                        'The level field is required.'
                    ],
                    'status' => [
                        'The status field is required.'
                    ],
                    'product_id' => [
                        'The product id field is required.'
                    ]
                ]
            ]);
    }

    public function testFieldsIsRejectedForCreateItem()
    {
        $invalidItemData = [
            'identifier' => bin2hex(random_bytes(130)),
            'expiration_date' => '',
            'shelf' => bin2hex(random_bytes(3)),
            'aisle' => bin2hex(random_bytes(3)),
            'level' => bin2hex(random_bytes(3)),
            'condition' => bin2hex(random_bytes(26)),
            'status' => bin2hex(random_bytes(12)),
            'product_id' => 2
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->postJson('/api/item', $invalidItemData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'Error',
                'data' => [
                    'identifier' => [
                        'The identifier must not be greater than 100 characters.'
                    ],
                    'expiration_date' => [
                        'The expiration date does not match the format Y-m-d.'
                    ],
                    'shelf' => [
                        'The shelf must not be greater than 2 characters.'
                    ],
                    'aisle' => [
                        'The aisle must not be greater than 2 characters.'
                    ],
                    'level' => [
                        'The level must not be greater than 2 characters.'
                    ],
                    'status' => [
                        'The status must not be greater than 20 characters.'
                    ]
                ]
            ]);
    }

    public function testGetAllItems()
    {
        $item = Item::factory()->count(10)->create([
            'product_id' => $this->product->id
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->getJson('/api/item/' . $this->product->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'items' => $item->toArray()
            ])
            ->assertJsonStructure([
                'status',
                'items' => [
                    '*' => [
                        'id',
                        'identifier',
                        'product_id',
                        'expiration_date',
                        'shelf',
                        'aisle',
                        'level',
                        'condition',
                        'status',
                        'updated_at',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function testGetItem()
    {
        $item = Item::factory()->create([
            'product_id' => $this->product->id
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->getJson('/api/item/unique/' . $item->identifier);

        $response->assertStatus(200);

        $response
            ->assertJson([
                'status' => 'Success',
                'item' => $item->toArray()
            ])
            ->assertJsonStructure([
                'status',
                'item' => [
                    'id',
                    'identifier',
                    'product_id',
                    'expiration_date',
                    'shelf',
                    'aisle',
                    'level',
                    'condition',
                    'status',
                    'updated_at',
                    'created_at'
                ]
            ]);
    }

    public function testClientOnlyGetsHisRegisteredItems()
    {
        Item::factory()->create([
            'product_id' => $this->product->id,
        ]);

        $otherClient = TestHelper::createClientWithAddress();

        $otherProduct = Product::factory()->create([
            'client_id' => $otherClient->id,
        ]);

        $outherItem = Item::factory()->create([
            'product_id' => $otherProduct->id,
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->getJson('/api/item/unique/' . $outherItem->identifier);

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error',
                'message' => 'Item not found'
            ]);
    }

    public function testUpdateItem()
    {
        $item = Item::factory()->create([
            'product_id' => $this->product->id
        ]);

        $updatedData = [
            'level' => '02',
            'status' => 'available',
            'product_id' => $item->product_id
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->putJson('/api/item/' . $item->identifier, $updatedData);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'item' => 'Item updated successfully'
            ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'level' => '02',
            'status' => 'available',
            'product_id' => $item->product_id,
        ]);
    }

    public function testDeleteItem()
    {
        $item = Item::factory()->create([
            'product_id' => $this->product->id
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->client->first()->api_token,
        ])->deleteJson('/api/item/' . $item->identifier);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'message' => 'Item deleted successfully'
            ])
            ->assertJsonStructure([
                'status',
                'message'
            ]);

        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }
}
