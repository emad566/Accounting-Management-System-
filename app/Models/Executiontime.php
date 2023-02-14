<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Executiontime extends Model
{
    protected $table = 'executiontime';
    protected $fillable = [
        'id',
        'user_id',
        'url',
        'route',
        'time_secs_html',
        'time_secs_php',
        'created_at',
        'updated_at',
    ];

    


}

