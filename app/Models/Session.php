<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Session extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    protected $table = 'sessions';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 
        'ip_address', 
        'user_agent', 
        'payload', 
        'last_activity'
    ];
}
