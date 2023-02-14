<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Spatie\Activitylog\Traits\LogsActivity;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    use Translatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $with = ['translations'];
    protected $translatedAttributes = ['fName', 'lName'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'translations',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function userImage()
    {
        if($this->profile_photo_path){
            return asset('storage/'.$this->profile_photo_path);
        }else{
            return asset('assets/dashboard/img/user/profile.png');
        }
    }

    public function profile_photo_urlSrc()
    {
        if($this->profile_photo_path){
            return 'storage/'.$this->profile_photo_path;
        }else{
            return 'assets/dashboard/img/user/profile.png';
        }
    }

    public function imageSrc()
    {
        if($this->image){
            return $this->image_rel_path();
        }else{
            return 'assets/dashboard/img/user/profile.png';
        }
    }

    public static function files_path() {
        return 'assets/dashboard/images/users/';
    }

    public function image_delete()
    {
        delete_img($this->image_rel_path());
    }

    public function image_rel_path()
    {
        return $this->files_path() . $this->attributes['image'];
    }

    public function  getImageAttribute($val){
        return ($val && file_exists($this->image_rel_path())) ?  asset($this->image_rel_path()) : false;
    }


    public function getActive(){
        return  $this -> is_active  == 0 ?  trans('main.is_not_active')   : trans('main.is_active') ;
     }

    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }
}
