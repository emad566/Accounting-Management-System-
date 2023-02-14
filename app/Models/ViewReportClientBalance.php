<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class ViewReportClientBalance extends Invoice
{
    protected$table="view_report_clientbalance";
    protected $fillable = [
        'invoice_id',
        'invoice_code',
        'client_id',
        'client_name',
        'invoice_status_id',
        'data_str',
        'get_requireds',
        'client_pay',
        'created_date',
        'details',
        'type'
    ];

    public static function clientbalance($client_id="", $date_str_from="", $date_str_to="")
    {
        $whereClient = "";
        if($client_id)
            $whereClient = " AND client_id=". $client_id;

        $queryStartDate = ($date_str_from)? $date_str_from : '2020-01-01';
        $querEndDate = ($date_str_to)? $date_str_to : Carbon::now()->isoFormat('YYYY-MM-DD');
        $q1 = "
        SELECT
            sum(get_requireds) - sum(client_pay) as  client_balance
        FROM
            view_report_clientbalance
            where date_str< '$queryStartDate'
        ORDER BY
            date_str ASC,
            created_date ASC,
            type
        DESC
        ";

        $firstPeriodBalances = DB::select($q1);
        if($firstPeriodBalances){
            foreach ($firstPeriodBalances as $firstPeriodBalance) {
                 $firstPeriodBalance->client_balance;
            }
            $firstPeriodBalance = $firstPeriodBalance->client_balance;
        }else $firstPeriodBalance = 0;
        if(!$firstPeriodBalance) $firstPeriodBalance = 0;
        $query = "
        (SELECT
            '' as invoice_id,
            '' as invoice_code,
            '' as client_id,
            '' as client_name,
            20 as invoice_status_id,
            '".$queryStartDate."' as date_str,
            IFNULL(sum(get_requireds), 0) as get_requireds,
            IFNULL(sum(client_pay), 0) as client_pay,
            IFNULL(sum(get_requireds) - sum(client_pay), 0) as  client_balance,
            '".$queryStartDate."' as created_date,
            'رصيد اول المده' COLLATE utf8mb4_unicode_ci details,
            'invoice' COLLATE utf8mb4_unicode_ci  as type
        FROM
            view_report_clientbalance AS vrc
            where date_str< '".$queryStartDate."'  ".$whereClient."
        ORDER BY
            date_str ASC,
            created_date ASC,
            type
        DESC)
        UNION

        SELECT
            invoice_id,
            invoice_code,
            client_id,
            client_name,
            invoice_status_id,
            date_str,
            get_requireds,
            client_pay,
        TRUNCATE
            (
                (
                    @Balance := @Balance + get_requireds - client_pay
                ),
                2
            ) AS client_balance,
            created_date,
            details,
            type
        FROM
            view_report_clientbalance AS vrc,
            (
        SELECT
            @Balance := ".$firstPeriodBalance."
        ) AS variableInit
        WHERE
            date_str >= '".$queryStartDate."' AND date_str <= '".$querEndDate."' ".$whereClient."
        ORDER BY
            date_str ASC,
            created_date ASC,
            type
        DESC

        ";
        // return $query;
        return DB::select($query);
    }
}
