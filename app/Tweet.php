<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $table = "tweets";

    protected $with = ['myuser','img'];


    public function myuser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function img()
    {
        return $this->hasOne(File::class);
    }

}
