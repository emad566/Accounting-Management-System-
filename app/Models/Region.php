<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Region extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'r_name',
        'city_id',
        'state_id',
        'is_active',
        'is_multi_due_inherit_id',
        'client_due_limit',
        'paid_discount',
        'due_discount',
    ];

    public static function policy_region_id($region_id, $col_name)
    {

        if($col_name == 'is_multi_due_inherit_id'){
            $region = Region::find($region_id);

            if ($region && $region->$col_name != 30) return $region->id;

            if($region->city_id){
                $region = Region::find($region->city_id);
                if ($region && $region->$col_name != 30) return $region->id;
            }

            if($region->state_id){
                $region = Region::find($region->state_id);
                if ($region && $region->$col_name != 30) return $region->id;
            }
        }else{
            $region = Region::find($region_id);
            if ($region && $region->$col_name) return $region->id;

            if($region->city_id){
                $region = Region::find($region->city_id);
                if ($region && $region->$col_name) return $region->id;
            }

            if($region->state_id){
                $region = Region::find($region->state_id);
                if ($region && $region->$col_name) return $region->id;
            }
        }
        return $region_id;
    }

    public function paid_discount()
    {
        $value = $this->paid_discount;
        if($value){
            return $value;
        }else{
            if(Generalpolicy::find(1)){
                return Generalpolicy::find(1)->paid_discount;
            }else{
                return "يرث من سياسات عامة";
            }

        }
    }

    public function client_due_limit()
    {
        $value = $this->client_due_limit;
        if($value){
            return $value;
        }else{
            if(Generalpolicy::find(1)){
                return Generalpolicy::find(1)->client_due_limit;
            }else{
                return "يرث من سياسات عامة";
            }

        }
    }

    public function due_discount()
    {
        $value = $this->due_discount;
        if($value){
            return $value;
        }else{
            if(Generalpolicy::find(1)){
                return Generalpolicy::find(1)->due_discount;
            }else{
                return "يرث من سياسات عامة";
            }

        }
    }

    public function is_multi_due_inherit_id()
    {
        $value = $this->is_multi_due_inherit_id;
        if($value && $value != 30){
            return $value;
        }else{
            if(Generalpolicy::find(1) && Generalpolicy::find(1)->is_multi_due){
                return 10;
            }else{
                return 20;
            }

        }
    }

    public function is_multi_due_inherit_name()
    {
        $value = $this->is_multi_due_inherit_id;
        if($value && $value != 30){
            return $value;
        }else{
            if(Generalpolicy::find(1) && Generalpolicy::find(1)->is_multi_due){
                return 10;
            }else{
                return 20;
            }

        }
    }

    public function is_multi_due_inherit()
    {
        return $this->hasOne('App\Models\Isinherit', 'id', 'is_multi_due_inherit_id');
    }

    public function city()
    {
        return  $this->hasOne(self::class, 'id', 'city_id');
    }

    public function state()
    {
        return  $this->hasOne(self::class, 'id', 'state_id');
    }

    public function cities()
    {
        return  $this->hasMany(self::class, 'state_id', 'id')->whereNull('city_id');
    }

    public function regions()
    {
        return  $this->hasMany(self::class, 'city_id', 'id');
    }

    public static function allStates()
    {
        return  Region::whereNull('state_id');
    }

    public static function allCities()
    {
        return  Region::whereNotNull('state_id')->whereNull('city_id');
    }

    public static function allRegions()
    {
        return  Region::whereNotNull('state_id')->whereNotNull('city_id');
    }

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store', 'store_region')->withPivot('id', 'store_id', 'region_id');
    }

    public function get_state_name()
    {
        if(!$this->state && !$this->city)
            return $this->r_name;
        else{
            return $this->state->r_name;
        }
    }

    public function get_city_name()
    {
        if($this->state && !$this->city)
            return $this->r_name;
        elseif($this->state && $this->city)
            return $this->city->r_name;
        else return "";
    }

    public function get_region_name()
    {
        if($this->state && $this->city)
            return $this->r_name;
        else return "";
    }
}
