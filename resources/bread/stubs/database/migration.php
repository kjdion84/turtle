<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Createbread_model_classTable extends Migration
{
    public function up()
    {
        // create bread_model_variables table
        Schema::create('bread_model_variables', function (Blueprint $table) {
            $table->increments('id');
            /* bread_schema */
            $table->timestamps();
        });

        // add permissions
        app(config('turtle.models.permission'))->create(['group' => 'bread_model_strings', 'name' => 'Browse bread_model_strings']);
        app(config('turtle.models.permission'))->create(['group' => 'bread_model_strings', 'name' => 'Read bread_model_strings']);
        app(config('turtle.models.permission'))->create(['group' => 'bread_model_strings', 'name' => 'Edit bread_model_strings']);
        app(config('turtle.models.permission'))->create(['group' => 'bread_model_strings', 'name' => 'Add bread_model_strings']);
        app(config('turtle.models.permission'))->create(['group' => 'bread_model_strings', 'name' => 'Delete bread_model_strings']);
    }

    public function down()
    {
        // drop bread_model_variables table
        Schema::dropIfExists('bread_model_variables');

        // delete permissions
        app(config('turtle.models.permission'))->where('group', 'bread_model_strings')->delete();
    }
}
