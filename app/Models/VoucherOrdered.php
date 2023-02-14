<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class VoucherOrdered extends Model
{
    use HasFactory;
    protected $table = "vouchers_ordered";
    protected $fillable = [
        'store_id',
        'user_rep_id',
        'user_accountant_id',
        'user_keeper_id',
        'user_accountant_return_id',
        'user_keeper_return_id',
        'voucher_status',
        'voucher_code',
        'voucher_details',
        'voucher_date',
        'voucher_close_date',
        'settlement_request_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function status()
    {
        return $this->hasOne('App\Models\VoucherStatus', 'id', 'voucher_status');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'voucher_product')->withPivot('id', 'voucher_id', 'product_id', 'runID', 'voucher_quantity');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_rep_id');
    }

    public function accountant()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_accountant_id');
    }

    public function keeper()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_keeper_id');
    }

    public function accountant_return()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_accountant_return_id');
    }

    public function keeper_return()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_keeper_return_id');
    }

    public function store()
    {
        return $this->hasOne('App\Models\Store', 'id', 'store_id');
    }
}
