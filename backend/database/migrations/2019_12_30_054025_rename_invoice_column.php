<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameInvoiceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_orders', function (Blueprint $table) {
            $table->renameColumn('invoice', 'buy_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_orders', function (Blueprint $table) {
            $table->renameColumn('buy_order', 'invoice');
        });
    }
}
