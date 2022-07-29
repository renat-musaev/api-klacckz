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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('course_categories');
            $table->string('name');
            $table->text('text');
            $table->string('cover');
            $table->string('preview')->nullable();
            $table->integer('price_1')->default(0);
            $table->integer('price_2')->default(0);
            $table->integer('price_3')->default(0);
            $table->integer('price_4')->default(0);
            $table->text('info_payment_1')->nullable();
            $table->text('info_payment_2')->nullable();
            $table->boolean('show')->default(0);
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
        Schema::dropIfExists('courses');
    }
};
