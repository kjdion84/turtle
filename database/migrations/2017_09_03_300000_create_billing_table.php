<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTable extends Migration
{
    public function up()
    {
        // add billing columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('billable')->default(false);
            $table->string('billing_customer')->nullable();
            $table->string('billing_subscription')->nullable();
            $table->string('billing_plan')->nullable();
            $table->integer('billing_cc_last4')->nullable();
            $table->timestamp('billing_trial_ends')->nullable();
            $table->timestamp('billing_period_ends')->nullable();
        });

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
        // drop billing columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('billable');
            $table->dropColumn('billing_customer');
            $table->dropColumn('billing_subscription');
            $table->dropColumn('billing_plan');
            $table->dropColumn('billing_cc_last4');
            $table->dropColumn('billing_trial_ends');
            $table->dropColumn('billing_period_ends');
        });

        // drop billing table
        Schema::dropIfExists('billings');
    }
}