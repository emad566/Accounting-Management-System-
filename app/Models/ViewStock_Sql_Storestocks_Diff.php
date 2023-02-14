<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ViewStock_Sql_Storestocks_Diff extends Model
{

    protected $table = 'view_stockSql_storestocks_diff';
    protected $fillable = [
        'Product_Name', 
        'product_id', 
        'runID', 
        'q_reversed', 
        'transfer_q_reserved', 
        'transfer_in', 
        'transfer_out', 
        'q_in_store', 
        'store_q_net', 
        'diff_sum'
    ];

}

