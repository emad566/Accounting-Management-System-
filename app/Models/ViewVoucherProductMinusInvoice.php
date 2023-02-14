<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewVoucherProductMinusInvoice extends Model
{
    protected $table="view_voucher_product_minus_invoice";
    protected $fillable = [
        'id',
        'voucher_id',
        'product_id',
        'runID',
        'voucher_quantity',
        'invoice_net_q',
        'net_q',
    ];


    public function voucher()
    {
        return $this->hasOne('App\Models\Voucher');
    }

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    
}
