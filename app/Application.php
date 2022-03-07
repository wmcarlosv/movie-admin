<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';
    protected $fillable = ['name','about','version','play_store_url','privacy_policy','url_qualify','url_more_apps','app_code'];

}
