<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transfer;
use App\Models\Notif;
use DB;

class ViewNotif extends Notif
{
    protected $table = 'view_notifs';

    protected $fillable = [
        'id',
        'user_create_id',
        'notefun',
        'table_name',
        'noteType',
        'notifiable_type',
        'notifiable_id',
        'readed_by_user_id',
        'readed_at',
        'user_id',
        'user_read_at',
        'created_at',
        'updated_at',
    ];
}
