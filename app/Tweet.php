<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $table = "tweets";

    protected $with = ['myuser'];


    public function myuser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
/*
    public  function myhashtag()
    {
        return $this->belongsToMany(Hashtag::class,'hashtags_tweets','tweet_id');
    } */
}
