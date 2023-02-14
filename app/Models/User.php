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
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
// class User extends Authenticatable implements MustVerifyEmail

class User extends Authenticatable
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    use SoftDeletes;
    use HasRoles;


    protected $guard_name = 'sanctum';



    protected $fillable = [
        'name',
        'email',
        'fName',
        'lName',
        'fullName',
        'password',
        'image',
        'voucher_id',
        'last_time',
        'rep_limit',
        'is_multi_due_inherit_id',
        'phone',
        'is_active',
        'dt_font_size'
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

    public function last_time()
    {
        return Carbon::now()->isoFormat('YYYY-MM-DD') . " " . $this->last_time ;
    }

    public function is_force_transaction()
    {
        $gets = $this->gets->where('created_at', '>=', $this->last_time())->sum('client_pay');

        $usb = ($this->usergets)? $this->usergets->user_safer_balance : 0;
        $gets =  $usb - $gets;
        return ($gets > $this->rep_limit) ? true : false;
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function getLastTImeAttribute($value)
    {
        if($value){
            return substr($value,0,5);
        }else{
            if(Generalpolicy::find(1)){
                return Generalpolicy::find(1)->last_time;
            }else{
                return "يرث من سياسات عامة";
            }

        }
    }

    public function getRepLimitAttribute($value)
    {
        if($value){
            return $value;
        }else{
            if(Generalpolicy::find(1)){
                return Generalpolicy::find(1)->rep_limit;
            }else{
                return "يرث من سياسات عامة";
            }

        }
    }

    public function getIsMultiDueInheritIdAttribute($value)
    {
        if($value && $value != 30){
            return $value;
        }else{
            if(Generalpolicy::find(1) && Generalpolicy::find(1)->is_multi_due){
                return 10;
            }else{
                return 20;
            }

        }
    }

    public function is_multi_due_inherit()
    {
        return $this->hasOne('App\Models\Isinherit', 'id', 'is_multi_due_inherit_id');
    }

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
            return asset('assets/dashboard/img/user/profile1.png');
        }
    }

    public function profile_photo_urlSrc()
    {
        if($this->profile_photo_path){
            return 'storage/'.$this->profile_photo_path;
        }else{
            return 'assets/dashboard/img/user/profile1.png';
        }
    }

    public function imageSrc()
    {
        if($this->image){
            return $this->image_rel_path();
        }else{
            return 'assets/dashboard/img/user/profile1.png';
        }
    }

    public static function files_path() {
        return 'assets/dashboard/images/users/';
    }

    public function image_delete()
    {
        if($this->attributes['image'])
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

    public function getRole($attr='')
    {
        if($this->roles->isEmpty())
            return false;

        return ($attr) ? $this->roles()->first()->$attr : $this->roles()->first();
    }

    public function vouchers()
    {
        return $this->hasMany('App\Models\Voucher', 'user_rep_id', 'id')->orderBy('voucher_status', 'ASC');
    }

    public function vouchers_ordered()
    {
        return $this->hasMany('App\Models\VoucherOrdered', 'user_rep_id', 'id')->orderBy('order', 'ASC');
    }

    public function gets()
    {
        return $this->hasMany('App\Models\Get', 'user_rep_id', 'id')->orderBy('get_date', 'DESC');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'user_rep_id', 'id');
    }

    public function voucher()
    {
        return $this->hasOne('App\Models\Voucher', 'id', 'voucher_id');
    }

    public function usergets()
    {
        return $this->hasOne('App\Models\ViewUserGet', 'user_rep_id', 'id');
    }

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store');
    }
    
    public function storestocks()
    {
        return $this->belongsToMany('App\Models\UserStorestock');
    }

    public function session()
    {
        return $this->hasOne(Session::class, 'user_id', 'id');
    }

    public function setPassword($value)
    {
        return $this->attributes['password'] = bcrypt($value);
    }

    public function notifs()
    {
        return $this->hasMany('App\Models\ViewNotif', 'user_id', 'id')->orderBy('user_read_at', 'ASC')->orderBy('created_at', 'DESC');
    }

    public function notifsCount()
    {
        return $this->notifs->whereNull('readed_at')->count();
    }
    
    public function notifs_html()
    {
        $html = "";
        foreach ($this->notifs->take(15) as $notif) {
            $noteClass = ($notif->user_read_at != '')? ' readed fa fa-check-circle ' : '';
            $noteClass .= ($notif->readed_at != '' && $notif->readed_by_user_id != $this->id)?  ' online ' : '';

            $html .= '<a class="notiflink" notId="' .$notif->id .'" href="' .route($notif->table_name.'.show', $notif->notifiable_id) .'">
                    <div class="user-img notifications"> <img
                        src="' .getSrc($notif->user_create, 'image') .'"
                        alt="user" class="img-circle"> <span class="profile-status '.$noteClass.'  pull-right"></span>
                    </div>
                    <div class="mail-contnet">
                        <h5>' .$notif->user_create->fullName .'</h5> <span class="mail-desc" title="' .$notif->notif_html() .'">' .$notif->notif_html() .'</span> <span
                        class="time">' . Carbon::parse($notif->created_at)->diffForHumans() .'</span>
                    </div>
                </a>';
        }

        return $html;
    }

    

    public static function users_allow($permissions=[], $obj, $user_id_column='id')
    {
        $permissions_users_ids = ViewPermission::where('model_type', 'App\\Models\\User')
        ->whereIn('name', $permissions)->pluck('model_id');

        $store_users = NULL;
        if($permissions_users_ids && $obj){
            $store_users = $obj->whereIn($user_id_column, $permissions_users_ids);
        }

        return $store_users;
    }

}
