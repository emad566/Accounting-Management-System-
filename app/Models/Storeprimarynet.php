<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class Storeprimarynet extends Model
{
    // use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected $table = 'outproducts';
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

}
