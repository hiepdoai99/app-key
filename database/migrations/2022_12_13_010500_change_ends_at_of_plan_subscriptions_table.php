<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEndsAtOfPlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('subby.tables.plan_subscriptions'), function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable()->change();
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
            $table->dropColumn('ends_at');
        });
    }
}
