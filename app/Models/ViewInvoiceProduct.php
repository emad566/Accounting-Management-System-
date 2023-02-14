<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewInvoiceProduct extends InvoiceProduct
{
    protected$table="view_invoice_product";
    protected $fillable = [
        'id',
        'invoice_id',
        'product_id',
        'runID',
        'invoice_public_price',
        'discount',
        'invoice_quantity',
        'invoice_bounce',
        'return_bounce',
        'invoice_bounce_net',
        'invoice_quantity_return',
        'invoice_net_q',
        'get_quantity',
        'get_required',
        'get_paid',
        'get_next',
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
    
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    
}


