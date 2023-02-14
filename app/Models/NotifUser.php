<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class NotifUser extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    public $timestamps = false;

    protected $table = 'notif_user';
    protected $fillable = [
        'id',
        'notif_id',
        'user_id',
        'readed_at',
    ];

    public function notif()
    {
        return $this->hasOne('App\Models\Notif', 'id', 'notif_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


}
