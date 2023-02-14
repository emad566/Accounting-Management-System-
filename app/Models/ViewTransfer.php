<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class ViewTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_code',
        'transfer_details',
        'transfer_status_id',
        'transfer_name',
        'transfer_phone',
        'transfer_date',
        'from_store_id',
        'to_store_id',
        'user_id',
        'updated_at',
        'created_at',
        'status_name',
        'from_store_name',
        'to_store_name'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'transfer_product')->withPivot('id', 'transfer_id', 'product_id', 'Quantity', 'RunID');
    }

    public function hasManyProducts()
    {
        return $this->hasMany('App\Models\TransferProduct', 'transfer_id', 'id');
    }


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\TransferStatus', 'id', 'transfer_status_id');
    }

    public function storeFrom()
    {
        return $this->hasOne('App\Models\Store', 'id', 'from_store_id');
    }

    public function storeTo()
    {
        return $this->hasOne('App\Models\Store', 'id', 'to_store_id');
    }

    public static function storeproducttransfer($request)
    {
        $fd = "2021-01-01 00:00:00";
        $ld = Carbon::now()->isoFormat('YYYY-MM-DD HH:MM:DD');

        if (ViewTransfer::orderBy('created_at', 'ASC')->count()){
            $fd = ViewTransfer::orderBy('created_at', 'ASC')->first()->created_at;
            $ld = ViewTransfer::orderBy('created_at', 'DESC')->first()->created_at;
        }

        $whereStore_id = "";
        $whereProduct_id = "";

        if($request){
            if($request->fd)
                $fd = $request->fd . " 00:00:00";

            if($request->ld)
                $ld = $request->ld . " 23:59:59";

            if ($request->store_id && is_numeric($request->store_id))
                $whereStore_id = " AND (vt.from_store_id=".$request->store_id." OR vt.to_store_id=".$request->store_id.")";

            if ($request->product_id && is_numeric($request->product_id))
                $whereProduct_id = " AND tp.product_id=".$request->product_id;
        }

        $where = " Where vt.created_at>='".$fd."' AND vt.created_at<='".$ld ."' ".$whereStore_id.$whereProduct_id;

        $q = '
        SELECT
            vt.*,
            pro.Product_Name,
            tp.transfer_id,
            tp.product_id,
            sum(tp.Quantity) as Quantity
        from view_transfers vt
        LEFT JOIN transfer_product tp
        on vt.id = tp.transfer_id
        LEFT JOIN products pro
        On tp.product_id=pro.id
        '.$where.'
        GROUP BY vt.from_store_id, vt.to_store_id, vt.id, tp.product_id
        ORDER by created_at ASC
        ';
        // return $q;
        return DB::select($q);
    }

}
