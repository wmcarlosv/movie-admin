<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = "channels";

    protected $fillable = ['title','description','poster','url','status'];
}
