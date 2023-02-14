<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Storestock extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;

    protected $table = 'storestocks';
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
        'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'updated_at' => 'datetime:Y-m-d h:i:s A',
    ];

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
    
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    

    public static function productcount($product_id, $request=""){
        $where = ($product_id)? " AND product_id=". $product_id : "";
        $query = "
        SELECT 
            store_id,
            Store_Name, 
            Product_Name, 
            product_id, 
            runID, 
            Public_Price, 
            expire_date, 
            sum(q_net) as q_net,
            sum(invoice_quantity_return_closed) as invoice_quantity_return_closed,
            sum(invoice_net_q_closed) as invoice_net_q_closed,
            sum(voucher_quantity) as voucher_quantity,
            sum(q_reversed) as q_reversed,
            sum(store_q_net) as store_q_net,
            sum(q_in_store) as q_in_store,
            sum(transfer_q_reserved) as transfer_q_reserved,
            sum(transfer_in) as transfer_in,
            sum(transfer_out) as transfer_out,
            sum(voucher_q_out) as voucher_q_out,
            sum(is_store_q) as is_store_q
        from view_stock_closed vsc
        where is_store_q = 1 ".$where."
        GROUP BY store_id, product_id
        ORDER BY Store_Name, Product_Name
        ";
        
        return DB::select($query);
    }

}

