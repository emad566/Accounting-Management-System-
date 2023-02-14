<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewViewOutproduct extends Model
{
    protected $table = 'view_view_outproducts';
    protected $guarded = [];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public function primStorePro()
    {
        $psp =  ViewStockClosed::where([
            'store_id'=>1,
            'product_id'=>$this->product_id,
            'runID'=>$this->runID,
        ])->first();

        if($psp) return $psp; else return null;

    }

    public function primStorePros()
    {
        return  $this->hasMany('App\Models\ViewStockClosed', 'product_id', 'product_id');
    }

    public function getNetQAttribute($val)
    {
        return ($val =="") ? $this->Quantity : $val;
    }
}
