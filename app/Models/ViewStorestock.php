<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewStorestock extends Storestock
{

    protected $table = 'view_storestocks';
    protected $fillable = [
        'store_id',
        'product_id',
        'runID',
        'q_in_store', // داخل المخزن
        'store_q_net', // متاح
        'q_reversed', // محجوز للصرف
        'transfer_q_reserved', // محجوز للتحويل
        'transfer_in', // قيد الشحن وارد 
        'transfer_out', // قيد الشحن صادر
        'is_store_q', // هل توجد كميه في المخزن للجرد
        'created_at',
        'updated_at',
        'Public_Price',
        'expire_date'
    ];

    

}

