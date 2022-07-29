<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kaspi_bank', function (Blueprint $table) {
            $table->id();
            $table->string('account');
            $table->string('txn_id')->nullable();
            $table->string('txn_date')->nullable();
            $table->bigInteger('user_id');
            $table->string('content_id');
            $table->string('content_type');
            $table->integer('sum');
            $table->boolean('paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kaspi_bank');
    }
};
