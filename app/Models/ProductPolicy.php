<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductPolicy extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    public $timestamps = false;

    protected $table = 'productpolicys';
    protected $fillable = [
        'client_id',
        'product_id',
        'is_multi_due_inherit_id',
        'paid_discount',
        'due_discount'
    ];

    // public function paid_discount($region_id="")
    // {
    //     if ($region_id && Region::find($region_id))
    //         return Region::find($region_id)->paid_discount;

    //     return ($this->paid_discount)? $this->paid_discount : 'يرث من سياسات المنطقة';
    // }

    // public function due_discount($region_id="")
    // {
    //     if ($region_id && Region::find($region_id))
    //         return Region::find($region_id)->due_discount;

    //     return ($this->due_discount)? $this->due_discount : 'يرث من سياسات المنطقة';
    // }



    // public function is_multi_due_inherit_name($region_id="")
    // {
    //     if ($region_id && Region::find($region_id))
    //         return Region::find($region_id)->is_multi_due_inherit->name;

    //     return $this->is_multi_due_inherit->name;
    // }

    public function is_multi_due_inherit()
    {
        return $this->hasOne('App\Models\Isinherit', 'id', 'is_multi_due_inherit_id');
    }


    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'client_id');
    }
}
