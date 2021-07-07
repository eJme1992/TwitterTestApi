<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = "hashtags";

    protected $with = ['mytweets'];

    public  function mytweets()
    {
        return $this->belongsToMany(Tweet::class,'hashtags_tweets','hashtag_id');
    }


}
