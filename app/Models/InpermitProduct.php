<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class InpermitProduct extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;
    protected $table="inpermit_product";
    protected $fillable = [
        'id',
        'inpermit_id',
        'product_id',
        'Quantity',
        'Buy_Price',
        'Public_Price',
        'runID',
        'create_date',
        'expire_date'
    ];


}
