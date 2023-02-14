<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class VoucherReturn extends Model
{
    
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected $table = 'voucher_returns';
    protected $fillable = [
        'id',
        'voucher_id',
        'return_id',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'updated_at' => 'datetime:Y-m-d h:i:s A',
    ];
}

