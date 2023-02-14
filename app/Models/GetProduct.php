<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GetProduct extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;

    protected $table = 'get_product';
    protected $fillable = [
        'id',
        'get_id',
        'invoice_product_id',
        'get_quantity',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'updated_at' => 'datetime:Y-m-d h:i:s A',
    ];


    public function view_get_product()
    {
        return $this->hasOne('App\Models\ViewGetProduct', 'get_product_id', 'id');
    }
    
    public function get()
    {
        return $this->hasOne(Get::class, 'id', 'get_id');
    }

    public function invoice_product()
    {
        return $this->hasOne('App\Models\InvoiceProduct', 'id', 'invoice_product_id');
    }





}
