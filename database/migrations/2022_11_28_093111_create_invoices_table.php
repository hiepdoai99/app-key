<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subscriber_id')->references('id')->on('users')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('plan_subscription_id')->nullable()->constrained();

            $table->double('total')->default(0.0);
            $table->string('code')->unique();
            $table->string('status')->index();
            $table->string('his')->index();

            $table->double('tax')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('transaction')->nullable();
            $table->string('coupon')->nullable();
            $table->double('discount')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
