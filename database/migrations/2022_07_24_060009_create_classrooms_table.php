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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages');
            $table->string('name');
            $table->text('info_payment_1_page')->nullable();
            $table->text('info_payment_2_page')->nullable();
            $table->text('info_payment_1_exercise')->nullable();
            $table->text('info_payment_2_exercise')->nullable();
            $table->text('info_payment_1_lessons')->nullable();
            $table->text('info_payment_2_lessons')->nullable();
            $table->text('info_payment_combo')->nullable();
            $table->boolean('show_pages')->default(0);
            $table->boolean('show_video')->default(0);
            $table->boolean('show_lessons')->default(0);
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
        Schema::dropIfExists('classrooms');
    }
};
