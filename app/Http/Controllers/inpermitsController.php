<?php

namespace App\Http\Controllers;

use App\Http\Requests\InpermitRequest;
use Illuminate\Http\Request;
use App\Models\Inpermit;
use App\Models\InpermitProduct;
use App\Models\Outpermit;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Transfer;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class inpermitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inpermits = Inpermit::orderBy('created_at', 'DESC')->get();
        $suppliers = Supplier::active()->orderBy('Sup_Name')->get();
        $products = Product::active()->orderBy('Product_code', 'ASC')->get();
        
        return view('dashboard.inpermits.index', compact(['inpermits', 'suppliers', 'products']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::active()->orderBy('Sup_Name')->get();
        $products = Product::active()->orderBy('Product_Name', 'ASC')->with('run_ids', function ($q)
        {
            return $q->select(['product_id', 'runID', 'Public_Price'])->orderBy('Public_Price', 'DESC')->groupBy('Public_Price');

        })->get();

        // return $products;

        return view('dashboard.inpermits.create', compact(['suppliers', 'products']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\InpermitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InpermitRequest $request)
    {


        $inputs = $request->except('_token');
        $inputs['user_id'] = Auth::id();

        if (!$request->inpermit_code) {
            do {
                $inputs['inpermit_code'] = tb_code();
            } while (Inpermit::where('inpermit_code', $inputs['inpermit_code'])->count() != 0);
        }

        $i = 0;

        $attachProducts = [];
        $quantities = $request->quantities;
        $runIDs = $request->runIDs;
        $Public_Prices = $request->Public_Prices;
        $Buy_Prices = $request->Buy_Prices;
        $create_dates = $request->create_dates;
        $expire_dates = $request->expire_dates;
        $flagReturn = false;

        $store_stock_arr = [];

        foreach ($request->product_ids as $product_id) {
            if (is_numeric($product_id) && array_key_exists($i, $quantities)) {
                $product = Product::find($product_id);
                if ($product && is_numeric($quantities[$i])) {
                    $inp_pro = inpermitsController::runIDcheck($product_id, $runIDs[$i], true);

                    $create_date = (!$inp_pro) ? $create_dates[$i] : $inp_pro->create_date;
                    $expire_date = (!$inp_pro) ? $expire_dates[$i] : $inp_pro->expire_date;

                    $create_date = Carbon::createFromFormat('Y-m-d', $create_date);
                    $expire_date = Carbon::createFromFormat('Y-m-d', $expire_date);

                    if ($create_date->gt($expire_date)) {
                        $notification = notification('تاريخ انتهاء الصلاحية يجب ان يكون اكبر من الانتاج!', false);
                        return back()->withInput()->with($notification);
                    }

                    if($inp_pro && number_format((float)$inp_pro->Public_Price, 2, '.', '') !=  number_format((float)$Public_Prices[$i], 2, '.', '')){
                        if ($create_date->gt($expire_date)) {
                            $notification = notification('لا يمكن ادخال منتج بنفس رقم التشغيلة بسعر مختلف', false);
                            return back()->withInput()->with($notification);
                        } 
                    }

                    if (!$inp_pro) {
                        $attachProducts[$i] = [
                            'product_id' => $product_id,
                            'Quantity' => $quantities[$i],
                            'Buy_Price' => $Buy_Prices[$i],
                            'Public_Price' => $Public_Prices[$i],
                            'runID' => $runIDs[$i],
                            'create_date' => $create_date,
                            'expire_date' => $expire_dates[$i],
                        ];
                    } elseif ($inp_pro) {
                        $attachProducts[$i] = [
                            'product_id' => $product_id,
                            'Quantity' => $quantities[$i],
                            'Buy_Price' => $inp_pro->Buy_Price,
                            'Public_Price' => $inp_pro->Public_Price,
                            'runID' => $inp_pro->runID,
                            'create_date' => $inp_pro->create_date,
                            'expire_date' => $inp_pro->expire_date,
                        ];
                    }

                    array_push($store_stock_arr, [
                        'product_id' => $attachProducts[$i]['product_id'],
                        'runID' => $attachProducts[$i]['runID'],
                        'q_in_store' => $attachProducts[$i]['Quantity'],
                        'store_q_net' => $attachProducts[$i]['Quantity'],
                    ]);
                } else $flagReturn = true;
            } else $flagReturn = true;
            $i++;

            if ($flagReturn) {

                $notification = array(
                    'message' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                    'alert-type' => 'error',
                    'error' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                );

                return redirect()->route('inpermits.index')->with($notification);
            }
        }



        DB::beginTransaction();
        $inpermit = Inpermit::create($inputs);
        $inpermit->products()->sync($attachProducts);

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr, '+');

        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('inpermits.index')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inpermit  $inpermit
     * @return \Illuminate\Http\Response
     */
    public function show(Inpermit $inpermit)
    {
        $suppliers = Supplier::active()->orderBy('Sup_Name')->get();
        $products = Product::active()->orderBy('Product_code', 'ASC')->get();
        return view('dashboard.inpermits.show', compact(['inpermit', 'suppliers', 'products']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inpermit  $inpermit
     * @return \Illuminate\Http\Response
     */
    public function edit(Inpermit $inpermit)
    {
        return "قيد التحديث";
        // return $inpermit->created_at;
        // return Carbon::createFromFormat('Y-m-d H:i:sO', $inpermit->created_at);
        $transfers = Transfer::where('created_at', '>', $inpermit->created_at)->where(function ($q) {
            return $q->where('from_store_id', 1)->orWhere('to_store_id', 1);
        })->get();

        if ($transfers && $transfers->count() > 0) {
            $notification = notification('عذرا لا يمكن عمل تعديل علي فاتورة تم بعدها عمل حركات علي المخزن الرئيسي', false);
            return back()->withInput()->with($notification);
        }

        if ($inpermit->outpermits && $inpermit->outpermits->count() > 0) {
            $notification = notification('لا يمكن تعديل فاتورة تم عمل مرتجع عليها', false);
            return back()->withInput()->with($notification);
        }


        $suppliers = Supplier::active()->orderBy('Sup_Name')->get();
        $products = Product::active()->orderBy('Product_code', 'ASC')->get();
        return view('dashboard.inpermits.edit', compact(['inpermit', 'suppliers', 'products']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\InpermitRequest  $request
     * @param  \App\Models\Inpermit  $inpermit
     * @return \Illuminate\Http\Response
     */
    public function update(InpermitRequest $request, Inpermit $inpermit)
    {
        return "قيد التحديث";
        $transfers = Transfer::where('created_at', '>', $inpermit->created_at)->where(function ($q) {
            return $q->where('from_store_id', 1)->orWhere('to_store_id', 1);
        })->get();

        if ($transfers && $transfers->count() > 0) {
            $notification = notification('عذرا لا يمكن عمل تعديل علي فاتورة تم بعدها عمل حركات علي المخزن الرئيسي', false);
            return back()->withInput()->with($notification);
        }

        if ($inpermit->outpermits && $inpermit->outpermits->count() > 0) {
            $notification = notification('لا يمكن تعديل فاتورة تم عمل مرتجع عليها', false);
            return back()->withInput()->with($notification);
        }

        $inputs = $request->except('_token');
        if (!$request->inpermit_code) {
            do {
                $inputs['inpermit_code'] = tb_code();
            } while (Inpermit::where('inpermit_code', $inputs['inpermit_code'])->count() != 0);
        }


        $i = 0;

        $attachProducts = [];
        $quantities = $request->quantities;
        $runIDs = $request->runIDs;
        $create_dates = $request->create_dates;
        $expire_dates = $request->expire_dates;
        $Public_Prices = $request->Public_Prices;
        $Buy_Prices = $request->Buy_Prices;
        $flagReturn = false;
        $dateFlag = false;

        $store_stock_arr = [];
        foreach ($request->product_ids as $product_id) {
            if (is_numeric($product_id) && array_key_exists($i, $quantities)) {
                $product = Product::find($product_id);

                if ($product && is_numeric($quantities[$i])) {
                    $inp_pro = self::runIDcheck($product_id, $runIDs[$i], true);


                    $create_date = (!$inp_pro) ? $create_dates[$i] : $inp_pro->create_date;
                    $expire_date = (!$inp_pro) ? $expire_dates[$i] : $inp_pro->expire_date;

                    $create_date = Carbon::createFromFormat('Y-m-d', $create_date);
                    $expire_date = Carbon::createFromFormat('Y-m-d', $expire_date);

                    if ($create_date->gt($expire_date)) {
                        $dateFlag = true;
                    }

                    if (!$inp_pro) {
                        $attachProducts[$i] = [
                            'product_id' => $product_id,
                            'Quantity' => $quantities[$i],
                            'Buy_Price' => $Buy_Prices[$i],
                            'Public_Price' => $Public_Prices[$i],
                            'runID' => $runIDs[$i],
                            'create_date' => $create_date,
                            'expire_date' => $expire_dates[$i],
                        ];
                    } elseif ($inp_pro) {
                        $attachProducts[$i] = [
                            'product_id' => $product_id,
                            'Quantity' => $quantities[$i],
                            'Buy_Price' => $inp_pro->Buy_Price,
                            'Public_Price' => $product->Public_Price,
                            'runID' => $runIDs[$i],
                            'create_date' => $create_date,
                            'expire_date' => $expire_date,
                        ];
                    }
                    array_push($store_stock_arr, [
                        'product_id' => $attachProducts[$i]['product_id'],
                        'runID' => $attachProducts[$i]['runID'],
                        'q_in_store' => $attachProducts[$i]['Quantity'],
                        'store_q_net' => $attachProducts[$i]['Quantity'],
                    ]);
                } else $flagReturn = true;
            } else $flagReturn = true;
            $i++;

            if ($flagReturn) {
                $notification = notification('يجب إدخال الأصناف والكميات بشكل صحيح', false);
                return back()->withInput()->with($notification);
            }

            if ($dateFlag) {
                $notification = notification('تاريخ انتهاء الصلاحية يجب ان يكون اكبر من الانتاج!', false);
                return back()->withInput()->with($notification);
            }
        }

        $store_stock_arr_min = [];
        foreach ($inpermit->products as $p) {
            // return $p;
            array_push($store_stock_arr_min, [
                'product_id' => $p->pivot->product_id,
                'runID' => $p->pivot->runID,
                'q_in_store' => $p->pivot->Quantity,
                'store_q_net' => $p->pivot->Quantity,
            ]);
        }

        // return $store_stock_arr_min;


        DB::beginTransaction();
        $inpermit->products()->detach();
        $inpermit->products()->attach($attachProducts);
        $inpermit->update($inputs);

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr_min, '-');
        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr, '+');

        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('inpermits.show', $inpermit->id)->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inpermit  $inpermit
     * @return \Illuminate\Http\Response
     */
    public function destroy($inpermit_id)
    {
        $inpermit = Inpermit::findOrFail($inpermit_id);
        $transfers = Transfer::where('created_at', '>', $inpermit->created_at)->where(function ($q) {
            return $q->where('from_store_id', 1)->orWhere('to_store_id', 1);
        })->get();

        if ($transfers && $transfers->count() > 0) {
            $notification = notification('عذرا لا يمكن حذف فاتورة تم بعدها عمل حركات علي المخزن الرئيسي', false);
            return back()->withInput()->with($notification);
        }

        if ($inpermit->outpermits && $inpermit->outpermits->count() > 0) {
            $notification = notification('لا يمكن حذف فاتورة تم عمل مرتجع عليها', false);
            return back()->withInput()->with($notification);
        }

        $store_stock_arr_min = [];
        foreach ($inpermit->products as $p) {
            array_push($store_stock_arr_min, [
                'product_id' => $p->pivot->product_id,
                'runID' => $p->pivot->runID,
                'q_in_store' => $p->pivot->Quantity,
                'store_q_net' => $p->pivot->Quantity,
            ]);
        }

        DB::beginTransaction();
        $inpermit->products()->detach();
        $inpermit->delete();

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr_min, '-');
        DB::commit();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('inpermits.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $inpermit_ids = $request->inpermits;
        if ($inpermit_ids) {
            foreach ($inpermit_ids as $inpermit_id) {
                $inpermit = Inpermit::find($inpermit_id);
                if ($inpermit) {
                    if ($inpermit->outpermits && $inpermit->outpermits->count() > 0) {
                        $notification = notification('لا يمكن حذف فاتورة تم عمل مرتجع عليها', false);
                        return back()->withInput()->with($notification);
                    }
                    $transfers = Transfer::where('created_at', '>', $inpermit->created_at)->where(function ($q) {
                        return $q->where('from_store_id', 1)->orWhere('to_store_id', 1);
                    })->get();

                    if ($transfers && $transfers->count() > 0) {
                        $notification = notification('عذرا لا يمكن حذف فاتورة تم بعدها عمل حركات علي المخزن الرئيسي', false);
                        return back()->withInput()->with($notification);
                    }

                    $store_stock_arr_min = [];
                    foreach ($inpermit->products as $p) {
                        array_push($store_stock_arr_min, [
                            'product_id' => $p->pivot->product_id,
                            'runID' => $p->pivot->runID,
                            'q_in_store' => $p->pivot->Quantity,
                            'store_q_net' => $p->pivot->Quantity,
                        ]);
                    }

                    $inpermit->products()->detach();
                    $inpermit->delete();

                    $main_store = Store::find(1);
                    $main_store->stock_update($store_stock_arr_min, '-');
                    
                }
            }

            DB::commit();
            $notification = array(
                'message' => 'تم الحذف بنجاح',
                'alert-type' => 'success',
                'success' => 'تم الحذف بنجاح',
            );
        } else {
            DB::commit();
            $notification = array(
                'message' => 'حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
                'alert-type' => 'error',
                'error' => 'حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
            );
        }

        return redirect()->route('inpermits.index')->with($notification);
    }

    public function runIDcheck($pro_id, $runID, $call = false)
    {
        $q = "SELECT * FROM inpermit_product WHERE product_id=$pro_id AND runID='$runID' order by inpermit_id DESC LIMIT 1";
        
        // if($pro_id){
        //     $pro = Product::find($pro_id)->with('run_ids', function ($q)
        //     {
        //         return $q->select(['product_id', 'runID', 'Public_Price'])->orderBy('Public_Price', 'DESC')->groupBy('Public_Price');
    
        //     })->get();
        // }

        $pro_run = DB::select($q);
        if (empty($pro_run)) {
            return false;
        } else {
            if ($call)
                return $pro_run[0];
            else 
                return response()->json($pro_run[0]);
        }
    }

    public function getrunid($pro)
    {
        $inpermit_pros = InpermitProduct::where('product_id', $pro)->orderBy('Public_Price', 'DESC')->groupBy('runID')->get();
        if($inpermit_pros->count()>0){
            return response()->json($inpermit_pros);
        }else{
            return response()->json(false);
        }
    }
}
