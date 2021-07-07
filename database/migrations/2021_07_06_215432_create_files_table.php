<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tweet_id')->unsigned()
            ->index()
            ->nullable();
            $table->unsignedBigInteger('user_id')->unsigned()
            ->index()
            ->nullable();

            $table->string('slug')->unique();
            $table->text('name');
            $table->text('url');
            $table->text('type');
            $table->text('state');

            $table->timestamps();

            $table->foreign('tweet_id')
            ->references('id')
            ->on('tweets');

            $table->foreign('user_id')
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
        Schema::dropIfExists('files');
    }
}
