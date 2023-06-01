<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceOfPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('subby.tables.plans'), function (Blueprint $table) {
            $table->decimal('price',12,2)->unsigned()->default('0.00')->change();
            $table->unsignedBigInteger('invoice_period')->default(0)->change();
            $table->string('invoice_interval')->default('day')->change();
        });
        Schema::table(config('subby.tables.plan_subscriptions'), function (Blueprint $table) {
            $table->decimal('price',12,2)->unsigned()->default('0.00')->change();
            $table->unsignedBigInteger('invoice_period')->default(0)->change();
            $table->string('invoice_interval')->default('day')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('subby.tables.plans'), function (Blueprint $table) {
            $table->dropColumn(['price', 'invoice_period', 'invoice_interval']);
        });
        Schema::table(config('subby.tables.plan_subscriptions'), function (Blueprint $table) {
            $table->dropColumn(['price', 'invoice_period', 'invoice_interval']);
        });
    }
}
