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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subject_id')->unsigned();
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->string('name');
            $table->string('cover');
            $table->string('cover_lesson')->nullable();
            $table->string('cover_video')->nullable();
            $table->string('preview_lesson')->nullable();
            $table->string('preview_exercise')->nullable();
            $table->string('info_free')->nullable();
            $table->text('info_payment_1_page')->nullable();
            $table->text('info_payment_2_page')->nullable();
            $table->text('info_payment_1_exercise')->nullable();
            $table->text('info_payment_2_exercise')->nullable();
            $table->text('info_payment_1_lesson')->nullable();
            $table->text('info_payment_2_lesson')->nullable();
            $table->text('text_exercise')->nullable();
            $table->text('text_lesson')->nullable();
            $table->integer('price_pages')->default(0);
            $table->integer('price_video')->default(0);
            $table->integer('price_lessons')->default(0);
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
        Schema::dropIfExists('books');
    }
};
