<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Voucher extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    // use Spatie\Activitylog\Traits\LogsActivity;

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
        'created_at',
        'updated_at',
    ];

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'voucher_id', 'id')->orderBy('id', 'DESC');
    }

    public function view_invoices()
    {
        return $this->hasOne('App\Models\ViewInvoice', 'voucher_id', 'id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\VoucherStatus', 'id', 'voucher_status');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'voucher_product')->withPivot('id', 'voucher_id', 'product_id', 'runID', 'voucher_quantity');
    }
    
    public function productsMany()
    {
        return $this->hasMany(VoucherProduct::class, 'voucher_id', 'id');
    }

    public function products_q_net()
    {
        return $this->hasMany('App\Models\ViewVoucherProductMinusInvoice', 'voucher_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_rep_id');
    }

    public function rep()
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

    public function returns()
    {
        return $this->belongsToMany('App\Models\Returns', 'voucher_returns', 'voucher_id', 'return_id');
    }
}
