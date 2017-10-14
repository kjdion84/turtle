<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTable extends Migration
{
    public function up()
    {
        // create billing table
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('plan_name');
            $table->string('amount');
            $table->integer('cc_last4');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        // drop billing table
        Schema::dropIfExists('billings');
    }
}