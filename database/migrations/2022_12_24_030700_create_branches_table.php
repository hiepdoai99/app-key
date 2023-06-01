<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
