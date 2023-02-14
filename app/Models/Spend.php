<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Spend extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    protected $table = 'spends';
    protected $fillable = [
        'cat_id',
        'bank_id',
        'spend_code',
        'region_id',
        'spend_user_id',
        'spend_amount',
        'spend_date',
        'spend_details',
        'recieve_details',
        'recieve_user_id',
        'transaction_status_id',
        'image',
        'accept_user_id',
        'date_str',
        'updated_at'
    ];

    public function spend_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'spend_user_id');
    }

    public function recieve_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'recieve_user_id');
    }

    public function accept_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'accept_user_id');
    }

    public function cat()
    {
        return $this->hasOne('App\Models\Cat', 'id', 'cat_id');
    }

    public function bank()
    {
        return $this->hasOne('App\Models\Bank', 'id', 'bank_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\TransactionStatus', 'id', 'transaction_status_id');
    }

    public static function files_path($dir) {
        return 'assets/dashboard/images/spends/'.$dir.'/';
    }

    public function image_delete()
    {
        delete_img($this->image_rel_path($this->spend_user->id));
    }

    public function image_rel_path()
    {
        return $this->files_path($this->spend_user->id) . $this->attributes['image'];
    }

    public function  getImageAttribute($val){
        return ($val && file_exists($this->image_rel_path($this->spend_user->id))) ?  asset($this->image_rel_path($this->spend_user->id)) : false;
    }

    public function  imageSrc(){
        return $this->image_rel_path($this->spend_user->id);
    }

    public static function cat_balance($cat_id="", $date_str_from="", $date_str_to="", $request = "")
    {
        $wherecat = "";
        if($cat_id){
            $wherecat = " AND cat_id=". $cat_id;
        }

        if($request){
            if($request->state_id){
                $wherecat .= " AND state_id=". $request->state_id;
            }

            if($request->city_id){
                $wherecat .= " AND (city_id=". $request->city_id ." OR region_id=". $request->city_id.") ";
            }

            if($request->region_id){
                $wherecat .= " AND region_id=". $request->region_id;
            }
        }



        $queryStartDate = ($date_str_from)? $date_str_from :  Carbon::now()->isoFormat('2021-01-01');
        $querEndDate = ($date_str_to)? $date_str_to : Carbon::now()->isoFormat('YYYY-MM-DD');
        
        $q1 = "
        SELECT
            sum(depit) - sum(credit) as  cat_balance
        FROM
            view_spends
            where spend_date < '$queryStartDate'
        ORDER BY
            spend_date ASC,
            created_at ASC
        ";
        // return $q1;
        $firstPeriodBalances = DB::select($q1);
        if($firstPeriodBalances){
            foreach ($firstPeriodBalances as $firstPeriodBalance) {
                 $firstPeriodBalance->cat_balance;
            }
            $firstPeriodBalance = $firstPeriodBalance->cat_balance;
        }else $firstPeriodBalance = 0;
        if(!$firstPeriodBalance) $firstPeriodBalance = 0;
        $query = 
        "
        SELECT
            '".$queryStartDate."' as created_at,
            '".$queryStartDate."' as date_str,
            '' as cat_id,
            '' as cat_name,
            '' as spend_code,
            '' as region_id,
            '' as r_name,
            '' as city_id,
            '' as city,
            '' as state_id,
            '' as state,
            'رصيد أول المدة' as details,
            IFNULL(sum(depit), 0) as depit,
            IFNULL(sum(credit), 0) as credit,
            IFNULL(sum(depit) - sum(credit), 0) as  cat_balance
        FROM
            view_spends
            where spend_date< '".$queryStartDate."' ".$wherecat."
        
        UNION
        
        SELECT
            created_at,
            spend_date as date_str,
            cat_id,
            cat_name,
            spend_code,
            region_id,
            r_name,
            city_id,
            city,
            state_id,
            state,
            spend_details as details,
            spend_amount as depit,
            0 as credit,
            TRUNCATE
                (
                    (
                        @Balance := @Balance + depit - credit
                    ),
                    2
                ) AS cat_balance
        FROM
            view_spends AS vs,
            (
        SELECT
            @Balance := ".$firstPeriodBalance."
        ) AS variableInit
        WHERE
            spend_date >= '".$queryStartDate."' AND spend_date <= '".$querEndDate."' ".$wherecat."
        ORDER BY
            date_str ASC,
            created_at ASC
            
        ";
        // return $query;
        return DB::select($query);
    }
}

