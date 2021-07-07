<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHashtagTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hashtags_tweets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tweet_id')->unsigned()
            ->index()
            ->nullable();
            $table->unsignedBigInteger('hashtag_id')->unsigned()
            ->index()
            ->nullable();

            $table->timestamps();

            $table->foreign('tweet_id')
            ->references('id')
            ->on('tweets');

            $table->foreign('hashtag_id')
            ->references('id')
            ->on('hashtags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hashtag_tweets');
    }
}
