<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOrdersDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_orders_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('client_orders')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('units');
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
        Schema::dropIfExists('client_orders_details');
    }
}
