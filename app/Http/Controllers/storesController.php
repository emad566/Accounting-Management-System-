<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Region;
use App\Models\User;
use App\Models\ViewStock;
use DB;
use Illuminate\Support\Facades\Auth;

class storesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->can(['Shaw all stores'])){
            $stores = Store::all();
        }else{
            $stores = Auth::user()->stores;
        }

        $users = User::all();
        $states = Region::allStates()->get();
        return view('dashboard.stores.index', compact(['stores', 'users', 'states']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $states = Region::allStates()->get();
        return view('dashboard.stores.create', compact('users', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {

        DB::beginTransaction();
        $inputs = $request->except('_token');
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $inputs['user_id'] = Auth::id();
        $store = Store::create($inputs);

        if($request->users)
            $store->users()->sync($request->users);

        if($request->regions)
            $store->regions()->sync($request->regions);

        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('stores.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        $users = User::all();
        $states = Region::allStates()->get();
        $is_StoreOwner =  (Auth::user()->stores->where('id', $store->id)->first()) ? true : false;
        if(Auth::user()->can(['Shaw all stores']) || $is_StoreOwner){
            $users = User::all();
            return view('dashboard.stores.show', compact(['store', 'users', 'states']));
        }else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        $users = User::all();
        $states = Region::allStates()->get();
        return view('dashboard.stores.edit', compact(['store', 'users', 'states']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreRequest  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, Store $store)
    {
        DB::beginTransaction();
        $inputs = $request->except('_token');
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;
        if($store->id == 1) $inputs['is_active'] = 1;

        if($request->users)
            $store->users()->sync($request->users);

        if($request->regions)
            $store->regions()->sync($request->regions);

        $store = $store->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('stores.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy($store_id)
    {
        if($store_id == 1){
            $notification = array(
                'message' => 'لا يمكن  حذف أو تعطيل المخزن الرئيسي',
                'alert-type' => 'error',
                'error' => 'لا يمكن  حذف أو تعطيل المخزن الرئيسي',
            );

            return redirect()->route('stores.index')->with($notification);
        }

        $store = Store::findOrFail($store_id);

        $count = $store->products->where('q_net', '>' , 0)->count();
        if($count){
            $notification = notification('عذرا، لا يمكن حذف مخزن به منتجات...', false);
            return redirect()->route('stores.index')->with($notification);
        }

        try {
            $store->forceDelete();
        } catch (\Throwable $th) {
            $store = Store::findOrFail($store_id);
            $store->delete();
        }


        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('stores.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $store_ids = $request->stores;
        if($store_ids){
            foreach($store_ids as $store_id){
                $store = Store::find($store_id);
                if($store && $store->id != 1){
                    $count = $store->products->where('q_net', '>' , 0)->count();
                    if($count){
                        $notification = notification('عذرا، لا يمكن حذف مخزن به منتجات...', false);
                        return redirect()->route('stores.index')->with($notification);
                    }

                    try {
                        $store->forceDelete();
                    } catch (\Throwable $th) {
                        $store = Store::find($store_id);
                        $store->delete();
                    }
                }

            }

            DB::commit();
            $notification = array(
                'message' => 'تم الحذف بنجاح',
                'alert-type' => 'success',
                'success' => 'تم الحذف بنجاح',
            );
        }else{
            DB::commit();
            $notification = array(
                'message' => 'حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
                'alert-type' => 'error',
                'error' => 'حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
            );
        }

        return redirect()->route('stores.index')->with($notification);
    }

    public function updateIsActive(Request $request, $store_id)
    {
        if($store_id == 1){
            $notification = array(
                'message' => 'لا يمكن  حذف أو تعطيل المخزن الرئيسي',
                'alert-type' => 'error',
                'error' => 'لا يمكن  حذف أو تعطيل المخزن الرئيسي',
            );

            return redirect()->route('stores.index')->with($notification);
        }
        try {
            $store = Store::findOrFail($store_id);
            DB::beginTransaction();
            if($store){
                $is_active = ($store->is_active)? 0 : 1;
            }

            $store->update(['is_active'=>$is_active]);
            DB::commit();

            $notification = array(
                'message' => 'تم حفظ التعديلات بنجاح',
                'alert-type' => 'success',
                'success' => 'تم حفظ التعديلات بنجاح',
            );

            return redirect()->route('stores.index')->with($notification);

        } catch (\Exception $ex) {
            return redirect()->route('stores.index')->with(['error' => $this->getFileNameError('updateIsActive')]);
        }
    }

    
}
