<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $table = 'series';

    protected $fillable = ['title','description','year','poster'];

    public function seasons(){
    	return $this->belongsToMany('App\Season');
    }
}
