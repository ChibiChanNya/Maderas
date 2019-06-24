<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersToProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_to_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id')->index();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->timestamp('request_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('description')->nullable();
            $table->decimal('total_cost', 9, 2)->nullable();
            $table->string('status');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->decimal('remaining_cost', 9, 2);
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
        Schema::dropIfExists('orders_to_providers');
    }
}
