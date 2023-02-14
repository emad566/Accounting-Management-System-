<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class ReturnProduct extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasFactory;

    protected $table = 'return_products';
    // protected $guarded = [*];
    protected $fillable = [
        'return_id',
        'invoice_product_id',
        'return_quantity',
        'return_bounce'
    ];


    public function invoice_product()
    {
        return $this->hasOne('App\Models\InvoiceProduct', 'id', 'invoice_product_id');
    }

}

