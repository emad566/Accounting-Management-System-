<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clientarea extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'clientareas';


    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

    public function getRNameAttribute($val)
    {
        return ($this->attributes['city_id']) ? $val : '';
    }

    public function getCityAttribute($val)
    {
        return ($this->attributes['city_id']) ? $val : $this->attributes['r_name'];
    }

    public function getCityIdAttribute($val)
    {
        return ($this->attributes['city_id']) ? $val : $this->region_id;
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'id');
    }

    public function view_client_accounting()
    {
        return $this->hasOne('App\Models\ViewClientAccounting', 'client_id', 'id');
    }

    public function client_type()
    {
        return $this->hasOne('App\Models\ClientType', 'id', 'client_type_id');
    }

    public function view_client()
    {
        return $this->hasOne('App\Models\ViewClient', 'id', 'id');
    }


}


