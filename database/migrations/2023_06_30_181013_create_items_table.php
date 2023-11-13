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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 100)->unique()->nullable(false);
            $table->date('expiration_date');
            $table->string('shelf', 2)->nullable(false);
            $table->string('aisle', 2)->nullable(false);
            $table->string('level', 2)->nullable(false);
            $table->string('condition', 50);
            $table->string('status', 20)->nullable(false);
            $table->unsignedBigInteger("product_id")->nullable(false);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
