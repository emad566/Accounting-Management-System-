<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewStockClosedSql extends Model
{
    protected $table = "view_stock_closed_sql";
    protected $fillable = [
        'store_id',
        'Store_Name',
        'Product_Name',
        'product_id',
        'runID',
        'Public_Price',
        'expire_date',
        'q_net',  // Q in the store without include any calculation of vouchers or invoices
        'invoice_quantity_return_closed',  // Q that returened from invoices of closed vouches
        'invoice_net_q_closed', // Q of closed vouchers
        'voucher_quantity',//Q of vouchers
        'q_reversed', // Q reversed but found in the store = q of wait + accept vouchers but not go out of the store
        'store_q_net', // Q in store which is avialble of new vouchers but not include  q_reversed
        'q_in_store' // q_in_store = q_reversed + store_q_net
    ];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
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
        from view_stock_closed_sql vsc
        where is_store_q = 1 ".$where."
        GROUP BY store_id, product_id
        ORDER BY Store_Name, Product_Name
        ";
        // return $query;
        return DB::select($query);
    }

}


