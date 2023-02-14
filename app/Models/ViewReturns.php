<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewReturns extends Returns
{
    protected $guarded = [];
    protected $table = 'view_returns';



    public function return()
    {
        return $this->hasOne('App\Models\Returns', 'id', 'id');
    }
}


