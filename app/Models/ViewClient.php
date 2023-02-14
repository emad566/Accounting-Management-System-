<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewClient extends Client
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    // 'client_name',
    // 'get_overPrice_sum',
    // 'get_requireds',
    // 'client_pay',
    // 'get_client_nexts',
    // 'r_name',
    // 'city',
    // 'state'
    public function getCityAttribute($value)
    {
        if($value){
            return $value;
        }else if(!$this->city_id){
            return $this->getRawOriginal('r_name');
        }else{
            return '';
        }
    }

    public function getRNameAttribute($value)
    {
        if($this->city_id){
            return $value;
        }else{
            return '';
        }
    }

}


