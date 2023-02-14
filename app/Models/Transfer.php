<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Transfer extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasFactory;

    protected $fillable = [
        'transfer_code',
        'transfer_status_id',
        'transfer_name',
        'transfer_phone',
        'transfer_date',
        'transfer_details',
        'from_store_id',
        'to_store_id',
        'user_id',
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'transfer_product')->withPivot('id', 'transfer_id', 'product_id', 'Quantity', 'RunID');
    }

    public function hasManyProducts()
    {
        return $this->hasMany('App\Models\TransferProduct', 'transfer_id', 'id');
    }


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\TransferStatus', 'id', 'transfer_status_id');
    }

    public function storeFrom()
    {
        return $this->hasOne('App\Models\Store', 'id', 'from_store_id');
    }

    public function storeTo()
    {
        return $this->hasOne('App\Models\Store', 'id', 'to_store_id');
    }

}
