<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            // $table->string('username');
            // $table->string('fullname');
            // $table->string('email');
            $table->string('image_cid')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('house_type')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street_number')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
