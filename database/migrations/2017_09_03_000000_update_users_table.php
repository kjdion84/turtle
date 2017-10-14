<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        // add timezone column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->default(config('app.timezone'));
            $table->boolean('billable')->default(false);
            $table->string('billing_customer')->nullable();
            $table->string('billing_subscription')->nullable();
            $table->string('billing_plan')->nullable();
            $table->integer('billing_cc_last4')->nullable();
            $table->timestamp('billing_trial_ends')->nullable();
            $table->timestamp('billing_period_ends')->nullable();
        });

        // add admin user
        app(config('turtle.models.user'))->create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('admin123')]);
    }

    public function down()
    {
        // drop timezone columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
    }
}