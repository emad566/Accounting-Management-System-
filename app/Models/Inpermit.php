<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Inpermit extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;

    protected $fillable = [
        'inpermit_code',
        'inpermit_details',
        'inpermit_date',
        'supplier_id',
        'user_id'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withPivot('id', 'Quantity', 'Buy_Price', 'Public_Price', 'Total', 'runID', 'create_date', 'expire_date');
    }

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public function inperim_product()
    {
        return $this->belongsToMany('App\Models\InpermitProduct', 'inpermit_id', 'id');
    }

    public function manyProducts()
    {
        return $this->hasMany('App\Models\ViewInpermitProduct', 'inpermit_id', 'id');
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\Supplier', 'id', 'supplier_id');
    }

    public function outproducts()
    {
        return $this->hasMany('App\Models\Outproduct', 'inpermit_id', 'id')->orderBy('product_code', 'ASC');
    }

    public function view_view_outproducts()
    {
        return $this->hasMany('App\Models\ViewViewOutproduct', 'inpermit_id', 'id')->orderBy('product_code', 'ASC');
    }

    public function outpermits()
    {
        return $this->hasMany('App\Models\Outpermit', 'inpermit_id', 'id');
    }

}
