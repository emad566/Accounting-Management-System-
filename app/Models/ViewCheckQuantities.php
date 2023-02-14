<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ViewCheckQuantities extends Model
{

    protected $table = 'view_check_quantities';
    protected $fillable = [
        'Product_Name', 
        'product_id', 
        'runID', 
        'in_out_q_net', 
        'voucher_q', 
        'invoice_q', 
        'transfer_out', 
        'q_in_store', 
        'in_deff'
    ];

}

