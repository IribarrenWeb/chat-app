<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function fromContact(){
        return $this->hasOne(User::class,'id','from');
    }

}
