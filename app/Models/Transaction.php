<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
class Transaction extends Model
{
    
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    protected $table = 'transactions';
    protected $fillable = [
        'id',
        'transaction_date',
        'transaction_code',
        'from_user_id',
        'bank_id',
        'amount',
        'transaction_details',
        'transaction_status_id',
        'image'
    ];

    public function status()
    {
        return $this->hasOne('App\Models\TransactionStatus', 'id', 'transaction_status_id');
    }

    public function from_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'from_user_id');
    }

    public function bank()
    {
        return $this->hasOne('App\Models\Bank', 'id', 'bank_id');
    }

    public static function files_path($dir) {
        return 'assets/dashboard/images/transactions/'.$dir.'/';
    }

    public function image_delete()
    {
        delete_img($this->image_rel_path($this->from_user->id));
    }

    public function image_rel_path()
    {
        return $this->files_path($this->from_user->id) . $this->attributes['image'];
    }

    public function  getImageAttribute($val){
        return ($val && file_exists($this->image_rel_path($this->from_user->id))) ?  asset($this->image_rel_path($this->from_user->id)) : false;
    }

    public function  imageSrc(){
        return $this->image_rel_path($this->from_user->id);
    }


}

