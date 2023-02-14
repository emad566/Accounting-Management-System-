<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Get extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;

    protected $table = 'gets';
    protected $fillable = [
        'id',
        'invoice_id',
        'get_code',
        'get_date',
        'user_rep_id',
        'get_overPrice',
        'client_pay',
        'paid_from_client_balance',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'updated_at' => 'datetime:Y-m-d h:i:s A',
    ];

    public function GetProducts()
    {
        return $this->belongsToMany('App\Models\InvoiceProduct', 'get_product', 'get_id', 'invoice_product_id')->withPivot(
            'id',
            'get_id',
            'invoice_product_id',
            'get_quantity',
            'created_at',
            'updated_at'
        );
    }

    public function pivotGetProducts()
    {
        return $this->hasMany('App\Models\GetProduct', 'get_id', 'id');
    }


    public function invoice()
    {
        return $this->hasOne('App\Models\Invoice', 'id', 'invoice_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_rep_id');
    }

    public function view_get()
    {
        return $this->hasOne('App\Models\ViewGet', 'get_id', 'id');
    }

}

