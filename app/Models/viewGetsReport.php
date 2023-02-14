<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewGetsReport  extends Model
{

    protected $table = 'view_gets_report';
    protected $fillable = [
        'product_id',  
        'Product_Name', 
        'runID', 
        'discount', 
        'invoice_public_price', 
        'invoice_product_id', 
        'get_quantity', 
        'get_date', 
        'invoice_status_id', 
        'client_pay_for_q', 
        'state_id', 
        'state', 
        'city_id', 
        'city', 
        'vilage_id', 
        'vilage'
    ];

    public static function client_pay_sum($request=false)
    {
        $where = '';
        if($request){
            
            $where .= ' where ';

            if($request->product_id){
                if($where != ' where ') $where .= ' AND ';
                $where .= ' product_id =' .  $request->product_id;
            }


            if($request->get_date_from){
                if($where != ' where ') $where .= ' AND ';
                $where .= " get_date >='" .  $request->get_date_from ."' ";
            }

            if($request->get_date_to){
                if($where != ' where ') $where .= ' AND ';
                $where .= " get_date <='" .  $request->get_date_to ."' ";
            }

            if($request->state_id){
                if($where != ' where ') $where .= ' AND ';
                $where .= ' state_id =' .  $request->state_id;
            }

            if($request->city_id){
                if($where != ' where ') $where .= ' AND ';
                $where .= ' city_id =' .  $request->city_id;
            }

            if($request->region_id){
                if($where != ' where ') $where .= ' AND ';
                $where .= ' vilage_id =' .  $request->region_id;
            }

        }

        if($where == ' where ') $where = '';

        $q = '
        SELECT
        	sum(gets_report_sum.client_pay) as client_pay
        from 
        (
        
            SELECT 
                gets_report.get_date,
                gets_report.get_id,
                gets_report.client_pay,
                
                gets_report.state_id,
                gets_report.state,
                gets_report.city_id,
                gets_report.city,
                gets_report.vilage_id,
                gets_report.vilage

            from 
            (
                SELECT 
                gets.id as get_id,
                gets.get_date,
                gets.client_pay,
                invoices.invoice_status_id,

                carea.state_id as state_id,
                if(carea.state is null, carea.r_name, carea.state) as state,

                if(carea.city_id is null, carea.region_id, carea.city_id) as city_id,
                if(carea.city is null, carea.r_name, carea.city) as city,

                if(carea.city_id is NOT null, carea.region_id, null) as vilage_id,
                if(carea.city is NOT null, carea.r_name, null) as vilage

                from get_product LEFT JOIN gets 
                ON get_product.get_id = gets.id



                LEFT JOIN invoices
                ON gets.invoice_id = invoices.id

                LEFT JOIN clientareas carea
                ON carea.id = invoices.client_id  

                WHERE 
                invoices.invoice_status_id = 20 
            ) gets_report 
            '.$where.' 
            GROUP BY
                gets_report.get_id
        ) gets_report_sum;
        ';
        // return $q;
        $q_result = DB::select($q);
        return ['sql'=>$q, 'q_result'=>$q_result];

    }
}

