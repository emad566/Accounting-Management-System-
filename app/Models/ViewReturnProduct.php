<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewReturnProduct extends ReturnProduct
{

    protected $guarded = [];
    protected $table = 'view_return_products';
    protected $fillable = [
        'id',
        'return_id',
        'invoice_product_id',
        'return_quantity',
        'return_bounce',
        'created_at',
        'updated_at',
        'invoice_id',
        'invoice_code',
        'return_code',
        'return_date',
        'fullName',
        'client_id',
        'client_name',
        'product_id',
        'runID',
        'Product_Name'
    ];


    public function return_product()
    {
        return $this->hasOne('App\Models\ReturnProduct', 'id', 'id');
    }
}


