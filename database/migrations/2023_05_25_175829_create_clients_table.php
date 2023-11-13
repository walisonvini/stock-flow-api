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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string("name", 50)->nullable(false);
            $table->string("tax_id", 14)->unique()->nullable(false);
            $table->string("phone", 30)->nullable(false);
            $table->string("api_token", 30)->unique()->nullable(false);
            $table->unsignedBigInteger("address_id")->unique()->nullable(false);
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
