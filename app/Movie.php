<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';

    protected $fillable = ['title','description','year','poster','api_code','views','downloads','status'];

    public function categories(){
    	return $this->belongsToMany('App\Category','movie_categories');
    }
}
