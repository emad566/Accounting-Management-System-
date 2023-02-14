<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;

class storestocksController extends Controller
{
    public function stock($store_id="")
    {
        $is_StoreOwner =  (Auth::user()->stores->where('id', $store_id)->first()) ? true : false;
        if(Auth::user()->can(['Inventory all stores']) || $is_StoreOwner || $store_id==""){
            $start = microtime(true);
            $store = Store::find($store_id);
            if(Auth::user()->can(['Inventory all stores'])){
                // $stores = Store::has('products')->select('id','Store_Name')->where('is_active', 1)->orderBy('Store_Name')->get();
                $stores = Store::select('id','Store_Name')->where('is_active', 1)->orderBy('Store_Name')->get();
            }else{
                $stores = Auth::user()->stores;
                if($store && !$is_StoreOwner){
                    abort(403, 'Unauthorized action.');
                }
            }

            $time_elapsed_secs = microtime(true) - $start;

            // return $time_elapsed_secs;
            return view('dashboard.storestocks.stock', compact(['stores', 'store']));
        }
        abort(403, 'Unauthorized action.');
    }
}
