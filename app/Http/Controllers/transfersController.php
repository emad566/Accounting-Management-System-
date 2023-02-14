<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use App\Http\Requests\TransferRequest;
use App\Models\InpermitProduct;
use Illuminate\Http\Request;
use App\Models\Notif;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\TransferStatus;
use App\Models\Store;
use App\Models\TransferProduct;
use App\Models\User;
use App\Models\ViewStockClosed;
use App\Models\ViewTransfer;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class transfersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $time = microtime(true);
        $transfers = Null;
        if (Auth::user()->can(['CRUD Transfers'])) {
            $transfers = Transfer::orderBy('transfer_status_id', 'ASC')->get();
        } else if (Auth::user()->can(['index_his_store_transfers'])) {
            $store_ids = Auth::user()->stores->pluck('id');
            $transfers = Transfer::whereIn('from_store_id', $store_ids)->orWhereIn('to_store_id', $store_ids)->orderBy('transfer_status_id', 'ASC')->get();
        }

        // return $transfers->pluck('transfer_status_id');

        $transfer_status = TransferStatus::all();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transfers.index', compact(['transfers', 'transfer_status']));
    }

    public function yajratransfers()
    {
        $time = microtime(true);
        $transfers = Null;
        if (Auth::user()->can(['CRUD Transfers'])) {
            $transfers = ViewTransfer::orderBy('transfer_status_id', 'ASC')->get();
        } else if (Auth::user()->can(['index_his_store_transfers'])) {
            $store_ids = Auth::user()->stores->pluck('id');
            $transfers = ViewTransfer::whereIn('from_store_id', $store_ids)->orWhereIn('to_store_id', $store_ids)->orderBy('transfer_status_id', 'ASC')->get();
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($transfers)
            ->addColumn(
                'actions',
                function ($transfer) {
                    $html = '<span class="actionLinks">';

                    if (Auth::user()->can(['CRUD Transfers']))
                        $html .= indexEdit($transfer, 'transfers');

                    $html .= indexView($transfer, 'transfers');

                    if (Auth::user()->can(['CRUD Transfers']))
                        $html .= indexDel(['del' => $transfer, 'table' => 'transfers', 'title' => 'transfer_code', 'indexDel' => true, 'vars' => false, 'transval' => 'التحويل برقم سند', 'nodel' => false]);

                    $html .= '</span>';
                    return new HtmlString($html);
                }
            )
            ->setRowData([
                'data-id' => function ($transfer) {
                    $html = '<input type="checkbox" name="transfer[]" value="' . $transfer->id . '" class="boxItem"> ';
                    return new HtmlString($html);
                }
            ])
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
        $stores_from = NULL;
        if (Auth::user()->stores) {
            // $store_ids = Auth::user()->stores->pluck('id');
            $stores_from = Auth::user()->stores;
            // $stores_from = Store::has('products')->where('is_active', 1)->whereIn('id', $store_ids)->orderBy('Store_Name')->get();
        }
        $stores_to = Store::where('is_active', 1)->orderBy('Store_Name')->get();
        $transfer_status = TransferStatus::all();
        $products = null;

        if (!$stores_from) {
            $notification = notification('لا يمكنك انشاء تحويل مخزني الأن: حيث لا تمتلك مخازن بها منتجات.!', false);
            return back()->withInput()->with($notification);
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transfers.create', compact(['transfer_status', 'products', 'stores_from', 'stores_to']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\TransferRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->voucherall){
            $store = Store::find($request->from_store_id);
            $request->merge(['product_ids'=> $store->storestocks->where('store_q_net', '>', 0)->pluck('product_id')->toArray()]);
            $request->merge(['runIDs'=> $store->storestocks->where('store_q_net', '>', 0)->pluck('runID')->toArray()]);
            $request->merge(['quantities'=> $store->storestocks->where('store_q_net', '>', 0)->pluck('store_q_net')->toArray()]);
        }

        
        $this->validate($request, [
            'transfer_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'transfer_code' => 'nullable|unique:transfers,transfer_code',
            'from_store_id' => 'numeric|required',
            'to_store_id' => 'numeric|required',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric',
        ]);

        // return $request->all();

        $time = microtime(true);
        $start = microtime(true);
        $store_ids = Auth::user()->stores->pluck('id');
        $store_ids = (array) $store_ids;
        
        foreach ($store_ids as $ids) {
            $store_ids = $ids;
            break;
        }

        if (!in_array($request->from_store_id, $store_ids)) {
            $notification = notification('لا يمكنك انشاء تحويل مخزني الأن: حيث لا تمتلك مخازن بها منتجات.!', false);
            return back()->withInput()->with($notification);
        }

        $users_notif = User::wherehas('stores', function ($q) use ($request) {
            return $q->whereIn('store_id', [$request->from_store_id, $request->to_store_id]);
        })->get();



        if ($request->from_store_id == $request->to_store_id) {
            $notification = array(
                'message' => 'خطأ: لا يمكن التحويل من والي نفس المخزن!!.',
                'alert-type' => 'error',
                'error' => 'خطأ: لا يمكن التحويل من والي نفس المخزن!!.',
            );
            return back()->withInput()->with($notification);
        }

        if (!array_filter($request->quantities)) {
            $notification = array(
                'message' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في أمر التحويل',
                'alert-type' => 'error',
                'error' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في أمر التحويل',
            );
            return back()->withInput()->with($notification);
        }


        $inputs = $request->except('_token');
        $inputs['user_id'] = Auth::id();

        if (!$request->transfer_code) {
            do {
                $inputs['transfer_code'] = tb_code();
            } while (Transfer::where('transfer_code', $inputs['transfer_code'])->count() != 0);
        }

        $i = 0;

        $attachProducts = [];
        $quantities = $request->quantities;
        $runIDs = $request->runIDs;
        $flagReturn = false;

        $store_stok = ViewStockClosed::where(['store_id' => $request->from_store_id])->get()->toArray();
        $store_stock_add = [];

        foreach ($request->product_ids as $product_id) {
            if (is_numeric($product_id) && array_key_exists($i, $quantities) && array_key_exists($i, $runIDs)) {

                $quantity = false;
                foreach ($store_stok as $s) {
                    if ($s['product_id'] == $product_id && $s['runID'] === $runIDs[$i]) {
                        $quantity = $s['store_q_net'];
                    }
                }

                if (is_numeric($quantities[$i]) && $quantity && $quantity >= $quantities[$i]) {
                    $attachProducts[$i] = [
                        'product_id' => $product_id,
                        'Quantity' => $quantities[$i],
                        'RunID' => $runIDs[$i],
                    ];



                    array_push($store_stock_add, [
                        'product_id' => $product_id,
                        'runID' => $runIDs[$i],
                        'store_q_net' => -intval($quantities[$i]),
                        'transfer_q_reserved' => +intval($quantities[$i]),
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

        DB::beginTransaction();
        $inputs['transfer_status_id'] = 10;
        $transfer = Transfer::create($inputs);
        $transfer->products()->sync($attachProducts);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'createTransfer',
            'table_name' => 'transfers',
            'noteType' => 'Transfer',
            'notifiable_type' => 'App\\Transfer',
            'notifiable_id' => $transfer->id,
        ]);

        $notif->users()->sync($users_notif);

        $main_store = Store::find($request->from_store_id);
        $main_store->stock_update($store_stock_add, '+');

        DB::commit();

        event(new TransferEvt($users_notif, $notif, $notif->notif_html()));

        $t = microtime(true) - $start;
        $t = number_format((float)$t, 2, '.', '');
        $notification = array(
            'message' => 'تم الإضافة بنجاح Time: ' . $t,
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح Time: ' . $t,
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transfers.show', $transfer->id)->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inpermit  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        $time = microtime(true);
        if (Auth::user()->can('Accountant') || Auth::user()->can('SupperAdmin')) {
            $store_ids =  (array) Store::all()->pluck('id');
        } else {
            $store_ids = (array) Auth::user()->stores->pluck('id');
        }

        $auth_store_ids = (array) Auth::user()->stores->pluck('id');

        // $i=0;
        foreach ($store_ids as $sid) {
           $store_ids = $sid;
           break;
        }
        foreach ($auth_store_ids as $sid) {
           $auth_store_ids = $sid;
           break;
        }
        // return $store_ids;
        $canchangestatusTo2030 = (in_array($transfer->from_store_id, $store_ids)) ? 1 : 0;
        $canchangestatusTo40 = (in_array($transfer->to_store_id, $auth_store_ids)) ? true : false;
        
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transfers.show', compact(['transfer', 'canchangestatusTo2030', 'canchangestatusTo40']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inpermit  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(Transfer $transfer)
    {
        $time = microtime(true);
        return "قيد التحديث  إضغط <a href='" . route('transfers.show', $transfer->id) . "'>هنا للرجوع لصفحة التحويل</a>";

        if ($transfer->transfer_status_id != 10) {
            $notification = notification('عذرا، لا يمكن تعديل أمر تحويل بعد شحنة', false);
            return back()->withInput()->with($notification);
        }
        $stores_from = NULL;
        if (Auth::user()->stores) {
            $store_ids = Auth::user()->stores->pluck('id');
            $stores_from = Store::has('products')->where('is_active', 1)->whereIn('id', $store_ids)->orderBy('Store_Name')->get();
        }

        $stores_to = Store::where('is_active', 1)->orderBy('Store_Name')->get();
        $transfer_status = TransferStatus::all();
        $products = null;

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transfers.edit', compact(['transfer', 'transfer_status', 'products', 'stores_from', 'stores_to']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\TransferRequest  $request
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(TransferRequest $request, Transfer $transfer)
    {
        $time = microtime(true);
        $store_ids = Auth::user()->stores->pluck('id');
        $store_ids = (array) $store_ids;
        foreach ($store_ids as $ids) {
            $store_ids = $ids;
            break;
        }


        if (!in_array($request->from_store_id, $store_ids)) {
            $notification = notification('لا يمكنك انشاء تحويل مخزني الأن: حيث لا تمتلك مخازن بها منتجات.!', false);
            return back()->withInput()->with($notification);
        }

        if ($transfer->transfer_status_id != 10) {
            $notification = notification('عذرا، لا يمكن تعديل أمر تحويل بعد شحنة', false);
            return back()->withInput()->with($notification);
        }
        if ($request->from_store_id == $request->to_store_id) {
            $notification = array(
                'message' => 'خطأ: لا يمكن التحويل من والي نفس المخزن!!.',
                'alert-type' => 'error',
                'error' => 'خطأ: لا يمكن التحويل من والي نفس المخزن!!.',
            );
            return back()->withInput()->with($notification);
        }
        if (!array_filter($request->quantities)) {
            $notification = array(
                'message' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في أمر التحويل',
                'alert-type' => 'error',
                'error' => 'يجب ان تدخل كمية عنصر واحد علي الأقل في أمر التحويل',
            );
            return back()->withInput()->with($notification);
        }


        $inputs = $request->except('_token');
        $inputs['user_id'] = Auth::id();

        if (!$request->transfer_code) {
            do {
                $inputs['transfer_code'] = tb_code();
            } while (Transfer::where('transfer_code', $inputs['transfer_code'])->count() != 0);
        }

        $i = 0;

        $attachProducts = [];
        $quantities = $request->quantities;
        $runIDs = $request->runIDs;
        $flagReturn = false;
        foreach ($request->product_ids as $product_id) {
            if (is_numeric($product_id) && array_key_exists($i, $quantities) && array_key_exists($i, $runIDs)) {

                $quantity = transfersController::runIDQuantity($product_id, $runIDs[$i], $request->from_store_id, $transfer->id);

                if (is_numeric($quantities[$i]) && $quantity && $quantity >= $quantities[$i]) {
                    $attachProducts[$i] = [
                        'product_id' => $product_id,
                        'Quantity' => $quantities[$i],
                        'RunID' => $runIDs[$i],
                    ];
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

        $transfer = Transfer::find($transfer->id);

        $users_notif = User::wherehas('stores', function ($q) use ($request) {
            return $q->whereIn('store_id', [$request->from_store_id, $request->to_store_id]);
        })->get();

        // return $attachProducts;

        DB::beginTransaction();
        $inputs['transfer_status_id'] = 10;
        $transfer->update($inputs);
        $transfer->products()->detach();
        $transfer->products()->attach($attachProducts);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'updateTransfer',
            'table_name' => 'transfers',
            'noteType' => 'Transfer',
            'notifiable_type' => 'App\\Transfer',
            'notifiable_id' => $transfer->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt($users_notif, $notif, $notif->notif_html()));

        $notification = notification('تم التعديل بنجاح', true);
        
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transfers.show', $transfer->id)->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy($transfer_id)
    {
        $time = microtime(true);
        $transfer = Transfer::findOrFail($transfer_id);
        if ($transfer->status->id == 20 || $transfer->status->id == 40) {
            $notification = notification('لا يمكن حذف تحويل تم تسليمة أو قيد الشحن', false);
            return back()->withInput()->with($notification);
        }

        DB::beginTransaction();
        if ($transfer->status->id == 10) {

            $store_stock_arr_from = [];
            foreach ($transfer->products as $p) {
                array_push($store_stock_arr_from, [
                    'product_id' => $p->pivot->product_id,
                    'runID' => $p->pivot->RunID,
                    'store_q_net' => $p->pivot->Quantity,
                    'transfer_q_reserved' => -$p->pivot->Quantity,
                ]);
            }

            $from_store = Store::find($transfer->from_store_id);
            $from_store->stock_update($store_stock_arr_from, '+');
        }
        $transfer->delete();
        DB::commit();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transfers.index')->with($notification);
    }

    public function delete(Request $request)
    {
        $time = microtime(true);

        DB::beginTransaction();
        $transfer_ids = $request->transfers;
        $del_error_flag = false;
        if ($transfer_ids) {
            foreach ($transfer_ids as $transfer_id) {
                $transfer = Transfer::find($transfer_id);
                if ($transfer) {
                    if ($transfer->status->id == 20 || $transfer->status->id == 40) {
                        $del_error_flag = true;
                    } else {
                        if ($transfer->status->id == 10) {
                            $store_stock_arr_from = [];
                            foreach ($transfer->products as $p) {
                                array_push($store_stock_arr_from, [
                                    'product_id' => $p->pivot->product_id,
                                    'runID' => $p->pivot->RunID,
                                    'store_q_net' => $p->pivot->Quantity,
                                    'transfer_q_reserved' => -$p->pivot->Quantity,
                                ]);
                            }

                            $from_store = Store::find($transfer->from_store_id);
                            $from_store->stock_update($store_stock_arr_from, '+');
                        }
                        $transfer->delete();
                    }
                }
            }

            DB::commit();
            if ($del_error_flag) {
                $notification = notification('لا يمكن حذف تحويل تم تسليمة أو قيد الشحن', false);
                return back()->withInput()->with($notification);
            }
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

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transfers.index')->with($notification);
    }

    public function fromto($store_id, $transfer = "")
    {
        $time = microtime(true);
        $start = microtime(true);
        if ($transfer) {
            DB::beginTransaction();
            $transfer = Transfer::findOrFail($transfer);
            $transfer->delete();
        }

        $stock = ViewStockClosed::where(['store_id' => $store_id])->where('store_q_net', '>', 0)->orderBy('Product_Name', 'asc')->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return response()->json([
            'stock' => $stock,
            't1' => microtime(true) - $start,
        ]);

        $products = ViewStockClosed::groupby('product_id')->distinct()->where(['store_id' => $store_id])->where('store_q_net', '>', 0)->orderBy('Product_Name', 'ASC')->get();
        $html = "";
        if ($products) {
            $html = select(['errors' => '', 'name' => 'product_id', 'frkName' => 'Product_Name', 'rows' => $products, 'transAttr' => true, 'label' => true, 'cols' => 3]);
        }
        return $html;
    }

    public function runIDQuantity($product_id, $runID, $store_id, $transfer = "")
    {
        $time = microtime(true);
        if ($transfer) {
            DB::beginTransaction();
            $transfer_id = $transfer;
            $transfer = Transfer::find($transfer_id);
            if ($transfer) {
                $transfer->delete();
            }
        }

        $pro = ViewStockClosed::where(['product_id' => $product_id, 'runID' => $runID, 'store_id' => $store_id])->first();
        if ($transfer) {
            DB::rollback();
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        if (!$pro) {
            return false;
        } else {
            return $pro->store_q_net;
        }
    }

    public function changestatus(Transfer $transfer, $status_id)
    {
        $time = microtime(true);
        $store_stock_arr_from = [];
        $store_stock_arr_to = [];
        if ($transfer->status_id != $status_id) {
            DB::beginTransaction();

            if (Auth::user()->can('Accountant') || Auth::user()->can('SupperAdmin')) {
                $store_ids =  (array) Store::all()->pluck('id');
            } else {
                $store_ids = (array) Auth::user()->stores->pluck('id');
            }

            $auth_store_ids = (array) Auth::user()->stores->pluck('id');

            foreach ($store_ids as $ids) {
                $store_ids = $ids;
                break;
            }

            foreach ($auth_store_ids as $ids) {
                $auth_store_ids = $ids;
                break;
            }

            $canchangestatusTo2030 = (in_array($transfer->from_store_id, $store_ids)) ? 1 : 0;
            $canchangestatusTo40 = (in_array($transfer->to_store_id, $auth_store_ids)) ? true : false;

            $users_notif = User::wherehas('stores', function ($q) use ($store_ids) {
                return $q->whereIn('store_id', $store_ids);
            })->get();


            $notefun = "";

            if (Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 10 && $canchangestatusTo2030 && $status_id == 20) {
                $notefun = "transfer_status_" . $status_id;
                $transfer->update(['transfer_status_id' => $status_id]);

                foreach ($transfer->products as $p) {

                    array_push($store_stock_arr_from, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'q_in_store' => -$p->pivot->Quantity,
                        'transfer_q_reserved' => -$p->pivot->Quantity,
                        'transfer_out' => $p->pivot->Quantity,
                    ]);

                    array_push($store_stock_arr_to, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'transfer_in' => $p->pivot->Quantity,
                    ]);
                }


                $from_store = Store::find($transfer->from_store_id);
                $from_store->stock_update($store_stock_arr_from, '+');

                $to_store = Store::find($transfer->to_store_id);
                $to_store->stock_update($store_stock_arr_to, '+');
            }

            if (Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 20 && $canchangestatusTo2030 && $status_id == 30) {
                $notefun = "transfer_status_" . $status_id;
                $transfer->update(['transfer_status_id' => $status_id]);

                foreach ($transfer->products as $p) {

                    array_push($store_stock_arr_from, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'store_q_net' => $p->pivot->Quantity,
                        'q_in_store' => $p->pivot->Quantity,
                        'transfer_out' => -$p->pivot->Quantity,
                    ]);

                    array_push($store_stock_arr_to, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'transfer_in' => -$p->pivot->Quantity,
                    ]);
                }

                $from_store = Store::find($transfer->from_store_id);
                $from_store->stock_update($store_stock_arr_from, '+');

                $to_store = Store::find($transfer->to_store_id);
                $to_store->stock_update($store_stock_arr_to, '+');
            }

            if (Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 20 && $canchangestatusTo40 && $status_id == 40) {
                $notefun = "transfer_status_" . $status_id;
                $transfer->update(['transfer_status_id' => $status_id]);

                foreach ($transfer->products as $p) {

                    array_push($store_stock_arr_from, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'transfer_out' => -$p->pivot->Quantity,
                    ]);

                    array_push($store_stock_arr_to, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'store_q_net' => $p->pivot->Quantity,
                        'q_in_store' => $p->pivot->Quantity,
                        'transfer_in' => -$p->pivot->Quantity,
                    ]);
                }

                $from_store = Store::find($transfer->from_store_id);
                $from_store->stock_update($store_stock_arr_from, '+');

                $to_store = Store::find($transfer->to_store_id);
                $to_store->stock_update($store_stock_arr_to, '+');
            }
            
            
            if (Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 40 && $canchangestatusTo40 && $status_id == '-20') {
                // return "ok";
                // $notefun = "transfer_status_" . $status_id;
                $transfer->update(['transfer_status_id' => 20]);

                foreach ($transfer->products as $p) {

                    array_push($store_stock_arr_from, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'transfer_out' => +$p->pivot->Quantity,
                    ]);

                    array_push($store_stock_arr_to, [
                        'product_id' => $p->pivot->product_id,
                        'runID' => $p->pivot->RunID,
                        'store_q_net' => -$p->pivot->Quantity,
                        'q_in_store' => -$p->pivot->Quantity,
                        'transfer_in' => +$p->pivot->Quantity,
                    ]);
                }

                $from_store = Store::find($transfer->from_store_id);
                $from_store->stock_update($store_stock_arr_from, '+');

                $to_store = Store::find($transfer->to_store_id);
                $to_store->stock_update($store_stock_arr_to, '+');
            }

            DB::commit();

            if ($notefun) {
                $notif = Notif::create([
                    'user_create_id' => Auth::id(),
                    'notefun' => $notefun,
                    'table_name' => 'transfers',
                    'noteType' => 'Transfer',
                    'notifiable_type' => 'App\\Transfer',
                    'notifiable_id' => $transfer->id,
                ]);

                $notif->users()->sync($users_notif);
                event(new TransferEvt($users_notif, $notif, $notif->notif_html()));
            }
        }



        $notification = notification('تم تغيير الحالة بنجاح', true);

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transfers.show', $transfer->id)->with($notification);
    }

    public function getrunids($store_id, $product_id, $transfer = "")
    {
        $time = microtime(true);
        if ($transfer) {
            DB::beginTransaction();
            $transfer = Transfer::findOrFail($transfer);
            $transfer->delete();
        }

        $runIDs = ViewStockClosed::where(['product_id' => $product_id, 'store_id' => $store_id])->where('store_q_net', '>', 0)->pluck('runID');

        $runIDs = (array) $runIDs;
        $runIDs =  reset($runIDs);
        if (count($runIDs) == 1) {
            $select_runIDs = select(['errors' => '', 'name' => 'runID', 'frkName' => 'runID', 'rows' => $runIDs, 'transAttr' => true, 'notrans' => true, 'select_id' => $runIDs[0], 'label' => true, 'cols' => 1]);
        } else {
            $select_runIDs = select(['errors' => '', 'name' => 'runID', 'frkName' => 'runID', 'rows' => $runIDs, 'transAttr' => true, 'notrans' => true, 'label' => true, 'cols' => 1]);
        }
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return $select_runIDs;
    }

    public function exceltransfer(Request $request, User $user)
    {
        return "ok";
        $lastTransfer = Transfer::orderBy('id', 'desc')->first();
        $lastTransfer_id = ($lastTransfer)? $lastTransfer->id : 1;
        
        if(Transfer::where('Transfer_code', $lastTransfer_id)->count()>0){
            do{
                $lastTransfer_id +=1;
            }while(Transfer::where('Transfer_code', $lastTransfer_id)->count()!=0);
        }
        
        DB::beginTransaction();
        $t = [
            'transfer_code'=>$lastTransfer_id,
            'transfer_status_id'=>10,
            'transfer_name'=>'Company',
            'transfer_phone'=>'01124414458',
            'transfer_details'=>'Send',
            'from_store_id'=>'22',
            'to_store_id'=>'21',
            'user_id'=>1,
        ];
        
        $transfer = Transfer::create($t); 
        // return "ok";
        
        $CSVfp = fopen("dbupload/store.csv", "r");
        $attachProducts = [];
        if ($CSVfp !== FALSE) {
            
            while (!feof($CSVfp)) {
                $data = fgetcsv($CSVfp, 10000, ",");
                echo $data[0] . "<br>";
                if (!empty($data)){
                    $pros = Product::where('Product_Name', $data[0])->get();
                    if($pros->count() < 1){
                        $pros = Product::where('Product_Name', 'LIKE', '%'. $data[0] . '%')->get();
                        if($pros->count() !== 1){
                            $pros = Product::where('id', $data[2])->get();
                        }
                    }
                    $product_id = $pros->first()->id;

                    $runID = InpermitProduct::where('product_id', $product_id)->first()->runID;
                    TransferProduct::create([
                        'transfer_id'=>$transfer->id,
                        'product_id'=>$product_id,
                        'Quantity'=>$data[1],
                        'RunID'=>$runID,
                    ]);
                }
            }
            
        }
        fclose($CSVfp);
        DB::commit();
        
    }

}
