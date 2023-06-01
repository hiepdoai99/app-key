<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLicenseToPlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('subby.tables.plan_subscriptions'), function (Blueprint $table) {
            $table->boolean('online')->nullable();
            $table->string('license')->unique();
            $table->foreignId('product_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('subby.tables.plan_subscriptions'), function (Blueprint $table) {
            $table->dropColumn(['license', 'online']);
            $table->dropForeign(['product_id']);
        });
    }
}
