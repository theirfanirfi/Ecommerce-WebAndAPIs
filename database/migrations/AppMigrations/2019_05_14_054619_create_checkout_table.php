<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Checkout', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname');
            $table->string('lastname')->nullable();
            $table->string('address');
            $table->bigInteger('user_id')->unsigned()->default(0);
            $table->string('town');
            $table->string('postalcode');
            $table->string('email')->nullable();
            $table->string('phonenumber');
            $table->integer('is_processed')->default(0);
            $table->integer('products_quantity');
            $table->integer('total_price');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Checkout');
    }
}
