<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Bank extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use SoftDeletes;

    protected $table = 'banks';
    protected $fillable = [
        'id',
        'bank_name',
        'start_balance',
    ];

    public function view_bank()
    {
        return $this->hasOne('App\Models\ViewBank', 'bank_id',  'id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction', 'bank_id',  'id');
    }

    public function spends()
    {
        return $this->hasMany('App\Models\Spend', 'bank_id',  'id');
    }
    
    public function inbanks()
    {
        return $this->hasMany('App\Models\Inbank', 'bank_id',  'id');
    }


}

