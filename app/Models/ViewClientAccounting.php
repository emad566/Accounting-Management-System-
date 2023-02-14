<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewClientAccounting extends Model
{
    use HasFactory;
    protected $table = "view_client_accounting";
    protected $fillable =[
        'client_id',
        'get_requireds_sum',
        'get_paids_sum',
        'client_pays_sum',
        'client_balance_effect_sum',
        'get_nexts_sum',
        'get_overPrice_sum',
        'paid_from_client_balance_sum',
        'get_quantitys_sum',
        'return_quantitys_sum',
        'invoice_quantitys_sum',
        'next_quantity_sum'
    ];


    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'id');
    }



    public function client_type()
    {
        return $this->hasOne('App\Models\ClientType', 'id', 'client_type_id');
    }

    public static function period($start, $end, $client_id=""){
        $query= "SELECT
        invoices.client_id,
        invoices.get_requireds_sum,
        invoices.get_paids_sum,
        invoices.client_pays_sum,
        invoices.client_balance_effect_sum,
        invoices.get_nexts_sum,
        invoices.get_overPrice_sum,
        invoices.paid_from_client_balance_sum,
        IFNULL(gets.get_quantitys_sum, 0) as get_quantitys_sum,
        IFNULL(returns.return_quantitys_sum, 0) as return_quantitys_sum,
        IFNULL(invQ.invoice_quantitys_sum, 0) as invoice_quantitys_sum,
        IFNULL(invQ.invoice_quantitys_sum, 0) - IFNULL(returns.return_quantitys_sum, 0) - IFNULL(gets.get_quantitys_sum, 0) as next_quantity_sum
        from
        (SELECT
        invoices.client_id,
        sum(view_invoices.get_requireds) as get_requireds_sum,
        sum(view_invoices.get_paids) as get_paids_sum,
        sum(view_invoices.client_pay) as client_pays_sum,
        sum(view_invoices.client_balance_effect) as client_balance_effect_sum,
        sum(view_invoices.get_nexts) as get_nexts_sum,
        sum(view_invoices.get_overPrice_sum) as get_overPrice_sum,
        sum(view_invoices.paid_from_client_balance_sum) as paid_from_client_balance_sum
        from invoices LEFT JOIN view_invoices on
        invoices.id = view_invoices.id
        WHERE invoices.invoice_status_id = 20
         AND invoices.invoice_date >= '$start'
         And invoices.invoice_date <= '$end'
        GROUP BY client_id) as invoices
        LEFT JOIN
        (SELECT
        invoices.client_id,
        sum(get_product.get_quantity) as get_quantitys_sum
        FROM invoices JOIN gets
        ON invoices.id = gets.invoice_id
        LEFT JOIN get_product
        ON gets.id = get_product.get_id
        WHERE invoices.invoice_status_id=20
            AND invoices.invoice_date >= '$start'
            And invoices.invoice_date <= '$end'
        GROUP BY client_id) as gets
        ON invoices.client_id = gets.client_id

        LEFT JOIN
        (SELECT
        invoices.client_id,
        sum(return_products.return_quantity) as return_quantitys_sum
        FROM invoices JOIN returns
        ON invoices.id = returns.invoice_id
        LEFT JOIN return_products
        ON returns.id = return_products.return_id
        WHERE invoices.invoice_status_id=20
             AND invoices.invoice_date >= '$start'
             And invoices.invoice_date <= '$end'
        GROUP BY client_id) as returns
        ON invoices.client_id = returns.client_id
        LEFT JOIN
        (SELECT
        invoices.client_id,
        sum(invoice_product.invoice_quantity) as invoice_quantitys_sum
        FROM invoices LEFT JOIN invoice_product
        ON invoices.id = invoice_product.invoice_id
        WHERE invoices.invoice_status_id=20
             AND invoices.invoice_date >= '$start'
             And invoices.invoice_date <= '$end'
        GROUP BY client_id) as invQ
        ON invoices.client_id = invQ.client_id";

        if($client_id){
            $query .= " where invoices.client_id=".$client_id;
        }
        return DB::select($query);
    }

}

