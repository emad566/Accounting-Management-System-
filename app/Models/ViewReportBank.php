<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class ViewReportBank extends Invoice
{
    protected$table="view_report_banks";
    protected $fillable = [
        'created_at', 
        'created_at', 
        'from_bank_name', 
        'from_bank_id', 
        'to_bank_name', 
        'to_bank_id', 
        'details', 
        'depit', 
        'credit', 
        'bank_id', 
        'type', 
        'trans_type'
    ];

    public static function bank_balance($bank_id="", $created_at_from="", $created_at_to="")
    {
        $wherebank = "";
        // $bank_created_date = '2021-01-01 00:00:00';
        if(Bank::orderBy('created_at', 'ASC')->count()>0){
            if($bank_id){
                $wherebank = " AND bank_id=". $bank_id;
                $bank = Bank::find($bank_id);
                $bank_created_date = $bank->created_at;
            }else{
    
                $bank_created_date = Bank::orderBy('created_at', 'ASC')->first()->created_at;
            }
        }else{
            $bank_created_date = "2021-01-01 00:00:00";
        }

        $queryStartDate = ($created_at_from)? $created_at_from :  $bank_created_date;
        $querEndDate = ($created_at_to)? $created_at_to : Carbon::now()->isoFormat('YYYY-MM-DD HH:MM:SS');
        $q1 = "
        SELECT
            sum(depit) - sum(credit) as  bank_balance
        FROM
            view_report_banks
            where created_at< '$queryStartDate'
        ORDER BY
            created_at ASC,
            type DESC
        ";
        // return $q1;
        $firstPeriodBalances = DB::select($q1);
        if($firstPeriodBalances){
            foreach ($firstPeriodBalances as $firstPeriodBalance) {
                 $firstPeriodBalance->bank_balance;
            }
            $firstPeriodBalance = $firstPeriodBalance->bank_balance;
        }else $firstPeriodBalance = 0;
        if(!$firstPeriodBalance) $firstPeriodBalance = 0;
        $query = "
        (SELECT
            '".$queryStartDate."' as created_at,
            '".$queryStartDate."' as date_str,
            '' as from_bank_name,
            '' as from_bank_id,
            '' as to_bank_name,
            '' as to_bank_id,
            'رصيد أول المدة' COLLATE utf8mb4_unicode_ci as details,
            IFNULL(sum(depit), 0) as depit,
            IFNULL(sum(credit), 0) as credit,
            IFNULL(sum(depit) - sum(credit), 0) as  bank_balance,
            '' as bank_id,
            'depit' COLLATE utf8mb4_unicode_ci as type,
            '' COLLATE utf8mb4_unicode_ci as trans_type
        FROM
            view_report_banks AS vrc
            where created_at< '".$queryStartDate."' ".$wherebank."
        ORDER BY
            created_at ASC,
            type DESC
        )
        UNION

        SELECT
            created_at,
            date_str,
            from_bank_name,
            from_bank_id,
            to_bank_name,
            to_bank_id,
            details,
            depit,
            credit,
        TRUNCATE
            (
                (
                    @Balance := @Balance + depit - credit
                ),
                2
            ) AS bank_balance,
            bank_id,
            type,
            trans_type
        FROM
            view_report_banks AS vrc,
            (
        SELECT
            @Balance := ".$firstPeriodBalance."
        ) AS variableInit
        WHERE
            created_at >= '".$queryStartDate."' AND created_at <= '".$querEndDate."' ".$wherebank."
        ORDER BY
            created_at ASC,
            type DESC
        ";
        // return $query;
        return DB::select($query);
    }
}
