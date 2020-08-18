<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $table = 'chapters';

    protected $fillable = ['season_id','title','position','api_code'];

    public function season(){
    	return $this->belongsTo('App\Season');
    }
}
