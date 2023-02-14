<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StoreRegion extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected$table="store_region";
    protected $fillable = [
        'id',
        'store_id',
        'region_id',
    ];
}
