<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Outpermit extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;

    protected $fillable = [
        'outpermit_code',
        'inpermit_id',
        'outpermit_detail',
        'outpermit_date',
        'user_id'
    ];

    public function InpermitProduct()
    {
        return $this->belongsToMany('App\Models\InpermitProduct', 'outpermit_product', 'outpermit_id', 'inpermit_product_id')->withPivot('id', 'Quantity_out');
    }

    public function inpermit()
    {
        return $this->hasOne('App\Models\Inpermit', 'id', 'inpermit_id');
    }

    public function outproducts()
    {
        return $this->hasMany('App\Models\Outproduct', 'outpermit_id', 'id')->orderBy('product_code', 'ASC');
    }




}
