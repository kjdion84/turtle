<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    public function up()
    {
        // create activities table
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('model_id')->nullable();
            $table->string('model_class')->nullable();
            $table->text('data')->nullable();
            $table->string('log');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // create permissions
        app(config('turtle.models.permission'))->create(['group' => 'Activities', 'name' => 'View Activities']);
    }

    public function down()
    {
        // drop activities table
        Schema::dropIfExists('activities');

        // delete permissions
        app(config('turtle.models.permission'))->where('group', 'Activities')->delete();
    }
}