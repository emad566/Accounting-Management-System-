<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Returns extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasFactory;

    protected $table = 'returns';
    // protected $guarded = [*];
    protected $fillable = [
        'invoice_id',
        'return_code',
        'to_store_id',
        'transfer_id',
        'voucher_id',
        'return_date',
        'user_rep_id'
    ];

    public function returnProductsSync()
    {
        return $this->belongsToMany('App\Models\InvoiceProduct', 'return_products', 'return_id', 'invoice_product_id')->withPivot(
            'return_id',
            'invoice_product_id',
            'return_quantity',
            'return_bounce'
        );
    }

    public function returnProducts()
    {
        return $this->hasMany('App\Models\ReturnProduct', 'return_id',  'id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_rep_id');
    }

    public function invoice()
    {
        return $this->hasOne('App\Models\Invoice', 'id', 'invoice_id');
    }

    public function voucher()
    {
        return $this->belongsToMany('App\Models\Voucher', 'voucher_returns', 'return_id', 'voucher_id');
    }

}

