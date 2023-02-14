<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class UserStore extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasFactory;
    protected $table = 'store_user';
    protected $fillable = [
        'user_id',
        'store_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
    
    public function stores()
    {
        return $this->hasMany(Store::class, 'id', 'store_id');
    }
}
