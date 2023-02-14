<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewGetProduct extends Model
{
    protected$table="view_get_product";
    protected $fillable = [
        'get_product_id',
        'get_price'
    ];
}
