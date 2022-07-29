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
        Schema::create('course_video_test_answers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_video_test_id')->unsigned();
            $table->foreign('course_video_test_id')->references('id')->on('course_video_tests');
            $table->string('answer');
            $table->boolean('right');
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
        Schema::dropIfExists('course_video_test_answers');
    }
};
