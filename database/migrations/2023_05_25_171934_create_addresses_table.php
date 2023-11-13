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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string("street", 255)->nullable(false);
            $table->integer("number")->nullable(false);
            $table->string("neighborhood", 255)->nullable(false);
            $table->string("city", 255)->nullable(false);
            $table->string("state", 255)->nullable(false);
            $table->string("country", 255)->nullable(false);
            $table->string("postal_code", 20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
