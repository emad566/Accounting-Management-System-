<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewStock extends Model
{
    protected $table = "view_stock";
    protected $fillable = [
        'store_id',
        'product_id',
        'Product_Name',
        'runID',
        'Public_Price',
        'expire_date',
        'q_net',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

}


