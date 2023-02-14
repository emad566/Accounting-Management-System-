<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'Product_code',
        'Product_Name',
        'Public_Price',
        'Min_Discount',
        'Max_Discount',
        'is_active',
        'is_multi_due_inherit_id',
        'paid_discount',
        'due_discount'
    ];

    public function paid_discount($region_id="")
    {

        if ($region_id && Region::find($region_id)){
            return Region::find($region_id)->paid_discount;
        }

        return ($this->paid_discount)? $this->paid_discount : 'يرث من سياسات المنطقة';
    }

    public function due_discount($region_id="")
    {
        if ($region_id && Region::find($region_id))
            return Region::find($region_id)->due_discount;

        return ($this->due_discount)? $this->due_discount : 'يرث من سياسات المنطقة';
    }



    public function is_multi_due_inherit_name($region_id="")
    {
        if ($region_id && Region::find($region_id))
            return Region::find($region_id)->is_multi_due_inherit->name;

        return $this->is_multi_due_inherit->name;
    }

    public function is_multi_due_inherit()
    {
        return $this->hasOne('App\Models\Isinherit', 'id', 'is_multi_due_inherit_id');
    }

    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

    public function inpermits()
    {
        return $this->belongsToMany('App\Models\Inpermit')->withPivot('id', 'Quantity', 'Buy_Price', 'Public_Price', 'Total', 'runID', 'expire_date');
    }

    public function run_ids()
    {
        
        return $this->hasMany(InpermitProduct::class, 'product_id', 'id');
    }

}
