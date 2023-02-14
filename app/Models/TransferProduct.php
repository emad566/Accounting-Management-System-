<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TransferProduct extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected $table="transfer_product";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'transfer_id',
        'product_id',
        'Quantity',
        'RunID',
    ];
}
