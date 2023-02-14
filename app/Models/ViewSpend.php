<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewSpend extends Spend
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'view_spends';

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

    public function spend()
    {
        return $this->hasOne('App\Models\Spend', 'id', 'id');
    }
}


