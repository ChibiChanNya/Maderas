<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('action');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('dt_action');
        });
    }

    /**
     * Schema::create('log_actions', function (Blueprint $table) {
     *       $table->bigIncrements('id');
     *       $table->integer('loggable_id');
     *       $table->string('loggable_type');
     *       $table->string('action');
     *       $table->unsignedBigInteger('user_id')->index();
     *       $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
     *       $table->timestamp('dt_action');
     *   });
     */

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_actions');
    }
}
