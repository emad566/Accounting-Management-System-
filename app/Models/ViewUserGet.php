<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewUserGet extends Model
{
    use HasFactory;
    protected $table = "view_user_gets";
    protected $fillable =[
        'user_rep_id',
        'user_gets',
    ];


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_rep_id');
    }
}

