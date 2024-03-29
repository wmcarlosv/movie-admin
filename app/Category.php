<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name','is_for_channel'];

    public function movies(){
    	return $this->belongsToMany('App\Movie');
    }
}
