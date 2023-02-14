<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class UserToken extends Model
{
    
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected $table = 'user_tokens';
    protected $fillable = [
        'id',
        'user_id',
        'token',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id',  'id');
    }

    
}

