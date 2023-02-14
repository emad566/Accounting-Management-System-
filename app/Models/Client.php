<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'region_id',
        'client_name',
        'client_phone',
        'client_address',
        'client_manager_name',
        'client_manager_phone',
        'is_active',
        'is_first_add',
        'client_due_limit',
        'is_multi_due_inherit_id',
        'initial_balance',
    ];

    public function client_due_limit()
    {
        if($this->client_due_limit) return $this->client_due_limit;
        $region_id = Region::policy_region_id($this->region_id, 'client_due_limit');
    //    return ($region_id)? $region_id :

        if ($region_id && Region::find($region_id))
            return Region::find($region_id)->client_due_limit();

        return ($this->client_due_limit)? $this->client_due_limit : 'يرث من سياسات المنطقة';
    }

    public function is_multi_due_inherit_name($product_id="")
    {
        // if($product_id){
        //     $porductpolicy = ProductPolicy::where()
        // }
        $region_id = Region::policy_region_id($this->region_id, 'is_multi_due_inherit_id');
        if ($region_id && Region::find($region_id)){
            return Region::find($region_id)->is_multi_due_inherit->name;
        }

        return $this->is_multi_due_inherit->name;
    }

    public function allow_due_product($product_id, $invoic_id)
    {
        $client_id = $this->id;
        $multi_due_product = ViewInvoiceProduct::where(['client_id'=>$client_id,
                                                 'product_id'=>$product_id])
                            ->where('get_quantity_next', '>', 0)->where('invoice_id', '<>', $invoic_id)->first();
        if($multi_due_product){
            $is_multi_due = $this->is_multi_due_inherit_pro($product_id);
            if(!$is_multi_due){
                return 'لا يمكن تنزيل منتج للعميل و عليه أجل منه، هناك أجل علي هذا المنتج في الفاتورة رقم '. $multi_due_product->invoice->invoice_code . ' قم بتحصيلة أولا';
            }
        }
        return '';
    }

    public function paid_discount($product_id)
    {
        $client_id = $this->id;
        $porductpolicy = ProductPolicy::where(['client_id'=>$client_id, 'product_id'=>$product_id])->first();
        if($porductpolicy && $porductpolicy->paid_discount){
            return $porductpolicy->paid_discount;
        }else{
            $region_id = Region::policy_region_id($this->region_id, 'paid_discount');
            $product = Product::find($product_id);
            if($product->paid_discount){
                return $product->paid_discount;
            }elseif($product->paid_discount($region_id)){
                return $product->paid_discount($region_id);
            }else{
                $generalpolicy = Generalpolicy::find(1);
                if($generalpolicy->paid_discount){
                    return $generalpolicy->paid_discount;
                }
            }
        }
        return 0;
    }

    public function due_discount($product_id)
    {
        $client_id = $this->id;
        $porductpolicy = ProductPolicy::where(['client_id'=>$client_id, 'product_id'=>$product_id])->first();
        if($porductpolicy && $porductpolicy->due_discount){
            return $porductpolicy->due_discount;
        }else{
            $region_id = Region::policy_region_id($this->region_id, 'due_discount');
            $product = Product::find($product_id);
            if($product->due_discount){
                return $product->due_discount;
            }elseif($product->due_discount($region_id)){
                return $product->due_discount($region_id);
            }else{
                $generalpolicy = Generalpolicy::find(1);
                if($generalpolicy->due_discount){
                    return $generalpolicy->due_discount;
                }
            }
        }
        return 0;
    }

    public function is_multi_due_inherit_pro($product_id)
    {

        $client_id = $this->id;
        $porductpolicy = ProductPolicy::where(['client_id'=>$client_id, 'product_id'=>$product_id])->first();

        if($porductpolicy && $porductpolicy->is_multi_due_inherit_id == 10){
            return false;
        }elseif($porductpolicy && $porductpolicy->is_multi_due_inherit_id == 20){
            return true;
        }elseif(!$porductpolicy || ($porductpolicy && $porductpolicy->is_multi_due_inherit_id == 60)){
            if($this->is_multi_due_inherit_id == 10){
                return false;
            }elseif($this->is_multi_due_inherit_id == 20){
                return true;
            }elseif($this->is_multi_due_inherit_id == 40){
                $product = Product::find($product_id);
                if($product){
                    if($product->is_multi_due_inherit_id == 10){
                        return false;
                    }elseif($product->is_multi_due_inherit_id == 20){
                        return true;
                    }elseif($product->is_multi_due_inherit_id == 50){
                        $region_id = Region::policy_region_id($this->region_id, 'is_multi_due_inherit_id');
                        if ($region_id && Region::find($region_id)){
                            $is_multi_due_inherit_id = Region::find($region_id)->is_multi_due_inherit_id;
                            if($is_multi_due_inherit_id == 10){
                                return false;
                            }elseif($is_multi_due_inherit_id == 20){
                                return true;
                            }
                        }
                    }
                }
            }
        }

        $generalpolicy = Generalpolicy::find(1);
        if($generalpolicy->is_multi_due){
            return true;
        }else{
            return false;
        }
    }

    public function is_multi_due_inherit()
    {
        return $this->hasOne('App\Models\Isinherit', 'id', 'is_multi_due_inherit_id');
    }


    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

    public function clientarea()
    {
        return $this->hasOne('App\Models\Clientarea', 'id', 'id');
    }

    public function view_client()
    {
        return $this->hasOne('App\Models\ViewClient', 'id', 'id');
    }

    public function view_client_accounting()
    {
        return $this->hasOne('App\Models\ViewClientAccounting', 'client_id', 'id');
    }

    public function client_type()
    {
        return $this->hasOne('App\Models\ClientType', 'id', 'client_type_id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'client_id', 'id');
    }
    
    
    public function view_report_clientbalance()
    {
        return $this->hasMany('App\Models\ViewReportClientBalance', 'client_id', 'id');
    }

    public function productpolicys()
    {
        return $this->hasMany('App\Models\ProductPolicy', 'client_id', 'id');
    }

}


