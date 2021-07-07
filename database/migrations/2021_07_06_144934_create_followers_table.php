<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {

            $table->unsignedBigInteger('follower')->unsigned()
            ->index()
            ->nullable();
            $table->unsignedBigInteger('Following')->unsigned()
            ->index()
            ->nullable();

            $table->timestamps();

            $table->foreign('follower')
            ->references('id')
            ->on('users');

            $table->foreign('Following')
            ->references('id')
            ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
    }
}
