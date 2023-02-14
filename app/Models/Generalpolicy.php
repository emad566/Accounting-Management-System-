<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Generalpolicy extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;

    
    protected $table = 'generalpolicys';
    protected $fillable = [
        'last_time',
        'is_multi_due',
        'rep_limit',
        'client_due_limit',
        'paid_discount',
        'due_discount',
        'max_return_period',
        'auto_accept_permission_name',
        'pay_more_invoice_balance'
    ];

    public function getLastTImeAttribute($value)
    {
        // return "Emad";
        return  substr($value,0,5);
    }


}

