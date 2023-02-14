<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewBank extends Model
{

    protected $table = 'view_banks';
    protected $fillable = [
        'bank_id',
        'start_balance',
        'transaction_amounts',
        'spend_amounts',
        'from_bank_transfer_amount',
        'to_bank_transfer_amount',
        'inbank_amounts',
        'bank_amounts_net',
    ];

    public function bank()
    {
        return $this->hasOne('App\Models\Bank', 'id',  'bank_id');
    }


}

