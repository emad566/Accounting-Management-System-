<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewStockClosedGroupby extends Model
{
    protected $table = "view_stock_closed_groupby";
    protected $fillable = [
        'store_id',
        'Store_Name',
        'Product_Name',
        'product_id',
        'q_in_store', // داخل المخزن
        'store_q_net', // متاح
        'q_reversed', // محجوز للصرف
        'transfer_q_reserved', // محجوز للتحويل
        'transfer_in', // قيد الشحن وارد 
        'transfer_out', // قيد الشحن صادر
        'is_store_q', // هل توجد كميه في المخزن للجرد
    ];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }
}

