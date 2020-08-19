<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $table = 'seasons';

    protected $fillable = ['serie_id','title','position'];

    public function serie(){
    	return $this->belongsTo('App\Serie');
    }

    public function chapters(){
    	return $this->hasMany('App\Chapter');
    }
}
