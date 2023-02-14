<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
class ViewReportInvoiceGet extends Invoice
{
    protected$table="view_report_invoiceget";
    protected $fillable = [
        'invoice_id',
        'client_id',
        'product_id',
        'Product_Name',
        'user_rep_id',
        'user_rep_name',
        'user_rep_fullName',
        'runID',
        'invoice_public_price',
        'discount',
        'invoice_bounce_net',
        'invoice_net_q_withoutbounce',
        'get_required',
        'get_paid',
        'get_next',
        'client_name',
        'region_id',
        'r_name',
        'city_id',
        'city',
        'state_id',
        'state',
        'client_type_id',
        'client_type_name',
        'invoice_code',
        'invoice_date',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        // 'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getCityAttribute($value)
    {
        if($value){
            return $value;
        }else if(!$this->city_id){
            return $this->getRawOriginal('r_name');
        }else{
            return '';
        }
    }

    public function getRNameAttribute($value)
    {
        if($this->city_id){
            return $value;
        }else{
            return '';
        }
    }
    
    public function getCreatedAtAttribute($value)
    {
        $createdAt = Carbon::parse($value);
        return $createdAt->format('Y-m-d H:i:s');
    }
}
