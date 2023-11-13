<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("sku", 20)->unique()->nullable(false);
            $table->string("name", 50)->nullable(false);
            $table->decimal("price", 12, 2)->nullable(false);
            $table->string("description", 255);
            $table->string("category", 50);
            $table->unsignedBigInteger("client_id")->nullable(false);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
