<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('contract');
            $table->timestamp('request_date')->nullable();
            $table->timestamp('finish_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('description')->nullable();
            $table->decimal('total_cost', 9, 2)->nullable();
            $table->string('status');
            $table->decimal('money_debt', 9, 2)->default(0.0)->nullable();
            $table->unsignedBigInteger('invoice')->nullable();
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
        Schema::dropIfExists('client_orders');
    }
}
