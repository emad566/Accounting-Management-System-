<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;

    use HasFactory;
    use SoftDeletes;
    protected $table="suppliers";
    protected $fillable = [
        'user_id',
        'Sup_Name',
        'Sup_address',
        'Sup_phone',
        'is_active',

    ];

    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

}
