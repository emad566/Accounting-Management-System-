<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use App\Http\Requests\VoucherRequest;
use App\Models\Generalpolicy;
use App\Models\InpermitProduct;
use App\Models\Invoice;
use App\Models\Notif;
use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherOrdered;
use App\Models\Store;
use App\Models\User;
use App\Models\ViewStock;
use App\Models\ViewStockClosed;
use App\Models\VoucherStatus;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class vouchersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $time = microtime(true);
        $vouchers = null;
        if (Auth::user()->can(['Shaw all Vouchers'])) {
            $vouchers = VoucherOrdered::orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        } elseif (Auth::user()->can(['Delegate'])) {
            $vouchers = Auth::user()->vouchers_ordered;
        } elseif (Auth::user()->can(['Keeper'])) {
            $vouchers = VoucherOrdered::where('voucher_status', '>', 1)->whereIn('store_id', Auth::user()->stores->pluck('id'))->orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        }

        $voucher_status = VoucherStatus::orderBy('order', 'ASC')->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.vouchers.index', compact(['vouchers', 'voucher_status']));
    }

    public function yajravouchers()
    {
        $time = microtime(true);
        $vouchers = null;
        if (Auth::user()->can(['Shaw all Vouchers'])) {
            $vouchers = VoucherOrdered::where('voucher_status', '<>', 4)->orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        } elseif (Auth::user()->can(['Delegate'])) {
            $vouchers = Auth::user()->vouchers_ordered->where('voucher_status', '<>', 4);
        } elseif (Auth::user()->can(['Keeper'])) {
            $vouchers = VoucherOrdered::where('voucher_status', '<>', 4)->where('voucher_status', '>', 1)->whereIn('store_id', Auth::user()->stores->pluck('id'))->orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($vouchers)
        ->addColumn(
            'actions',
            function ($voucher) {
                $html = '<span class="actionLinks">';
                $html .= indexView($voucher, 'vouchers');
                $html .= '</span>';
                return new HtmlString($html);
            }
        )
        ->make(true);
    }

    public function indexok()
    {
        $time = microtime(true);
        $vouchers = null;
        if (Auth::user()->can(['Shaw all Vouchers'])) {
            $vouchers = VoucherOrdered::orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        } elseif (Auth::user()->can(['Delegate'])) {
            $vouchers = Auth::user()->vouchers_ordered;
        } elseif (Auth::user()->can(['Keeper'])) {
            $vouchers = VoucherOrdered::where('voucher_status', '>', 1)->whereIn('store_id', Auth::user()->stores->pluck('id'))->orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        }

        $voucher_status = VoucherStatus::orderBy('order', 'ASC')->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.vouchers.indexok', compact(['vouchers', 'voucher_status']));
    }

    public function yajravouchersok()
    {
        $time = microtime(true);
        $vouchers = null;
        if (Auth::user()->can(['Shaw all Vouchers'])) {
            $vouchers = VoucherOrdered::where('voucher_status', '>', 3)->orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        } elseif (Auth::user()->can(['Delegate'])) {
            $vouchers = Auth::user()->vouchers_ordered->where('voucher_status', '<>', 4);
        } elseif (Auth::user()->can(['Keeper'])) {
            $vouchers = VoucherOrdered::where('voucher_status', '>', 3)->where('voucher_status', '>', 1)->whereIn('store_id', Auth::user()->stores->pluck('id'))->orderBy('id', 'DESC')->orderBy('order', 'ASC')->get();
        }
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        

        return DataTables::of($vouchers)
        ->addColumn(
            'actions',
            function ($voucher) {
                $html = '<span class="actionLinks">';
                $html .= indexView($voucher, 'vouchers');
                $html .= '</span>';
                return new HtmlString($html); 
            }
        )
        ->make(true);

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $time = microtime(true);
        if (Auth::user()->voucher_id || Auth::user()->vouchers->whereIn('voucher_status', [1, 2, 3, 6])->count() > 0) {
            $notification = array(
                'message' => 'عذرا، لديك إذن صرف مفتوح بالفعل، يجب عليك تسوية أذن الصرف القديم حتي تتمكن من فتح إذن جديد.!',
                'alert-type' => 'error',
                'error' => 'عذرا، لديك إذن صرف مفتوح بالفعل، يجب عليك تسوية أذن الصرف القديم حتي تتمكن من فتح إذن جديد.!',
            );
            return redirect()->route('vouchers.index')->with($notification);
        }

        $stores = Auth::user()->stores->where('is_active', 1)->where('id', '<>', 1)->sortBy('Store_Name');
        $products = null;
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.vouchers.create', compact(['products', 'stores']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\VoucherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->voucherall){
            $store = Store::find($request->store_id);
            $request->merge(['product_ids'=> $store->storestocks->where('store_q_net', '>', 0)->pluck('product_id')->toArray()]);
            $request->merge(['runIDs'=> $store->storestocks->where('store_q_net', '>', 0)->pluck('runID')->toArray()]);
            $request->merge(['voucher_quantity_outs'=> $store->storestocks->where('store_q_net', '>', 0)->pluck('store_q_net')->toArray()]);
        }

        $this->validate($request, [
            'voucher_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'voucher_code' => 'nullable|unique:vouchers,voucher_code',
            'store_id' => 'numeric|required',
            'voucher_quantity_outs' => 'required|array',
            'voucher_quantity_outs.*' => 'numeric',
        ]);
        
        $time = microtime(true);
        if (Auth::user()->voucher_id || Auth::user()->vouchers->whereIn('voucher_status', [1, 2, 3, 6])->count() > 0) {
            $notification = array(
                'message' => 'عذرا، لديك اذن صرف مفتوح حاليا يجب عليك تسوية إذن الصرف أولا حتي تتمكن من إنشاء إذن صرف جديد.!',
                'alert-type' => 'error',
                'error' => 'عذرا، لديك اذن صرف مفتوح حاليا يجب عليك تسوية إذن الصرف أولا حتي تتمكن من إنشاء إذن صرف جديد.!',
            );
            return redirect()->route('vouchers.index')->with($notification);
        }

        if (!Auth::user()->stores->where('id', $request->store_id)->where('is_active', 1)->first()) {
            $notification = array(
                'message' => 'ليس لديك صلاحيات علي هذا المخزن!',
                'alert-type' => 'error',
                'error' => 'ليس لديك صلاحيات علي هذا المخزن!',
            );
            return back()->withInput()->with($notification);
        }

        if (!$request->voucher_quantity_outs || !array_filter($request->voucher_quantity_outs)) {
            $notification = array(
                'message' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في أمر التحويل',
                'alert-type' => 'error',
                'error' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في أمر التحويل',
            );
            return back()->withInput()->with($notification);
        }


        $inputs = $request->except('_token');
        $inputs['user_rep_id'] = Auth::id();
        if (!$request->voucher_code) {
            do {
                $inputs['voucher_code'] = tb_code();
            } while (Voucher::where('voucher_code', $inputs['voucher_code'])->count() != 0);
        }

        $i = 0;

        $attachProducts = [];
        $quantities = $request->voucher_quantity_outs;

        $runIDs = $request->runIDs;
        $flagReturn = false;
        $store_stok = ViewStockClosed::where(['store_id' => $request->store_id])->get()->toArray();

        $store_stock_add = [];
        foreach ($request->product_ids as $product_id) {
            if (is_numeric($product_id) && array_key_exists($i, $quantities) && array_key_exists($i, $runIDs)) {

                $quantity = false;
                foreach ($store_stok as $s) {
                    if ($s['product_id'] === $product_id && $s['runID'] === $runIDs[$i]) {
                        $quantity = $s['store_q_net'];
                    }
                }


                if (is_numeric($quantities[$i]) && $quantity && $quantity >= $quantities[$i]) {
                    $attachProducts[$i] = [
                        'product_id' => $product_id,
                        'runID' => $runIDs[$i],
                        'voucher_quantity' => $quantities[$i],
                    ];

                    array_push($store_stock_add, [
                        'product_id' => $product_id,
                        'runID' => $runIDs[$i],
                        'store_q_net' => -intval($quantities[$i]),
                        'q_reversed' => +intval($quantities[$i]),
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

        $users_notif = User::wherehas('stores', function ($q) use ($request) {
            return $q->where('store_id', $request->store_id);
        })->where('id', '<>', Auth::id())->get();


        DB::beginTransaction();

        $generalpolicy = Generalpolicy::find(1);
        $inputs['voucher_status'] = 1;

        if ($generalpolicy->auto_accept_permission_name && Auth::user()->can([$generalpolicy->auto_accept_permission_name])) {
            $inputs['voucher_status'] = 2;
        }


        $voucher = Voucher::create($inputs);
        $voucher->products()->sync($attachProducts);
        Auth::user()->update(['voucher_id' => $voucher->id]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'createVoucher',
            'table_name' => 'vouchers',
            'noteType' => 'Voucher',
            'notifiable_type' => 'App\\Voucher',
            'notifiable_id' => $voucher->id,
        ]);

        $notif->users()->sync($users_notif);

        $stock_store = Store::find($request->store_id);
        $stock_store->stock_update($store_stock_add, '+');

        DB::commit();

        event(new TransferEvt($users_notif, $notif, $notif->notif_html()));

        $t = microtime(true) - $time;
        $t = number_format((float)$t, 2, '.', '');
        $notification = array(
            'message' => 'تم الإضافة بنجاح Time: ' . $t,
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح Time: ' . $t,
        );
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('vouchers.show', $voucher->id)->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inpermit  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher, $load=false)
    {
        $time = microtime(true);
        $products = $voucher->products_q_net;
        if($load=='invoice'){
            return view('dashboard.vouchers.voucherInvoices', compact(['voucher', 'products']));
        }
        if($load == 'return'){
            return view('dashboard.vouchers.voucherReturns', compact(['voucher', 'products']));
        }
        
        $canRequire = vouchersController::canRequire($voucher);
        // $stores = Store::has('products')->where('is_active', 1)->orderBy('Store_Name')->where('id', '<>', 1)->get();
        $t1 = microtime(true) - $time;
        // return $t1;

        // return view('dashboard.vouchers.show', compact(['canRequire', 'voucher', 'products', 'stores']));
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        
        $invoice_ids = $voucher->invoices->pluck('id');
        return view('dashboard.vouchers.show', compact(['t1', 'canRequire', 'voucher', 'products', 'invoice_ids']));
    }
    
    public function loadvoucherdata(Voucher $voucher)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inpermit  $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
        $time = microtime(true);
        return "قيد التحديث  إضغط <a href='" . route('vouchers.show', $voucher->id) . "'>هنا للرجوع لصفحة الأذن</a>";

        return "Edit are disabled";
        $stores = Store::has('products')->where('is_active', 1)->orderBy('Store_Name')->where('id', '<>', 1)->get();
        $products = $voucher->products;
        // return $products;
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.vouchers.edit', compact(['voucher', 'products', 'stores']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\VoucherRequest  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $time = microtime(true);
        return "update";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        $time = microtime(true);
        if (!in_array($voucher->voucher_status, [1, 2, 100])) {
            $notification = notification('لم يتم الحذف: لا يمكن حذف اذن بعد خروجة من المخزن ', false);
            return back()->withInput()->with($notification);
        }

        if (!Auth::user()->can(['delete_accepted_vouchers'])) {
            if (!Auth::id() == $voucher->user->id) {
                $notification = notification('عذرا، لا يمكن حذف إذن صرف بعد الموافقة علية، كذلك يجب ان تكون صاحب الأذن حتي تتمكن من حذفة', false);
                return back()->withInput()->with($notification);
            }
        }

        if ($voucher->invoices->count() > 0) {
            $notification = notification('عذرا لا يمكن حذف إذن صرف مرتبط بفواتير');
            return redirect()->route('vouchers.index')->with($notification);
        }

        $voucher_id = $voucher->id;
        if (Auth::user()->voucher_id == $voucher->id) {
            Auth::user()->update(['voucher_id', 'null']);
        }

        DB::beginTransaction();
        $store_stock_add = [];

        switch ($voucher->voucher_status) {
            case 1:
            case 2:
                foreach ($voucher->products as $p) {
                    array_push($store_stock_add, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->runID,
                        'store_q_net' => +$p->pivot->voucher_quantity,
                        'q_reversed' => -$p->pivot->voucher_quantity,
                    ]);
                }
                break;
        }

        $stock_store = Store::find($voucher->store_id);
        $stock_store->stock_update($store_stock_add, '+');

        $voucher->delete();
        DB::commit();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('vouchers.index')->with($notification);
    }

    public function delete(Request $request)
    {
        $time = microtime(true);
        DB::beginTransaction();
        $voucher_ids = $request->vouchers;
        if ($voucher_ids) {
            foreach ($voucher_ids as $voucher_id) {
                $voucher = Voucher::find($voucher_id);
                if ($voucher) {
                    if (!(in_array($voucher->voucher_status, [1, 2, 100]) && Auth::id() == $voucher->user->id)) {
                        $notification = notification('عذرا، لا يمكن حذف إذن صرف بعد الموافقة علية، كذلك يجب ان تكون صاحب الأذن حتي تتمكن من حذفة', false);
                        return back()->withInput()->with($notification);
                    }

                    $store_stock_add = [];

                    switch ($voucher->voucher_status) {
                        case 1:
                        case 2:
                            foreach ($voucher->products as $p) {
                                array_push($store_stock_add, [
                                    'product_id' => $p->pivot->product_id,
                                    'runID' => $p->pivot->runID,
                                    'store_q_net' => +$p->pivot->voucher_quantity,
                                    'q_reversed' => -$p->pivot->voucher_quantity,
                                ]);
                            }
                            break;
                    }

                    $stock_store = Store::find($voucher->store_id);
                    $stock_store->stock_update($store_stock_add, '+');
                    
                    $voucher->delete();
                }
            }

            DB::commit();
            $notification = array(
                'message' => 'تم الحذف بنجاح',
                'alert-type' => 'success',
                'success' => 'تم الحذف بنجاح',
            );
        } else {
            $notification = array(
                'message' => 'حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
                'alert-type' => 'error',
                'error' => 'حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
            );
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('vouchers.index')->with($notification);
    }

    public function fromto($store_id)
    {
        $time = microtime(true);
        // $products = ViewStockClosed::groupby('product_id')->distinct()->where(['store_id'=>$store_id])->where('store_q_net', '>', 0)->join('products', 'products.Product_code', '=', 'view_stock_closed.product_id')->orderBy('Product_code')->get();
        $start = microtime(true);
        $stock = ViewStockClosed::where(['store_id' => $store_id])->where('store_q_net', '>', 0)->orderBy('Product_Name', 'asc')->get();
        // $stock_obj = ViewStockClosed::where(['store_id'=>$store_id])->where('store_q_net', '>', 0);
        // $t1 = microtime(true) - $start;
        // $stock = $stock_obj->orderBy('Product_Name', 'asc')->get();
        // $t2 = microtime(true) - $start;
        // $products = $stock_obj->groupby('product_id')->distinct()->orderBy('Product_Name', 'asc')->get();
        // $products = Product::all();
        // $html = "";
        // if($products){
        //     $html= select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>3]);
        // }
        // return $html;

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return response()->json([
            // 'html'=>$html,
            // 'stock'=>$stock,
            'stock' => $stock,
            // 't2'=>$t2,
            't1' => microtime(true) - $start,
        ]);
    }

    public function runIDQuantity($product_id, $runID, $store_id, $voucher = "")
    {
        $time = microtime(true);
        if ($voucher) {
            DB::beginTransaction();
            $voucher_id = $voucher;
            $voucher = Voucher::find($voucher_id);
            if ($voucher) {
                $voucher->delete();
            }
        }

        $pro = ViewStockClosed::where(['product_id' => $product_id, 'runID' => $runID, 'store_id' => $store_id])->first();
        if ($voucher) {
            DB::rollback();
        }
        execution_time_php($time, __class__ . '@' .__FUNCTION__);


        if (!$pro) {
            return false;
        } else {
            return $pro->store_q_net;
        }
    }

    public function getrunids($store_id, $product_id, $voucher = "")
    {
        $time = microtime(true);
        if ($voucher) {
            DB::beginTransaction();
            $voucher = Voucher::find($voucher_id);
            if ($voucher) {
                $voucher->delete();
            }
        }

        $runIDs = ViewStockClosed::where(['product_id' => $product_id, 'store_id' => $store_id])->where('store_q_net', '>', 0)->pluck('runID');

        $runIDs = (array) $runIDs;
        $runIDs =  reset($runIDs);
        execution_time_php($time, __class__ . '@' .__FUNCTION__);

        if (count($runIDs) == 1) {
            $select_runIDs = select(['errors' => '', 'name' => 'runID', 'frkName' => 'runID', 'rows' => $runIDs, 'transAttr' => true, 'notrans' => true, 'select_id' => $runIDs[0], 'label' => true, 'cols' => 1]);
        } else {
            $select_runIDs = select(['errors' => '', 'name' => 'runID', 'frkName' => 'runID', 'rows' => $runIDs, 'transAttr' => true, 'notrans' => true, 'label' => true, 'cols' => 1]);
        }
        return $select_runIDs;
    }

    public function accept(Voucher $voucher)
    {
        $voucher_id = $voucher->id;
        $time = microtime(true);

        if (!$voucher->voucher_status == 1) {
            $notification = notification('لا يمكن الموافقة علي اذن صرف الا اذا كان بانتظار الموافقة للمحاسب', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        if ($voucher->id != $voucher->rep->voucher_id) {
            $notification = notification('عذرا هذا الاذن غير مرتبط بحساب المندوب علي انه مفتوح !! أو ان المندوب لديه أكثر من إذن صرف مفتوح', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        DB::beginTransaction();
        $voucher->update(['voucher_status' => 2, 'user_accountant_id' => Auth::id()]);
        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'voucher_status_2',
            'table_name' => 'vouchers',
            'noteType' => 'Voucher',
            'notifiable_type' => 'App\\Voucher',
            'notifiable_id' => $voucher->id,
        ]);

        $notif->users()->sync($voucher->user);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تمت الموافقة بنجاح',
            'alert-type' => 'success',
            'success' => 'تمت الموافقة بنجاح',
        );
        execution_time_php($time, __class__ . '@' .__FUNCTION__);

        

        // Load Voucher again
        $voucher = Voucher::find($voucher_id);
        $products = $voucher->products_q_net;
        $canRequire = vouchersController::canRequire($voucher);
        return view('dashboard.vouchers.voucherChangeStatus', compact(['notification', 'voucher', 'products', 'canRequire']));
        
        //return redirect()->route('vouchers.voucherChangeStatus', $voucher->id)->with($notification);
        // return redirect()->route('vouchers.show', $voucher->id)->with($notification);
    }

    public function refuse(Voucher $voucher)
    {
        $voucher_id = $voucher->id;
        $time = microtime(true);

        if ($voucher->voucher_status > 2) {
            $notification = notification('لا يمكن رفض اذن بعد خروجة من المخزن!', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        DB::beginTransaction();
        $voucher->user->update(['voucher_id' => NULL]);
        $voucher->update(['voucher_status' => 100, 'user_accountant_id' => Auth::id()]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'voucher_status_100',
            'table_name' => 'vouchers',
            'noteType' => 'Voucher',
            'notifiable_type' => 'App\\Voucher',
            'notifiable_id' => $voucher->id,
        ]);

        $notif->users()->sync($voucher->user);

        $store_stock_add = [];

        switch ($voucher->voucher_status) {
            case 1:
            case 2:
                foreach ($voucher->products as $p) {
                    array_push($store_stock_add, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->runID,
                        'store_q_net' => +$p->pivot->voucher_quantity,
                        'q_reversed' => -$p->pivot->voucher_quantity,
                    ]);
                }
                break;
        }

        $stock_store = Store::find($voucher->store_id);
        $stock_store->stock_update($store_stock_add, '+');
        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));
        $notification = array(
            'message' => 'تم الرفض بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الرفض بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);

        // Load Voucher again
        $voucher = Voucher::find($voucher_id);
        $products = $voucher->products_q_net;
        $canRequire = vouchersController::canRequire($voucher);
        return view('dashboard.vouchers.voucherChangeStatus', compact(['notification', 'voucher', 'products', 'canRequire']));
    }

    public function accountantreturn(Voucher $voucher)
    {
        $voucher_id = $voucher->id;
        $time = microtime(true);
        if((Auth::user()->can('Review_Accountant_his_Vouchers') && Auth::user()->voucher_id == $voucher->id) 
            || Auth::user()->can('Review Accountant Vouchers')){

                if ($voucher->voucher_status != 6) {
                    $notification = notification('لم يتم: لا يمكن طلب تسوية لاذن الا بعد طلب التسوية', false);
                    return redirect()->route('vouchers.show', $voucher->id)->with($notification);
                }
        
                if ($voucher->id != $voucher->rep->voucher_id) {
                    $notification = notification('عذرا هذا الاذن غير مرتبط بحساب المندوب علي انه مفتوح !! أو ان المندوب لديه أكثر من إذن صرف مفتوح', false);
                    return redirect()->route('vouchers.show', $voucher->id)->with($notification);
                }
        
                DB::beginTransaction();
                $voucher->update(['user_accountant_return_id' => Auth::user()->id]);
                if ($voucher->user_keeper_return_id) {
                    $voucher->update(['voucher_status' => 4]);
                    $voucher->user->update(['voucher_id' => null]);
        
                    $store_stock_add = [];
                    $voucher = Voucher::find($voucher->id);
                    foreach ($voucher->products_q_net as $p) {
                        array_push($store_stock_add, [
                            'product_id' => $p->product_id,
                            'runID' => $p->runID,
                            'q_in_store' => $p->net_q,
                            'store_q_net' => $p->net_q,
                        ]);
                    }
                    $stock_store = Store::find($voucher->store_id);
                    $stock_store->stock_update($store_stock_add, '+');
                }
        
                $notif = Notif::create([
                    'user_create_id' => Auth::id(),
                    'notefun' => 'voucher_status_accountant',
                    'table_name' => 'vouchers',
                    'noteType' => 'Voucher',
                    'notifiable_type' => 'App\\Voucher',
                    'notifiable_id' => $voucher->id,
                ]);
        
                $notif->users()->sync($voucher->user);
        
                DB::commit();
                event(new TransferEvt('', $notif, $notif->notif_html()));
        
                $notification = array(
                    'message' => 'تم التسوية مع المحاسب بنجاح',
                    'alert-type' => 'success',
                    'success' => 'تم التسوية مع المحاسب بنجاح',
                );
                
                execution_time_php($time, __class__ . '@' .__FUNCTION__);
                
                // Load Voucher again
                $products = $voucher->products_q_net;
                $canRequire = vouchersController::canRequire($voucher);
                return view('dashboard.vouchers.voucherChangeStatus', compact(['voucher', 'products', 'canRequire']));
        }else{
            $notification = notification('لم يتم:ليس لديك صلاحية', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }
    }

    public function keeperreturn(Voucher $voucher)
    {
        $voucher_id = $voucher->id;
        $time = microtime(true);
        if ($voucher->voucher_status != 6) {
            $notification = notification('لم يتم: لا يمكن طلب تسوية لاذن الا بعد طلب التسوية', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        if ($voucher->id != $voucher->rep->voucher_id) {
            $notification = notification('عذرا هذا الاذن غير مرتبط بحساب المندوب علي انه مفتوح !! أو ان المندوب لديه أكثر من إذن صرف مفتوح', false);
            return redirect()->route('vouchers.index')->with($notification);
        }

        DB::beginTransaction();
        $voucher->update(['user_keeper_return_id' => Auth::user()->id]);
        if ($voucher->user_accountant_return_id) {
            $voucher->update(['voucher_status' => 4]);
            $voucher->user->update(['voucher_id' => null]);

            $store_stock_add = [];
            $voucher = Voucher::find($voucher->id);
            foreach ($voucher->products_q_net as $p) {
                array_push($store_stock_add, [
                    'product_id' => $p->product_id,
                    'runID' => $p->runID,
                    'q_in_store' => $p->net_q,
                    'store_q_net' => $p->net_q,
                ]);
            }
            $stock_store = Store::find($voucher->store_id);
            $stock_store->stock_update($store_stock_add, '+');
        }

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'voucher_status_keeper',
            'table_name' => 'vouchers',
            'noteType' => 'Voucher',
            'notifiable_type' => 'App\\Voucher',
            'notifiable_id' => $voucher->id,
        ]);

        $notif->users()->sync($voucher->user);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم التسوية مع أمين المخزن بنجاح',
            'alert-type' => 'success',
            'success' => 'تم التسوية مع أمين المخزن بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        
        //Load Voucher data
        $voucher = Voucher::find($voucher_id);
        $products = $voucher->products_q_net;
        $canRequire = vouchersController::canRequire($voucher);
        return view('dashboard.vouchers.voucherChangeStatus', compact(['notification', 'voucher', 'products', 'canRequire']));
    }

    public function keeperaccept(Voucher $voucher)
    {
        $voucher_id = $voucher->id;
        $voucher_id = $voucher->id;
        $time = microtime(true);
        if($voucher->voucher_status != 2){
            $notification = notification('لم يتم تغيير حالة الأذن، لا يمكن خروج اذن إلا اذا كان بحالة موافقة المحاسب!', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        if ($voucher->id != $voucher->rep->voucher_id) {
            $notification = notification('عذرا هذا الاذن غير مرتبط بحساب المندوب علي انه مفتوح !! أو ان المندوب لديه أكثر من إذن صرف مفتوح', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        $users_notif = User::wherehas('stores', function ($q) use ($voucher) {
            return $q->where('store_id', $voucher->store_id);
        })->where('id', '<>', Auth::id())->get();

        $store_stock_add = [];
        foreach ($voucher->products as $p) {
            array_push($store_stock_add, [
                'product_id' => $p->pivot->product_id,
                'runID' => $p->pivot->runID,
                'q_in_store' => -$p->pivot->voucher_quantity,
                'q_reversed' => -$p->pivot->voucher_quantity,
            ]);
        }
        $stock_store = Store::find($voucher->store_id);

        DB::beginTransaction();
        $stock_store->stock_update($store_stock_add, '+');

        if ($voucher->voucher_status == 2) {
            $voucher->update(['voucher_status' => 3, 'user_keeper_id' => Auth::id()]);
        }

        

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'voucher_status_3',
            'table_name' => 'vouchers',
            'noteType' => 'Voucher',
            'notifiable_type' => 'App\\Voucher',
            'notifiable_id' => $voucher->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم  خروج الاذن من المخزن بنجاح',
            'alert-type' => 'success',
            'success' => 'تم  خروج الاذن من المخزن بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);

        //Load Voucher data
        $voucher = Voucher::find($voucher_id);
        $products = $voucher->products_q_net;
        $canRequire = vouchersController::canRequire($voucher);
        return view('dashboard.vouchers.voucherChangeStatus', compact(['notification', 'voucher', 'products', 'canRequire']));
    }

    public function settlement_request(Voucher $voucher)
    {
        $voucher_id = $voucher->id;
        $time = microtime(true);
        if ($voucher->voucher_status != 3) {
            $notification = notification('لم يتم، لا يمكن طلب تسوية إلا اذا كانت حالة الأذن خرج من المخزن!!', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        if ($voucher->id != $voucher->rep->voucher_id) {
            $notification = notification('عذرا هذا الاذن غير مرتبط بحساب المندوب علي انه مفتوح !! أو ان المندوب لديه أكثر من إذن صرف مفتوح', false);
            return redirect()->route('vouchers.index')->with($notification);
        }

        $users_notif = User::wherehas('stores', function ($q) use ($voucher) {
            return $q->where('store_id', $voucher->store_id);
        })->where('id', '<>', Auth::id())->get();

        $canRequire = vouchersController::canRequire($voucher);
        if (!$canRequire) {
            $notification = notification('لا يمكن طلب تسوية بدون الرد من المحاسب علي كل الفواتير.', false);
        }

        DB::beginTransaction();
        if ($voucher->voucher_status == 3 && $canRequire) {
            $voucher->update(['voucher_status' => 6, 'settlement_request_id' => Auth::user()->id]);
        }

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'voucher_status_6',
            'table_name' => 'vouchers',
            'noteType' => 'Voucher',
            'notifiable_type' => 'App\\Voucher',
            'notifiable_id' => $voucher->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم  طلب التسوية بنجاح',
            'alert-type' => 'success',
            'success' => 'تم  طلب التسوية بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);

        //Load Voucher data
        return 'تم  طلب التسوية بنجاح';
    }

    public function canRequire($voucher)
    {
        $time = microtime(true);
        $canRequireCount = $voucher->invoices->where('invoice_status_id', 20)->count();
        
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return ($canRequireCount == $voucher->invoices->count()) ? true : false;
    }

    public function openVoucher(Voucher $voucher)
    {
        $time = microtime(true);
        if ($voucher->voucher_status != 4) {
            $notification = notification('لم يتم التحديث: عذرا لا يمكن فتح إذن صرف إلا بعد تسويته!', false);
            return redirect()->route('vouchers.show', $voucher->id)->with($notification);
        }

        if ($voucher->rep->voucher_id) {
            $notification = notification('عذرا هذا المندوب لدية إذن صرف مفتوح بالفعل', false);
            return back()->withInput()->with($notification);
        }
        // return $voucher->rep;
        DB::beginTransaction();
        $voucher->rep->update(['voucher_id' => $voucher->id]);
        $voucher->update([
            'user_accountant_return_id' => NULL,
            'user_keeper_return_id' => NULL,
            'voucher_status' => 3,
            'voucher_close_date' => NULL,
            'settlement_request_id' => NULL
        ]);

        $store_stock_add = [];
        $voucher = Voucher::find($voucher->id);
        foreach ($voucher->products_q_net as $p) {
            array_push($store_stock_add, [
                'product_id' => $p->product_id,
                'runID' => $p->runID,
                'q_in_store' => -$p->net_q,
                'store_q_net' => -$p->net_q,
            ]);
        }

        $stock_store = Store::find($voucher->store_id);
        $stock_store->stock_update($store_stock_add, '+');

        DB::commit();

        $notification = notification('تم فتح اذن الصرف وربطه بالمندوب', true);

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        
        return redirect()->route('vouchers.show', $voucher->id)->with($notification);
    }

    public function invoice(Invoice $invoice)
    {
        // return $invoice;
        return view('dashboard.vouchers.showinvoice', compact(['invoice']));
    }

    public function quantities(Voucher $voucher)
    {
        
       $pros = $voucher->products_q_net;
        
        $pids = $pros->pluck('product_id')->toArray();
        $rids = $pros->pluck('runID')->toArray();
        $inps = InpermitProduct::whereIn('product_id', $pids)->whereIn('runID', $rids)->get();
        
        $prosArr = [];
        foreach($pros as $p){
            $p['Public_Price'] = $inps->where('product_id', $p->product_id)->where('runID', $p->runID)->first()->Public_Price;
            array_push($prosArr, $p);
        }

        return $prosArr;

    }
}
