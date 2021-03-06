<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperationDispatchedColumnToOrdersToProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_to_providers', function (Blueprint $table) {
            $table->boolean('operation_dispatched')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_to_providers', function (Blueprint $table) {
            $table->dropColumn('operation_dispatched');
        });
    }
}
