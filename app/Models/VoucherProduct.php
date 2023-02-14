<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VoucherProduct extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected $table="voucher_product";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'voucher_id',
        'product_id',
        'runID',
        'voucher_quantity',
    ];
}
