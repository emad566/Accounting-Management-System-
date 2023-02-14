<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Store extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'Store_Name',
        'Store_Place',
        'Store_Type',
        'is_active',
    ];

    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    public function products()
    {
        return $this->hasMany('App\Models\ViewStockClosed');
    }

    public function regions()
    {
        return $this->belongsToMany('App\Models\Region', 'store_region')->withPivot('id', 'store_id', 'region_id');
    }

    public function stock_update($data, $opt='inc')
    {
        $storestocks = $this->storestocks->toArray();

        DB::beginTransaction();
        
        foreach($data  as $d ){
            $row = $this->storestocks->where('product_id',$d['product_id'])->where('runID', '===', $d['runID'])->first();
            if($row){
                $row->update([
                    'product_id'=>$d['product_id'],
                    'q_in_store'=>array_key_exists('q_in_store', $d)? add($row->q_in_store, $d['q_in_store'], $opt) : $row->q_in_store,
                    'store_q_net'=>array_key_exists('store_q_net', $d)? add($row->store_q_net, $d['store_q_net'], $opt) : $row->store_q_net,
                    'q_reversed'=>array_key_exists('q_reversed', $d)? add($row->q_reversed, $d['q_reversed'], $opt) : $row->q_reversed,
                    'transfer_q_reserved'=>array_key_exists('transfer_q_reserved', $d)? add($row->transfer_q_reserved, $d['transfer_q_reserved'], $opt) : $row->transfer_q_reserved,
                    'transfer_in'=>array_key_exists('transfer_in', $d)? add($row->transfer_in, $d['transfer_in'], $opt) : $row->transfer_in,
                    'transfer_out'=>array_key_exists('transfer_out', $d)? add($row->transfer_out, $d['transfer_out'], $opt) : $row->transfer_out,
                ]);
            }else if($opt == '+') {
                $this->storestocks()->create([
                    'product_id'=>$d['product_id'],
                    'runID'=>$d['runID'],
                    'q_in_store'=>array_key_exists('q_in_store', $d)? $d['q_in_store'] : 0,
                    'store_q_net'=>array_key_exists('store_q_net', $d)? $d['store_q_net'] : 0,
                    'q_reversed'=>array_key_exists('q_reversed', $d)? $d['q_reversed'] : 0,
                    'transfer_q_reserved'=>array_key_exists('transfer_q_reserved', $d)? $d['transfer_q_reserved'] : 0,
                    'transfer_in'=>array_key_exists('transfer_in', $d)? $d['transfer_in'] : 0,
                    'transfer_out'=>array_key_exists('transfer_out', $d)? $d['transfer_out'] : 0,
                ]);
            }
        }

        DB::commit();
        return true;
    }

    public function storestocks()
    {
        return $this->hasMany(Storestock::class, 'store_id', 'id');
    }
    
    public function storeStocksNoRunId()
    {
        return $this->hasMany(ViewStockClosedGroupby::class, 'store_id', 'id');
    }
    
    public function viewStockClosedSql()
    {
        return $this->hasMany(ViewStockClosedSql::class, 'store_id', 'id');
    }

    public function viewstorestocks()
    {
        return $this->hasMany(ViewStorestock::class, 'store_id', 'id');
    }
    

    /* ==== New store stock update: 
        Done inpermit:store/update/delete
        Done outpermit:store/update/delete
        
        transfer:
                Done: store
                -- Under mentains: update
                delete:
                    Done 10  تم الأنشاء
                    Done30  مرفوض - (مرتجع من الشحن)
                Status:
                    Done 10  تم الأنشاء
                    Done 20  قيد الشحن       
                    Done 30  مرفوض - (مرتجع من الشحن)
                    Done 40  تم التسليم

        voucher:
                Done store
                -- update
                Done delete:
                    Done 1   انتظار موافقة المحاسب
                    Done 2   موافقة المحاسب
                    Done 100 مرفوض

                Status:
                    Done 1   انتظار موافقة المحاسب
                    Done 2   موافقة المحاسب
                    Done 3   خرج من المخزن
                    Done 4   تم التسوية
                    -- 5   مغلق/تسوية كاملة نقدي
                    Done 6 طلب تسوية
                    Done 100 مرفوض

                Done open: 
                    
    */



}
