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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 10)->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('name');
            $table->string('promo_code', 12)->unique();
            $table->integer('device_count')->default(0);
            $table->integer('device_limit')->default(1);
            $table->integer('balance')->default(0);
            $table->integer('notification_id')->default(0);
            $table->string('code_android')->nullable();
            $table->string('code_ios')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
