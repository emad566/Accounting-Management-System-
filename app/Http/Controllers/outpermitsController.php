<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutpermitRequest;
use Illuminate\Http\Request;
use App\Models\Inpermit;
use App\Models\Outpermit;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use App\Models\ViewStockClosed;
use DB;
use Auth;

class outpermitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outpermits = Outpermit::all();
        return view('dashboard.outpermits.index', compact(['outpermits']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($inpermit_id)
    {
        $inpermit = Inpermit::findOrFail($inpermit_id);
        $suppliers = Supplier::where('is_active', 1)->orderBy('Sup_Name')->get();
        $products = Product::where('is_active', 1)->orderBy('Product_Name')->get();
        return view('dashboard.outpermits.create', compact(['inpermit', 'suppliers', 'products']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\OutpermitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutpermitRequest $request)
    {
        if (!array_filter($request->Quantity_outs)) {
            $notification = array(
                'message' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في الفاتورة',
                'alert-type' => 'error',
                'error' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في الفاتورة',
            );
            return back()->withInput()->with($notification);
        }
        $inpermit = Inpermit::findOrFail($request->inpermit_id);


        $inputs = $request->except('_token');
        $inputs['user_id'] = Auth::id();

        if (!$request->outpermit_code) {
            do {
                $outputs['outpermit_code'] = tb_code();
            } while (Outpermit::where('outpermit_code', $outputs['outpermit_code'])->count() != 0);
        }

        $i = 0;

        $attachProducts = [];
        $quantities = $request->Quantity_outs;
        $flagReturn = false;

        $store_stock_arr = [];
        $store_stok = ViewStockClosed::where(['store_id' => 1])->get()->toArray();
        foreach ($request->inpermit_product_ids as $product_id) {
            if (is_numeric($product_id)) {
                if (!empty($quantities) && (!array_key_exists($i, $quantities) || is_null($quantities[$i]))) {
                    $i++;
                    continue;
                }

                $pivotProduct = $inpermit->products()->wherePivot('id', $product_id)->first();
                

                $quantity = false;
                foreach ($store_stok as $s) {
                    if ($s['product_id'] == $pivotProduct->pivot->product_id && $s['runID'] == $pivotProduct->pivot->runID) {
                        $quantity = $s['store_q_net'];
                    }
                }

                if ($pivotProduct && is_numeric($quantities[$i]) && $pivotProduct->pivot->Quantity >= $quantities[$i] && $quantity >= $quantities[$i]) {

                    $attachProducts[$i] = [
                        'inpermit_product_id' => $product_id,
                        'Quantity_out' => $quantities[$i],
                    ];

                    array_push($store_stock_arr, [
                        'product_id' =>  $pivotProduct->pivot->product_id, 
                        'runID' =>  $pivotProduct->pivot->runID,
                        'q_in_store' =>  $quantities[$i],
                        'store_q_net' =>  $quantities[$i],
                    ]);
                } else $flagReturn = true;
            } else $flagReturn = true;

            if ($flagReturn) {
                $notification = array(
                    'message' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                    'alert-type' => 'error',
                    'error' => 'يجب إدخال الأصناف والكميات بشكل صحيح ' . $quantity . "<" . $quantities[$i],
                );

                return back()->withInput()->with($notification);
            }
            $i++;
        }
        if (empty($attachProducts)) {
            return $attachProducts;
            $notification = array(
                'message' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في الفاتورة',
                'alert-type' => 'error',
                'error' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في الفاتورة',
            );
            return back()->withInput()->with($notification);
        }


        DB::beginTransaction();
        $outpermit = Outpermit::create($inputs);

        $outpermit->InpermitProduct()->sync($attachProducts);

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr, '-');

        DB::commit();

        $notification = array(
            'message' => 'تم الأرتجاع بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الأرتجاع بنجاح',
        );
        return redirect()->route('outpermits.index')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inpermit  $outpermit
     * @return \Illuminate\Http\Response
     */
    public function show(Inpermit $outpermit)
    {
        $suppliers = Supplier::where('is_active', 1)->orderBy('Sup_Name')->get();
        $products = Product::where('is_active', 1)->orderBy('Product_Name')->get();
        return view('dashboard.outpermits.edit', compact(['outpermit', 'suppliers', 'products']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inpermit  $outpermit
     * @return \Illuminate\Http\Response
     */
    public function edit(Outpermit $outpermit)
    {
        $inpermit = $outpermit->inpermit;
        // return $outpermit->outproducts()->first()->product;
        $suppliers = Supplier::where('is_active', 1)->orderBy('Sup_Name')->get();
        $products = Product::where('is_active', 1)->orderBy('Product_Name')->get();

        return view('dashboard.outpermits.edit', compact(['outpermit', 'suppliers', 'products', 'inpermit']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\OutpermitRequest  $request
     * @param  \App\Models\Outpermit  $outpermit
     * @return \Illuminate\Http\Response
     */
    public function update(OutpermitRequest $request, Outpermit $outpermit)
    {
        return "تعديل مردور فاتورة المشتريات، قيد التحديث من المطور.";
        if (!array_filter($request->Quantity_outs)) {
            $notification = array(
                'message' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في الفاتورة',
                'alert-type' => 'error',
                'error' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في الفاتورة',
            );
            return back()->withInput()->with($notification);
        }
        $inpermit = Inpermit::findOrFail($request->inpermit_id);


        $inputs = $request->except('_token');
        $inputs['user_id'] = Auth::id();

        if (!$request->outpermit_code) {
            do {
                $outputs['outpermit_code'] = tb_code();
            } while (Outpermit::where('outpermit_code', $outputs['outpermit_code'])->count() != 0);
        }

        $i = 0;

        $attachProducts = [];
        $quantities = $request->Quantity_outs;
        $flagReturn = false;
        $store_stock_arr_min = [];
        foreach ($request->inpermit_product_ids as $product_id) {
            if (is_numeric($product_id)) {
                if (!empty($quantities) && (!array_key_exists($i, $quantities) || is_null($quantities[$i]))) {
                    $i++;
                    continue;
                }

                $pivotProduct = $inpermit->products()->wherePivot('id', $product_id)->first();
                if ($pivotProduct && is_numeric($quantities[$i]) && $pivotProduct->pivot->Quantity >= $quantities[$i]) {
                    $attachProducts[$i] = [
                        'inpermit_product_id' => $product_id,
                        'Quantity_out' => $quantities[$i],
                    ];

                    array_push($store_stock_arr_min, [
                        'product_id' =>  $pivotProduct->pivot->product_id,
                        'runID' =>  $pivotProduct->pivot->runID,
                        'q_in_store' =>  $quantities[$i],
                        'store_q_net' =>  $quantities[$i],
                    ]);
                } else $flagReturn = true;
            } else $flagReturn = true;

            if ($flagReturn) {
                $notification = array(
                    'message' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                    'alert-type' => 'error',
                    'error' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                );
                return back()->withInput()->with($notification);
            }
            $i++;
        }

        $store_stock_arr_add = [];
        foreach ($outpermit->InpermitProduct as $p) {
            array_push($store_stock_arr_add, [
                'product_id' => $p->product_id,
                'runID' => $p->runID,
                'q_in_store' => $p->pivot->Quantity_out,
                'store_q_net' => $p->pivot->Quantity_out,
            ]);
        }

        DB::beginTransaction();
        $outpermit->update($inputs);

        $outpermit->InpermitProduct()->sync($attachProducts);

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr_add, '+');

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr_min, '-');

        DB::commit();

        $notification = array(
            'message' => 'تم حفظ التعديلات بنجاح',
            'alert-type' => 'success',
            'message' => 'تم حفظ التعديلات بنجاح',
        );

        return redirect()->route('outpermits.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Outpermit  $outpermit
     * @return \Illuminate\Http\Response
     */
    public function destroy($outpermit_id)
    {
        $outpermit = Outpermit::findOrFail($outpermit_id);

        $store_stock_arr_add = [];
        foreach ($outpermit->InpermitProduct as $p) {
            array_push($store_stock_arr_add, [
                'product_id' => $p->product_id,
                'runID' => $p->runID,
                'q_in_store' => $p->pivot->Quantity_out,
                'store_q_net' => $p->pivot->Quantity_out,
            ]);
        }

        DB::beginTransaction();
        $outpermit->delete();

        $main_store = Store::find(1);
        $main_store->stock_update($store_stock_arr_add, '+');

        DB::commit();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('outpermits.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $outpermit_ids = $request->outpermits;
        if ($outpermit_ids) {
            foreach ($outpermit_ids as $outpermit_id) {
                $outpermit = Outpermit::find($outpermit_id);
                if ($outpermit) {
                    $store_stock_arr_add = [];
                    foreach ($outpermit->InpermitProduct as $p) {
                        array_push($store_stock_arr_add, [
                            'product_id' => $p->product_id,
                            'runID' => $p->runID,
                            'q_in_store' => $p->pivot->Quantity_out,
                            'store_q_net' => $p->pivot->Quantity_out,
                        ]);
                    }

                    DB::beginTransaction();
                    $outpermit->delete();

                    $main_store = Store::find(1);
                    $main_store->stock_update($store_stock_arr_add, '+');

                    DB::commit();
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

        return redirect()->route('outpermits.index')->with($notification);
    }

    public function find()
    {
        return view('dashboard.outpermits.find');
    }

    public function find_post(Request $request)
    {
        $inpermit = Inpermit::where('inpermit_code', $request->inpermit_code)->first();
        if ($inpermit) {
            return redirect()->route('outpermits.create', ['inpermit_id' => $inpermit->id]);
        } else {
            return "not Found";
        }
        return view('dashboard.outpermits.find');
    }
}
