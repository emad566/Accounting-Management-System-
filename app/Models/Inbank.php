<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Inbank extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    protected $table = 'inbanks';
    protected $fillable = [
        'create_user_id',
        'inbank_code',
        'inbank_date',
        'inbank_amount',
        'to_bank_id',
        'inbank_details',
        'transaction_status_id',
        'image',
        'accept_user_id'
    ];

    public function to_bank()
    {
        return $this->hasOne('App\Models\Bank', 'id',  'to_bank_id');
    }

    public function create_user()
    {
        return $this->hasOne('App\Models\User', 'id',  'create_user_id');
    }

    public function accept_user()
    {
        return $this->hasOne('App\Models\User', 'id',  'accept_user_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\TransactionStatus', 'id', 'transaction_status_id');
    }


    public static function files_path($dir) {
        return 'assets/dashboard/images/inbanks/'.$dir.'/';
    }

    public function image_delete()
    {
        delete_img($this->image_rel_path($this->to_bank->id));
    }

    public function image_rel_path()
    {
        return $this->files_path($this->to_bank->id) . $this->attributes['image'];
    }

    public function  getImageAttribute($val){
        return ($val && file_exists($this->image_rel_path($this->to_bank->id))) ?  asset($this->image_rel_path($this->to_bank->id)) : false;
    }

    public function  imageSrc(){
        return $this->image_rel_path($this->to_bank->id);
    }
}

