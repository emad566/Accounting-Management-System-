<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewStockClosed extends Model
{
    protected $table = "view_stock_closed";
    protected $fillable = [
        'store_id',
        'Store_Name',
        'Product_Name',
        'product_id',
        'runID',
        'Public_Price',
        'expire_date',
        'q_in_store', // داخل المخزن
        'store_q_net', // متاح
        'q_reversed', // محجوز للصرف
        'transfer_q_reserved', // محجوز للتحويل
        'transfer_in', // قيد الشحن وارد 
        'transfer_out', // قيد الشحن صادر
        'is_store_q', // هل توجد كميه في المخزن للجرد
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'updated_at' => 'datetime:Y-m-d h:i:s A',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public static function productcount($product_id, $request=""){
        $where = ($product_id)? " AND product_id=". $product_id : "";
        $groupBy = ($product_id)? " store_id, product_id": " product_id";
        $query = "
        SELECT 
            store_id,
            Store_Name, 
            Product_Name, 
            product_id, 
            runID, 
            Public_Price, 
            expire_date, 
            sum(q_reversed) as q_reversed,
            sum(store_q_net) as store_q_net,
            sum(q_in_store) as q_in_store,
            sum(transfer_q_reserved) as transfer_q_reserved,
            sum(transfer_in) as transfer_in,
            sum(transfer_out) as transfer_out,
            sum(is_store_q) as is_store_q
        from view_stock_closed vsc
        where is_store_q <> 0 ".$where."
        GROUP BY ".$groupBy ."
        ";
        // return $query;
        return DB::select($query);
    }

}


