<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ViewPermission extends Model
{

    protected $table = 'view_permission';
    protected $fillable = [
        'id',
        'name',
        'guard_name',
        'created_at',
        'updated_at',
        'role_id',
        'model_type',
        'model_id'
    ];

}

