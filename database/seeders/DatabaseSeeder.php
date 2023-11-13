<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Address;
use App\Models\Client;
use App\Models\Product;
use App\Models\Item;
use App\Models\Stock;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $address = Address::factory()->count(1)->create();
        Client::factory()->count(1)->create([
            'address_id' => $address->first()->id
        ]);

        $products = Product::factory()->count(5)->create([
            'client_id' => Client::first()->id
        ]);

        Item::factory()->count(5)->create([
            'product_id' => Product::first()->id
        ]);


        foreach ($products as $product) {
            Stock::factory()->create([
                'client_id' => Client::first()->id,
                'product_id' => $product->id,
            ]);
        }

    }
}
