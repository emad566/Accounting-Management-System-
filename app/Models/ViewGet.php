<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewGet extends Model
{
    protected$table="view_gets";
    protected $fillable = [
        'get_id',
        'get_price_sum'
    ];

}
