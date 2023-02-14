<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Invoice;
use App\Models\Get;
use App\Models\InvoicePayType;
use App\Models\ViewVoucherProductMinusInvoice;
use App\Models\Client;
use App\Models\Generalpolicy;
use App\Models\InvoiceStatus;
use App\Models\Notif;
use App\Models\Region;
use App\Models\User;
use App\Models\ViewClient;
use App\Models\ViewInvoice;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class invoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    

    public function index($next='next')
    {
        $time = microtime(true);
        $users = User::has('usergets')->orderBy('fullName', 'ASC')->get();
        // return $users;
        $invoices = null;
        if(Auth::user()->can('View_all_invoices')){
            $where = [];
        }else if(Auth::user()->can('Show_his_invoices')){
            $where =['user_rep_id'=>Auth::user()->id];
        }

        // return $next;

        if($next=='next'){
            $invoices = Invoice::where($where)->whereHas('view_invoice', function ($q) use($where)
            {
                return $q->where('get_nexts', '>', 0)->where($where);
            })->orderBy('invoice_status_id', 'ASC')->get();
        }else if($next=='paid'){
            $invoices = Invoice::where($where)->whereHas('view_invoice', function ($q)
            {
                return $q->where('get_nexts', '=', 0);
            })->orderBy('invoice_status_id', 'ASC')->get();
        }else if($next=='wait'){
            $invoices = Invoice::where($where)->where('invoice_status_id', '<', 20)->orderBy('invoice_status_id', 'ASC')->get();
        }else if($next=='all'){
            $invoices = Invoice::where($where)->orderBy('invoice_status_id', 'ASC')->get();
        }

        $invoice_satatus = InvoiceStatus::all();


        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.invoices.index', compact(['invoices', 'next', 'invoice_satatus', 'users']));
    }

    public function yajrainvoices($next='next')
    {
        $time = microtime(true);
        $invoices = null;
        if(Auth::user()->can('View_all_invoices')){
            $where = [];
        }else if(Auth::user()->can('Show_his_invoices')){
            $where =['user_rep_id'=>Auth::user()->id];
        }

        if($next=='next'){
            $invoices = ViewInvoice::where($where)->where('get_nexts', '>', 0)
            ->orderBy('invoice_status_id', 'ASC')->get();
        }else if($next=='paid'){
            $invoices = ViewInvoice::where($where)->where('get_nexts', '=', 0)
            ->orderBy('invoice_status_id', 'ASC')->get();
        }else if($next=='wait'){
            $invoices = ViewInvoice::where($where)->where('invoice_status_id', '<', 20)->orderBy('invoice_status_id', 'ASC')->get();
        }else if($next=='all'){
            $invoices = ViewInvoice::where($where)->orderBy('invoice_status_id', 'ASC')->get();
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($invoices)
        ->addColumn('actions',
            function($invoice) {
                $html ='<span class="actionLinks">';
                $html .= indexView($invoice, 'invoices');
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
    public function create(Voucher $voucher, $payType=30)
    {
        $time = microtime(true);
        if(!Auth::user()->voucher_id
        || Auth::user()->voucher_id != $voucher->id
        || Auth::user()->voucher->voucher_status !=3){
            $notification = array(
                'message' => 'عذرا لا يمكن إنشاء فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
                'alert-type' => 'error',
                'error' => 'عذرا لا يمكن إنشاء فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
            );
            return back()->withInput()->with($notification);
        }

        $store_region_ids = Region::whereHas('stores', function($q){
            $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
        })->pluck('id');

        $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');


        $clients = ViewClient::whereIn('region_id', $region_ids)->where('is_active', 1)->orderBy('client_name', 'ASC')->get();
        $pay_types = InvoicePayType::all();
        $products_ids = $voucher->products->pluck('id');
        $products = Product::whereIn('id', $products_ids)->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.invoices.create', compact(['voucher', 'clients', 'pay_types', 'products', 'payType']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\InvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $time = microtime(true);
        $start = microtime(true);
        $voucher = Voucher::findOrFail($request->voucher_id);

        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        //Remove imgDate from request to prevent logout issue duo to tooo very long string of ImgData string value
        $request->merge([
            'imgData' => '',
        ]);

        // return $request;

        $this->validate($request, [
            'client_id' => 'required|numeric',
            'invoice_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'product_ids' => 'required|array', 
            'product_ids.*' => 'numeric',
            'invoice_quantitys' => 'required|array',
            'invoice_quantitys.*' => 'numeric',
            'discounts' => 'required|array',
            'discounts.*' => 'numeric|min:0|max:100',
            'client_pay' => 'required',
        ]);

        if(!$request->invoice_code || trim($request->invoice_code) == ''){
            $notification = notification('أدخل كود الفاتورة', false);
            return back()->withInput()->with($notification);
        }

        $count = count($request->product_ids);

        if($count != count($request->runIDs)
            || $count != count($request->invoice_quantitys)
            || $count != count($request->bounces)
            || $count != count($request->discounts)
            || $count != count($request->pay_prices)
            || $count != count($request->pay_quantitys)
            || $count != count($request->paid_prices)
            || $count != count($request->paid_nexts)
        ){
            $notification = array(
                'message' => 'من فضلك إدخل تفاصيل الاصناف في الفاتورة بشكل صحيح',
                'alert-type' => 'error',
                'error' => 'من فضلك إدخل تفاصيل الاصناف في الفاتورة بشكل صحيح',
            );
            return back()->withInput()->with($notification);
        }

        if(!Auth::user()->voucher_id
        || Auth::user()->voucher_id != $request->voucher_id
        || Auth::user()->voucher->voucher_status !=3){
            $notification = array(
                'message' => 'عذرا لا يمكن إنشاء فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
                'alert-type' => 'error',
                'error' => 'عذرا لا يمكن إنشاء فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
            );
            return back()->withInput()->with($notification);
        }

        $error_flag=false;
        $i=0;
        $total_pay_prices = 0;
        $total_paid_prices = 0;
        $total_paid_nexts = 0;
        $attachProducts = [];
        $attachProducts_gets = [];
        foreach ($request->product_ids as $product_id) {
            $is_runId = ViewVoucherProductMinusInvoice::where(['product_id'=>$product_id,
                                                            'voucher_id'=>$request->voucher_id,
                                                            'runID'=>$request->runIDs[$i]])->where('net_q', '>', 0)->first();
            if(!$is_runId){
                $error_flag= true;
                break;
            }
            $net_q = invoicesController::runIDQuantity($product_id, $request->runIDs[$i], $request->voucher_id, false);
            if($request->invoice_quantitys[$i]>$net_q || $request->invoice_quantitys[$i]<0){
                $error_flag= true;
                break;
            }

            if($request->bounces[$i]<0){
                $error_flag= true;
                break;
            }

            //discount Check here

            //discount Check End

            if($request->pay_quantitys[$i]<0
            || $request->pay_quantitys[$i] > $request->invoice_quantitys[$i]){
                $error_flag= true;
                break;
            }

            $product = Product::find($product_id);

            $Public_Price = Public_Price($product_id, $request->runIDs[$i]);
            if(!$Public_Price) return Public_Price_Error();

            if(!$product){
                $error_flag= true;
                break;
            }

            $total_pay_prices += round( $Public_Price*(100-$request->discounts[$i]) * $request->invoice_quantitys[$i] / 100, 2);
            $total_paid_prices += round( $Public_Price*(100-$request->discounts[$i]) * $request->pay_quantitys[$i] / 100, 2);
            $total_paid_nexts += round( $Public_Price*(100-$request->discounts[$i]) * ($request->invoice_quantitys[$i] - $request->pay_quantitys[$i]) / 100, 2);

            $attachProducts[$i] = [
                'product_id' => $product_id,
                'runID' => $request->runIDs[$i],
                'invoice_quantity' => $request->invoice_quantitys[$i],
                'invoice_bounce' => $request->bounces[$i],
                'invoice_public_price' =>  $Public_Price,
                'discount' => $request->discounts[$i],
            ];

            $attachProducts_gets[$i] = [
                'product_id' => $product_id,
                'runID' => $request->runIDs[$i],
                'get_quantity'=>$request->pay_quantitys[$i],
            ];





            $i++;
        }

        if($error_flag){
            $notification = array(
                'message' => '#101: من فضلك ادخل الكميات والأسعار بشكل صحيح.!',
                'alert-type' => 'error',
                'error' => '#101: من فضلك ادخل الكميات والأسعار بشكل صحيح.!',
            );
            return back()->withInput()->with($notification);
        }

        $client = Client::where(['id'=>$request->client_id, 'is_active'=>1])->first();
        if(!$client){
            $notification = array(
                'message' => 'لا يمكن إنشاء فاتورة لعميل معطل!',
                'alert-type' => 'error',
                'error' => 'لا يمكن إنشاء فاتورة لعميل معطل!'
            );
            return back()->withInput()->with($notification);
        }

        $get_overPrice_sum = $client->view_client->get_overPrice_sum;
        $paid_from_client_balance = ($get_overPrice_sum > $total_paid_prices)? $total_paid_prices : $get_overPrice_sum;


        $get_overPrice = $request->client_pay - ($total_paid_prices - $paid_from_client_balance);
        // return $get_overPrice;
        if($get_overPrice < -4.99){

            $notification = notification('فرق الحساب الذي يضاف الي حساب العميل يجب ان لا يقل علي عن -4.99 (القيمة بالسالب).!', false);
            return back()->withInput()->with($notification);
        }

        if(abs(round($total_paid_nexts + $total_paid_prices - $total_pay_prices, 2)) > 0.02  ){
            $notification = array(
                'message' => 'هناك عدم توافق في حساب قيم السداد الاجل والنقدي.! '.
                '('.$total_paid_nexts .'+'. $total_paid_prices.') != '. $total_pay_prices,
                'alert-type' => 'error',
                'error' => 'هناك عدم توافق في حساب قيم السداد الاجل والنقدي.! '.
                '('.$total_paid_nexts .'+'. $total_paid_prices.') != '. $total_pay_prices,
            );
            return back()->withInput()->with($notification);
        }

        // return back()->withInput();
        if (!file_exists(Invoice::files_path($request->client_id))) {
            mkdir(Invoice::files_path($request->client_id), 0755, true);
        }

        $imageName = '';
        if($imgData){
            $img = $imgData;


            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'Invice-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Invoice::files_path($request->client_id).$imageName;
            // return  $imageData;
            file_put_contents($imagePath, $imageData);

        }

        $users_notif = User::users_allow(['change_invoice_status'], $voucher->store->users);

        $invoice_code = $request->invoice_code;

        $old_invoice = Invoice::where(['invoice_code'=>$invoice_code])->first();
        if($old_invoice){

            $invoice_code = 'M' .$client->region_id . "_" . $client->id . "_" . Auth::id() . '#' . $request->invoice_code . '#';

            $old_invoice = Invoice::where(['invoice_code'=>$invoice_code])->first();
            if($old_invoice){

                do{
                    $invoice_code_time = time().'**'.$invoice_code;
                    $old_invoice = Invoice::where(['invoice_code'=>$invoice_code_time])->first();
                }while($old_invoice);

                $invoice_code = $invoice_code_time;
            }
        }

        // return $invoice_code;

        DB::beginTransaction();



        // try {
            $invoice = Invoice::create([
                'voucher_id' => $request->voucher_id,
                'invoice_status_id' => 1,
                'client_id' => $request->client_id,
                'user_rep_id' => Auth::id(),
                'image' => $imageName,
                'invoice_code' => $invoice_code,
                'invoice_details' => $request->invoice_details,
                'invoice_date' => $request->invoice_date,
            ]);

            $invoice->products()->sync($attachProducts);

            $get_code = rand(1,999999);

            do{
                $get_code = rand(1,999999);
                $isget = Get::where(['get_code'=>$get_code])->first();
            }while($isget);

            $get = Get::create([
                'invoice_id' => $invoice->id,
                'get_code' => $get_code,
                'get_date' => $invoice->invoice_date,
                'user_rep_id' => Auth::id(),
                'get_overPrice' => $get_overPrice,
                'paid_from_client_balance' => $paid_from_client_balance,
                'client_pay' => $request->client_pay,
            ]);

            $arr_i = 0;
            foreach ($attachProducts_gets as $attachProducts_get) {
                $product = $invoice->products()->wherePivot('product_id',$attachProducts_get['product_id'])
                ->wherePivot('runID',$attachProducts_get['runID'])->first();
                unset($attachProducts_gets[$arr_i]['product_id'] );
                unset($attachProducts_gets[$arr_i]['runID'] );
                $attachProducts_gets[$arr_i]['invoice_product_id'] = $product->pivot->id;
                $arr_i++;
            }

            $get->GetProducts()->sync($attachProducts_gets);

            $notif = Notif::create([
                'user_create_id' => Auth::id(),
                'notefun' => 'createInvoice',
                'table_name' => 'invoices',
                'noteType' => 'Invoice',
                'notifiable_type' => 'App\\Invoice',
                'notifiable_id' => $invoice->id,
            ]);

            $notif->users()->sync($users_notif);
            
            DB::commit();

            event(new TransferEvt($users_notif, $notif, $notif->notif_html()));

            $t = microtime(true) - $start;
            $t =number_format((float)$t, 2, '.', '');
            $notification = array(
                'message' => 'تم الإضافة بنجاح Time: ' . $t,
                'alert-type' => 'success',
                'success' => 'تم الإضافة بنجاح Time: ' . $t,
            );

            execution_time_php($time, __class__ . '@' .__FUNCTION__);
            return redirect()->route('invoices.show', [$invoice->id])->with($notification);
        // } catch (\Throwable $th) {
        //     $notification = array(
        //         'message' => '#102 حدث خطأ أثناء الحفظ، حاول مجددا أو تواصل مع الإدارة. .!',
        //         'alert-type' => 'error',
        //         'error' => '#102 حدث خطأ أثناء الحفظ، حاول مجددا أو تواصل مع الإدارة. .!',
        //     );
        //     return back()->withInput()->with($notification);
        // }

    }

    public function show(Invoice $invoice)
    {
        $time = microtime(true);
        // return $invoice->view_invoice_products;
        return view('dashboard.invoices.show', compact(['invoice']));
    }
   
    public function showcode($invoice)
    {
        $time = microtime(true);
        $invoice = Invoice::where('invoice_code', $invoice)->first();
        if($invoice){
            return redirect()->route('invoices.show', [$invoice->id]); 
        }else{
            return "لا يوجد فاتورة بهذا الكود!";
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inpermit  $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice, $payType=30)
    {
        $time = microtime(true);
        $payType=30;
        if(!$invoice->status->id == 20){
            $notification = notification('عذرا لا يمكن التعديل علي فاتورة تم المواقة عليها', false);
            return back()->withInput()->with($notification);
        }

        if( Auth::user()->voucher_id != $invoice->voucher_id
        || Auth::user()->voucher->voucher_status !=3
        || $invoice->status->id == 20){
            $notification = notification('لا يمكن التعديل علي الفاتورة.', false);
            return back()->withInput()->with($notification);
        }

        if(!Auth::user()->voucher_id
        || Auth::user()->voucher_id != $invoice->voucher_id
        || Auth::user()->voucher->voucher_status !=3){
            $notification = array(
                'message' => 'عذرا لا يمكن تعديل فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
                'alert-type' => 'error',
                'error' => 'عذرا لا يمكن تعديل فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
            );
            return back()->withInput()->with($notification);
        }

        $pay_types = InvoicePayType::where('id', 30)->orderBy('id', 'ASC')->get();
        $store_region_ids = Region::whereHas('stores', function($q){
            $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
        })->pluck('id');
        $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
        $clients = ViewClient::whereIn('region_id', $region_ids)->where('is_active', 1)->orderBy('client_name', 'ASC')->get();
        $products_ids = $invoice->voucher->products->pluck('id');
        $products = Product::whereIn('id', $products_ids)->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.invoices.edit', compact(['invoice', 'pay_types', 'payType', 'clients', 'products']));
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\InvoiceRequest  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $time = microtime(true);
        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        //Remove imgDate from request to prevent logout issue duo to tooo very long string of ImgData string value
        $request->merge([
            'imgData' => '',
        ]);


        $this->validate($request, [
            'client_id' => 'required|numeric',
            'invoice_date' => 'required|date|before_or_equal:'.Carbon::now().'|after_or_equal:'.$invoice->voucher->voucher_date,
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            //'invoice_code' => 'required|unique:invoices,invoice_code',
            // 'imgData' => 'required',
            'invoice_quantitys' => 'required|array',
            'invoice_quantitys.*' => 'numeric',
            'discounts' => 'required|array',
            'discounts.*' => 'numeric|min:0|max:100',
            'client_pay' => 'required',
        ]);

        if(!$invoice->status->id == 20){
            $notification = notification('عذرا لا يمكن التعديل علي فاتورة تم المواقة عليها', false);
            return back()->withInput()->with($notification);
        }

        if( Auth::user()->voucher_id != $invoice->voucher_id
        || Auth::user()->voucher->voucher_status !=3
        || $invoice->status->id == 20){
            $notification = notification('لا يمكن التعديل علي الفاتورة.', false);
            return back()->withInput()->with($notification);
        }

        $count = count($request->product_ids);

        if($count != count($request->runIDs)
            || $count != count($request->invoice_quantitys)
            || $count != count($request->bounces)
            || $count != count($request->discounts)
            || $count != count($request->pay_prices)
            || $count != count($request->pay_quantitys)
            || $count != count($request->paid_prices)
            || $count != count($request->paid_nexts)
        ){
            $notification = array(
                'message' => 'من فضلك إدخل تفاصيل الاصناف في الفاتورة بشكل صحيح',
                'alert-type' => 'error',
                'error' => 'من فضلك إدخل تفاصيل الاصناف في الفاتورة بشكل صحيح',
            );
            return back()->withInput()->with($notification);
        }

        if(!Auth::user()->voucher_id
        || Auth::user()->voucher_id != $invoice->voucher_id
        || Auth::user()->voucher->voucher_status !=3){
            $notification = array(
                'message' => 'عذرا لا يمكن تعديل فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
                'alert-type' => 'error',
                'error' => 'عذرا لا يمكن تعديل فاتورة من إذن صرف غير مفتوح أو غير مربوط بالمندوب.!',
            );
            return back()->withInput()->with($notification);
        }

        $error_flag=false;
        $i=0;
        $total_pay_prices = 0;
        $total_paid_prices = 0;
        $total_paid_nexts = 0;
        $attachProducts = [];
        $attachProducts_gets = [];

        foreach ($request->product_ids as $product_id) {
            $is_runId = ViewVoucherProductMinusInvoice::where(['product_id'=>$product_id,
                                                            'voucher_id'=>$invoice->voucher_id,
                                                            'runID'=>$request->runIDs[$i]])->first();
            if(!$is_runId){
                return '!$is_runId';
                $error_flag= true;
                break;
            }

            $net_q = invoicesController::runIDQuantityEdit($product_id, $request->runIDs[$i], $invoice->id, false);

            if($request->invoice_quantitys[$i]>$net_q || $request->invoice_quantitys[$i]<0){
                $error_flag= true;
                return '!$request->invoice_quantitys[$i]>$net_q || $request->invoice_quantitys[$i]<0';
                break;
            }

            if($request->bounces[$i]<0){
                return 'if($request->bounces[$i]<0){';
                $error_flag= true;
                break;
            }

            //discount Check here

            //discount Check End

            if($request->pay_quantitys[$i]<0
            || $request->pay_quantitys[$i] > $request->invoice_quantitys[$i]){
                return '|| $request->pay_quantitys[$i] > $request->invoice_quantitys[$i]){';
                $error_flag= true;
                break;
            }

            $product = Product::find($product_id);
            $Public_Price = Public_Price($product_id, $request->runIDs[$i]);
            if(!$Public_Price) return Public_Price_Error();

            if(!$product){
                return '!$product';
                $error_flag= true;
                break;
            }

            $total_pay_prices += round($Public_Price*(100-$request->discounts[$i]) * $request->invoice_quantitys[$i] / 100, 2);
            $total_paid_prices += round($Public_Price*(100-$request->discounts[$i]) * $request->pay_quantitys[$i] / 100, 2);
            $total_paid_nexts += round($Public_Price*(100-$request->discounts[$i]) * ($request->invoice_quantitys[$i] - $request->pay_quantitys[$i]) / 100, 2);

            // return $Public_Price;
            $attachProducts[$i] = [
                'product_id' => $product_id,
                'runID' => $request->runIDs[$i],
                'invoice_quantity' => $request->invoice_quantitys[$i],
                'invoice_bounce' => $request->bounces[$i],
                'invoice_public_price' => $Public_Price,
                'discount' => $request->discounts[$i],
            ];

            $attachProducts_gets[$i] = [
                'product_id' => $product_id,
                'runID' => $request->runIDs[$i],
                'get_quantity'=>$request->pay_quantitys[$i],
            ];





            $i++;
        }

        if($error_flag){
            $notification = array(
                'message' => '#101: من فضلك ادخل الكميات والأسعار بشكل صحيح.!',
                'alert-type' => 'error',
                'error' => '#101: من فضلك ادخل الكميات والأسعار بشكل صحيح.!',
            );
            return back()->withInput()->with($notification);
        }

        $client = Client::where(['id'=>$request->client_id, 'is_active'=>1])->first();
        if(!$client){
            $notification = array(
                'message' => 'لا يمكن تعديل فاتورة لعميل معطل!',
                'alert-type' => 'error',
                'error' => 'لا يمكن تعديل فاتورة لعميل معطل!'
            );
            return back()->withInput()->with($notification);
        }

        $get_overPrice_sum = $client->view_client->get_overPrice_sum;
        $paid_from_client_balance = ($get_overPrice_sum > $total_paid_prices)? $total_paid_prices : $get_overPrice_sum;


        $get_overPrice = $request->client_pay - ($total_paid_prices - $paid_from_client_balance);
        // return $get_overPrice;
        if($get_overPrice < -4.99){

            $notification = notification('فرق الحساب الذي يضاف الي حساب العميل يجب ان لا يقل علي عن -4.99 (القيمة بالسالب).!', false);
            return back()->withInput()->with($notification);
        }

        if(abs(round($total_paid_nexts + $total_paid_prices - $total_pay_prices, 2)) > 0.02  ){
            $notification = array(
                'message' => 'هناك عدم توافق في حساب قيم السداد الاجل والنقدي.! '.
                '('.$total_paid_nexts .'+'. $total_paid_prices.') != '. $total_pay_prices,
                'alert-type' => 'error',
                'error' => 'هناك عدم توافق في حساب قيم السداد الاجل والنقدي.! '.
                '('.$total_paid_nexts .'+'. $total_paid_prices.') != '. $total_pay_prices,
            );
            return back()->withInput()->with($notification);
        }

        // return back()->withInput();
        $invoice_status_id = ($invoice->invoice_status_id==10)? 15 : 1;
        $imageName = "";
        if (!file_exists(Invoice::files_path($request->client_id))) {
            mkdir(Invoice::files_path($request->client_id), 0755, true);
        }

        $imageName = $invoice->getRawOriginal('image');
        if($imgData){
            $img = $imgData;


            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'Invice-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Invoice::files_path($request->client_id).$imageName;
            // return  $imageData;
            file_put_contents($imagePath, $imageData);

            if (file_exists($invoice->image_rel_path())) {
                $invoice->image_delete();
            }
        }
        $imageName = ($imageName)? $imageName : $invoice->image;

        $users_notif = User::users_allow(['change_invoice_status'], $invoice->voucher->store->users);

        DB::beginTransaction();

        // try {
            $invoice->update([
                'voucher_id' => $invoice->voucher_id,
                'invoice_status_id' => $invoice_status_id,
                'client_id' => $request->client_id,
                'user_rep_id' => Auth::id(),
                'image' => $imageName,
                'invoice_code' => $request->invoice_code,
                'invoice_details' => $request->invoice_details,
                'invoice_date' => $request->invoice_date,
            ]);

            $invoice->products()->detach();
            $invoice->gets()->delete();
            $invoice->products()->attach($attachProducts);

            $get_code = rand(1,999999);

            do{
                $get_code = rand(1,999999);
                $isget = Get::where(['get_code'=>$get_code])->first();
            }while($isget);

            $get = Get::create([
                'invoice_id' => $invoice->id,
                'get_code' =>$get_code,
                'get_date' => $invoice->invoice_date,
                'user_rep_id' => Auth::id(),
                'get_overPrice' => $get_overPrice,
                'paid_from_client_balance' => $paid_from_client_balance,
                'client_pay' => $request->client_pay,
            ]);

            $arr_i = 0;
            foreach ($attachProducts_gets as $attachProducts_get) {
                $product = $invoice->products()->wherePivot('product_id',$attachProducts_get['product_id'])
                ->wherePivot('runID',$attachProducts_get['runID'])->first();
                unset($attachProducts_gets[$arr_i]['product_id'] );
                unset($attachProducts_gets[$arr_i]['runID'] );
                $attachProducts_gets[$arr_i]['invoice_product_id'] = $product->pivot->id;
                $arr_i++;
            }

            $get->GetProducts()->detach();
            $get->GetProducts()->attach($attachProducts_gets);

            $notif = Notif::create([
                'user_create_id' => Auth::id(),
                'notefun' => 'updateInvoice',
                'table_name' => 'invoices',
                'noteType' => 'Invoice',
                'notifiable_type' => 'App\\Invoice',
                'notifiable_id' => $invoice->id,
            ]);

            $notif->users()->sync($users_notif);

            DB::commit();

            event(new TransferEvt('', $notif, $notif->notif_html()));

            $notification = array(
                'message' => 'تم التعديل بنجاح',
                'alert-type' => 'success',
                'success' => 'تم التعديل بنجاح',
            );

            execution_time_php($time, __class__ . '@' .__FUNCTION__);
            return redirect()->route('invoices.show', [$invoice->id])->with($notification);
    }
    
    public function editdate(Invoice $invoice)
    {
        $time = microtime(true);
        return view('dashboard.invoices.editdate', compact(['invoice']));
    }

    public function updatedate(Request $request, Invoice $invoice)
    {
        $time = microtime(true);
        $invoice->update(['invoice_date'=>$request->invoice_date]);
        $notification = array(
            'message' => 'تم تعديل تاريخ الفاتورة بنجاح بنجاح',
            'alert-type' => 'success',
            'success' => 'تم تعديل تاريخ الفاتورة بنجاح بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('invoices.show', [$invoice->id])->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $time = microtime(true);
        if($invoice->user_rep_id != Auth::user()->id && !Auth::user()->can('delete_accepted_invoices')){
            $notification = array(
                'message' => 'عذرا، لن يتمكن من حذف الفاتورة إلا من قام بإنشائها',
                'alert-type' => 'error',
                'error' => 'عذرا، لن يتمكن من حذف الفاتورة إلا من قام بإنشائها',
            );
            return back()->withInput()->with($notification);
        }

        if($invoice->invoice_status_id ==20 && !Auth::user()->can('delete_accepted_invoices')){
            $notification = array(
                'message' => 'عذرا لا يمكن حذف فاتورة تم الموافقة عليها ',
                'alert-type' => 'error',
                'error' => 'عذرا لا يمكن حذف فاتورة تم الموافقة عليها ',
            );
            return back()->withInput()->with($notification);
        }

        if(floatval($invoice->view_invoice->client_balance_effect) > 0 || floatval($invoice->view_invoice->get_overPrice_sum)>0){
            $notification = notification('عذرا لا يمكن حذف فاتورة أثرت في محفظة العميل !');
            return back()->withInput()->with($notification);
        }

        $next = ($invoice->get_nexts>0)? 'next' : 'paid';
        if (file_exists($invoice->image_rel_path())) {
            $invoice->image_delete();
        }
        if(!$invoice->client->is_first_add){
            $invoice->client->forceDelete();
        }else{
            $invoice->delete();
        }

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('invoices.index', $next)->with($notification);
    }


    // public function fromto($store_id)
    // {
    //     $products = ViewStockClosed::groupby('product_id')->distinct()->where(['store_id'=>$store_id])->where('store_q_net', '>', 0)->orderBy('Product_Name')->get();

    //     $html = "";
    //     if($products){
    //         $html= select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>3]);
    //     }
    //     return $html;
    // }

    public function runIDQuantity($product_id, $runID, $voucher_id, $json=false)
    {
        $time = microtime(true);
        $pro = ViewVoucherProductMinusInvoice::where(['product_id'=>$product_id, 'runID'=>$runID, 'voucher_id'=>$voucher_id])->first();
        if( !$pro){
            return false;
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        if(!$json){
            return $pro->net_q;
        }else{
            $Public_Price = Public_Price($pro->product_id, $pro->runID);
            if(!$Public_Price) return Public_Price_Error();

            return response()->json([
                'id'=>$pro->id,
                'voucher_id'=>$pro->voucher_id,
                'product_id'=>$pro->product_id,
                'runID'=>$pro->runID,
                'voucher_quantity'=>$pro->voucher_quantity,
                'invoice_net_q'=>$pro->invoice_net_q,
                'net_q'=>$pro->net_q,
                'Public_Price'=>$Public_Price,
                'Min_Discount'=>$pro->product->Min_Discount,
                'Max_Discount'=>$pro->product->Max_Discount,
            ]);
        }
    }

    public function runIDQuantityEdit($product_id, $runID, $invoice_id, $json=false)
    {
        $time = microtime(true);
        $invoice = Invoice::findOrFail($invoice_id);
        $invoice_product = $invoice->view_invoice_products->where('product_id', '=', $product_id)->where('runID', '===', $runID)->first();

        $pro = ViewVoucherProductMinusInvoice::where(['product_id'=>$product_id, 'runID'=>$runID, 'voucher_id'=> $invoice->voucher_id])->first();
        if( !$pro || !$invoice){
            return false;
        }
        if($invoice_product){
            $net_q = $pro->net_q + $invoice_product->invoice_bounce + $invoice_product->invoice_quantity;
        }else{
            $net_q = $pro->net_q;
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        if(!$json){
            return $net_q;
        }else{
            $Public_Price = Public_Price($pro->product_id, $pro->runID);
            if(!$Public_Price) return Public_Price_Error();

            return response()->json([
                'id'=>$pro->id,
                'voucher_id'=>$pro->voucher_id,
                'product_id'=>$pro->product_id,
                'runID'=>$pro->runID,
                'voucher_quantity'=>$pro->voucher_quantity,
                'invoice_net_q'=>$pro->invoice_net_q,
                'net_q'=>$net_q,
                'Public_Price'=>$Public_Price,
                'Min_Discount'=>$pro->product->Min_Discount,
                'Max_Discount'=>$pro->product->Max_Discount,
            ]);
        }
    }

    public function getrunid($product_id, $voucher_id, $client_id="", $invoice_id= "")
    {
        $time = microtime(true);
        $allow_due_product = true;
        $paid_discount = 0;
        $due_discount = 0;
        $last_Return_craeted_at = "";
        if($client_id){
            $max_return_period = Generalpolicy::findOrFail(1)->max_return_period;
            $client = ViewClient::find($client_id);
            if($client){
                $allow_due_product = $client->allow_due_product($product_id, $invoice_id);
                $paid_discount = $client->paid_discount($product_id);
                $due_discount = $client->due_discount($product_id);
            }

            //: Skiped temporarly
            // $last_Return = ViewReturnProduct::where(['client_id'=>$client_id, 'product_id'=>$product_id])->where('created_at', '>',  Carbon::now()->subDays($max_return_period))->orderBy('created_at', 'DESC')->first();
            // if($last_Return){
            //     $datetime1 = new DateTime(Carbon::now());
            //     $datetime2 = new DateTime($last_Return->created_at);
            //     $interval = $datetime1->diff($datetime2);
            //     $days = $interval->format('%a');

            //     $last_Return_craeted_at = $max_return_period - $days;
            // }

        }

        $runIDs = ViewVoucherProductMinusInvoice::where(['product_id'=>$product_id, 'voucher_id'=>$voucher_id])->where('net_q', '>', 0)->pluck('runID');
        $runIDs = ViewVoucherProductMinusInvoice::where(['product_id'=>$product_id, 'voucher_id'=>$voucher_id])->pluck('runID');
        $runIDs = (array) $runIDs;
        $runIDs =  reset($runIDs);

        if(count($runIDs) == 1){
            $select_runIDs = select(['errors'=>'', 'name'=>'runID', 'frkName'=>'runID', 'rows'=>$runIDs, 'transAttr'=>true, 'notrans'=>true, 'select_id'=>$runIDs[0], 'label'=>true, 'cols'=>1]);
        }else{
            $select_runIDs = select(['errors'=>'', 'name'=>'runID', 'frkName'=>'runID', 'rows'=>$runIDs, 'transAttr'=>true, 'notrans'=>true, 'label'=>true, 'cols'=>1]);
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return response()->json([
            'allow_due_product'=>$allow_due_product,
            'select_runIDs'=>$select_runIDs,
            'paid_discount'=>$paid_discount,
            'due_discount'=>$due_discount,
            'last_Return_craeted_at'=>$last_Return_craeted_at,
        ]);
    }

    public function changestatus(Invoice $invoice, $status, $api=0)
    {
        $time = microtime(true);
        if($status==20){
            DB::beginTransaction() ;
            $invoice->client->update(['is_first_add'=>1]);
            $invoice->update(['invoice_status_id'=>$status, 'user_accept_id'=>Auth::id()]);
            DB::commit();
        }else{
            $invoice->update(['invoice_status_id'=>$status]);
        }

        if($invoice->rep->id != Auth::id()){
            $notif = Notif::create([
                'user_create_id' => Auth::id(),
                'notefun' => 'invoice_status_'.$status,
                'table_name' => 'invoices',
                'noteType' => 'Invoice',
                'notifiable_type' => 'App\\Invoice',
                'notifiable_id' => $invoice->id,
            ]);

            $notif->users()->sync($invoice->rep);
        }


       

        if($invoice->rep->id != Auth::id()){
            event(new TransferEvt('', $notif, $notif->notif_html()));
        }

        $notification = notification('تم تنفيذ الأجراء بنجاح', true);
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        if($api){
            return true;
        }
        return redirect(url()->previous())->with($notification);
    }

    public function repclientsreset(Request $request, User $user)
    {
        $store_region_ids = Region::whereHas('stores', function($q) use($user){
            $q->whereIn('store_id', $user->stores->pluck('id'));
        })->pluck('id');

        $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');


        $clients = ViewClient::whereIn('region_id', $region_ids)->where('is_active', 1)->orderBy('client_name', 'ASC')->get();
        
        $voucherProducts = $user->voucher->productsMany;
        

        

        DB::beginTransaction();
        $j = 0;
        $client_ids = [7, 11, 12, 15, 16, 24, 30, 31, 32, 33, 34, 35, 36, 39, 42, 45, 46, 47, 53, 57, 61, 62, 65, 72, 78, 79, 80, 81, 83, 91, 92, 93, 95, 97, 100, 102, 103, 104, 107, 108, 109, 113, 123, 124, 126, 135, 137, 141, 148, 149, 184, 220, 236, 239, 250, 264, 282, 298, 302, 320, 321, 326, 327, 328, 336, 345, 347, 350, 353, 358, 361, 368, 369, 372, 392, 401, 412, 413, 420, 424, 433, 437, 443, 444, 453, 456, 462, 463, 464, 465, 466, 468, 471, 492, 500, 504, 506, 507, 508, 509, 512, 513, 514, 524, 538, 543, 550, 562, 571, 578, 582, 583, 584, 587, 588, 602, 614, 619, 639, 648, 656, 671, 675, 677, 683, 685, 691, 692, 696, 703, 717, 718, 724, 725, 731, 736, 737, 738, 739, 748, 749, 750, 754, 760, 761, 767, 777, 787, 790, 791, 797, 801, 802, 809, 814, 818, 819, 823, 830, 845, 848, 854, 855, 860, 863, 867, 883, 886, 892, 898, 901, 907, 908, 914, 916, 924, 966, 989, 999, 1003, 1010, 1013, 1019, 1023, 1025, 1032, 1035, 1036, 1042, 1046, 1051, 1053, 1054, 1056, 1062, 1063, 1064, 1065, 1072, 1073, 1076, 1078, 1086, 1089, 1091, 1094, 1095, 1098, 1100, 1102, 1103, 1104, 1108, 1109, 1111, 1117, 1120, 1121, 1123, 1129, 1131, 1135, 1137, 1138, 1139, 1147, 1149, 1150, 1152, 1156, 1157, 1158, 1162, 1165, 1175, 1177, 1178, 1184, 1186, 1188, 1193, 1196, 1198, 1199, 1207, 1208, 1209, 1218, 1220, 1222, 1223, 1233, 1234, 1241, 1245, 1247, 1251, 1252, 1253, 1254, 1255, 1257, 1258, 1259, 1260, 1261, 1264, 1265, 1270, 1271, 1272, 1274, 1279, 1281, 1282, 1286, 1288, 1289, 1291, 1292, 1296, 1300, 1303, 1306, 1307, 1312, 1313, 1314, 1315, 1317, 1318, 1319, 1320, 1321, 1322, 1324, 1325, 1327, 1330, 1331, 1332, 1333, 1334, 1335, 1336, 1338, 1339, 1340, 1341, 1344, 1345, 1346, 1347, 1348, 1352, 1353, 1355, 1356, 1358, 1359, 1362, 1363, 1368, 1370, 1371, 1374, 1377, 1378, 1379, 1380, 1382, 1383, 1384, 1385, 1388, 1391, 1396, 1397, 1398, 1402, 1406, 1410, 1413, 1415, 1431, 1433, 1440, 1442, 1447, 1448, 1449, 1452, 1456, 1457, 1462, 1469, 1471, 1472, 1479, 1481, 1482, 1489, 1490, 1495, 1497, 1498, 1500, 1501, 1502, 1503, 1504, 1505, 1507, 1508, 1509, 1511, 1512, 1515, 1518, 1519, 1522, 1524, 1526, 1527, 1528, 1529, 1532, 1533, 1535, 1536, 1537, 1539, 1541, 1544, 1545, 1546, 1548, 1549, 1554, 1556, 1557, 1558, 1559, 1560, 1561, 1563, 1564, 1565, 1566, 1567, 1569, 1570, 1571, 1575, 1576, 1578, 1579, 1580, 1581, 1582, 1583, 1585, 1586, 1587, 1589, 1590, 1591, 1594, 1596, 1600, 1601, 1605, 1606, 1607, 1608, 1611, 1612, 1614, 1616, 1618, 1619, 1620, 1624, 1626, 1632, 1633, 1636, 1637, 1638, 1641, 1648, 1652, 1655, 1656, 1657, 1661, 1663, 1664, 1666, 1670, 1671, 1672, 1674, 1683, 1685, 1687, 1690, 1691, 1697, 1698, 1699, 1702, 1705, 1706, 1707, 1709, 1710, 1711, 1715, 1717, 1720, 1721, 1722, 1726, 1729, 1731, 1733, 1734, 1735, 1736, 1738, 1741, 1742, 1743, 1745, 1746, 1747, 1752, 1754, 1756, 1757, 1761, 1762, 1766, 1767, 1769, 1772, 1773, 1775, 1776, 1777, 1779, 1781, 1782, 1784, 1785, 1788, 1790, 1791, 1794, 1795, 1800, 1801, 1803, 1805, 1809, 1810, 1813, 1816, 1817, 1844, 1847, 1850, 1857, 1873, 1877, 1882, 1883, 1887, 1888, 1889, 1893, 1895, 1898, 1899, 1903, 1908, 1909, 1913, 1914, 1915, 1916, 1917, 1918, 1919, 1920, 1921, 1922, 1923, 1924, 1925, 1926, 1927, 1928, 1929, 1930, 1931, 1932, 1933, 1934, 1935, 1936, 1937, 1938, 1939, 1940, 1941, 1942, 1944, 1945, 1946, 1949, 1950, 1951, 1952, 1953, 1954, 1956, 1957, 1958, 1959, 1960, 1961, 1962, 1963, 1964, 1965, 1966, 1968, 1969, 1970, 1971, 1972, 1973, 1974, 1975, 1976, 1977, 1978, 1979, 1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989, 1990, 1991, 1992, 1993, 1994, 1995, 1996, 1998, 1999, 2000, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2022, 2023, 2024, 2025, 2027, 2028, 2029, 2030, 2031, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039, 2040, 2041, 2042, 2043, 2044, 2045, 2046, 2047, 2048, 2049, 2050, 2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059, 2061, 2062, 2063, 2064, 2065, 2066, 2067, 2068, 2069, 2071, 2072, 2075, 2076, 2077, 2078, 2079, 2080, 2081, 2082, 2083, 2084, 2085, 2086, 2087, 2088, 2089, 2090, 2091, 2092, 2093, 2094, 2095, 2096, 2099, 2100, 2101, 2102, 2103, 2104, 2105, 2106, 2107, 2108, 2109, 2110, 2111, 2112, 2113, 2114, 2115, 2116, 2117, 2118, 2119, 2120, 2121, 2122, 2123, 2124, 2125, 2126, 2127, 2128, 2129, 2130, 2131, 2132, 2133, 2134, 2135, 2136, 2137, 2138, 2139, 2140, 2141, 2146, 2147, 2148, 2149, 2150, 2151, 2152, 2153, 2154, 2155, 2156, 2157, 2158, 2159, 2160, 2161, 2162, 2164, 2165, 2166, 2167, 2168, 2169, 2170, 2171, 2172, 2173, 2174, 2175, 2176, 2177, 2178, 2179, 2180, 2181, 2182, 2183, 2184, 2185, 2186, 2187, 2188, 2189, 2190, 2191, 2192, 2194, 2195, 2196, 2197, 2198, 2199, 2200, 2201, 2202, 2203, 2205, 2206, 2207, 2208, 2209, 2210, 2211, 2213, 2214, 2215, 2216, 2217, 2218, 2219, 2220, 2221, 2222, 2223, 2224, 2225, 2226, 2227, 2228, 2229, 2230, 2231, 2232, 2233, 2234, 2235, 2236, 2237, 2238, 2239, 2240, 2241, 2242, 2243, 2244, 2245, 2246, 2247, 2248, 2249, 2250, 2251, 2252, 2253, 2254, 2255, 2256, 2257, 2258, 2259, 2260, 2261, 2262, 2263, 2264, 2265, 2266, 2267, 2268, 2269, 2270, 2271, 2272, 2273, 2274, 2275, 2276, 2278, 2279, 2280, 2281, 2282, 2283, 2284, 2285, 2286, 2287, 2288, 2289, 2290, 2291, 2292, 2293, 2294, 2295, 2296, 2297, 2298, 2299, 2300, 2301, 2302, 2303, 2304, 2305, 2306, 2307, 2308, 2309, 2310, 2311, 2312, 2313, 2314, 2315, 2316, 2317, 2318, 2319, 2320, 2321, 2322, 2323, 2324, 2325, 2326, 2328, 2329, 2330, 2331, 2332, 2333, 2334, 2335, 2336, 2337, 2338, 2339, 2340, 2341, 2342, 2343, 2344, 2345, 2346, 2347, 2348, 2349, 2350, 2351, 2352, 2353, 2354, 2355, 2356, 2357, 2358, 2359, 2360, 2361, 2362, 2363, 2364, 2365, 2366, 2367, 2368, 2369, 2370, 2371, 2372, 2373, 2374, 2375, 2376, 2377, 2378, 2379, 2380, 2381, 2382, 2383, 2384, 2385, 2386, 2387, 2388, 2389, 2390, 2391, 2392, 2393, 2395, 2396, 2397, 2398, 2399, 2400, 2401, 2402, 2403, 2404, 2405, 2406, 2407, 2408, 2409, 2410, 2411, 2412, 2413, 2415, 2416, 2417, 2418, 2419, 2420, 2421, 2422, 2423, 2424, 2425, 2426, 2427, 2428, 2429, 2430, 2431, 2432, 2433, 2434, 2435, 2436, 2437, 2438, 2439, 2440, 2441, 2442, 2443, 2444, 2445, 2446, 2448, 2449, 2450, 2451, 2452, 2453, 2454, 2455, 2456, 2457, 2458, 2460, 2461, 2462, 2463, 2464, 2466, 2467, 2468, 2469, 2470, 2471, 2472, 2473, 2474, 2475, 2476, 2477, 2478, 2479, 2480, 2481, 2482, 2483, 2484, 2485, 2486, 2487, 2488, 2489, 2490, 2491, 2492, 2493, 2494, 2495, 2496, 2497, 2498, 2499, 2500, 2501, 2502, 2503, 2504, 2506, 2508, 2509, 2510, 2511, 2512, 2513, 2514, 2515, 2516, 2518, 2519, 2520, 2521, 2522, 2523, 2524, 2525, 2526, 2527, 2528, 2529, 2530, 2531, 2532, 2533, 2534, 2535, 2536, 2537, 2538, 2539, 2540, 2541, 2543, 2544, 2545, 2546, 2547, 2548, 2549, 2550, 2551, 2552, 2553, 2554, 2555, 2556, 2557, 2558, 2559, 2560, 2561, 2562, 2563, 2564, 2565, 2566, 2567, 2568, 2569, 2570, 2571, 2572, 2574, 2575, 2576, 2577, 2578, 2579, 2580, 2581, 2582, 2583, 2584, 2585, 2586, 2587, 2588, 2589, 2590, 2591, 2592, 2593, 2594, 2595, 2596, 2597, 2598, 2599, 2600, 2601, 2602, 2603, 2604, 2605, 2606, 2607, 2608, 2609, 2610, 2611, 2612, 2613, 2615, 2616, 2617, 2618, 2619, 2620, 2621, 2622, 2623, 2624, 2625, 2626, 2627, 2628, 2629, 2630, 2631, 2632, 2633, 2634, 2635, 2636, 2637, 2638, 2639, 2640, 2641, 2642, 2643, 2644, 2645, 2646, 2647, 2648, 2649, 2650, 2651, 2654, 2655, 2656, 2658, 2660, 2662, 2663, 2664, 2665, 2667, 2670, 2671, 2673, 2674, 2675, 2676, 2677, 2678, 2679, 2680, 2681, 2682, 2683, 2684, 2685, 2686, 2687, 2688, 2689, 2690, 2691, 2692, 2693, 2694, 2695, 2698, 2700, 2701, 2702, 2704, 2705, 2706, 2707, 2709, 2710, 2711, 2712, 2713, 2714, 2715, 2716, 2717, 2718, 2719, 2720, 2721, 2722, 2723, 2724, 2725, 2726, 2727, 2728, 2729, 2730, 2731, 2732, 2733, 2735, 2736, 2737, 2738, 2739, 2740, 2741, 2742, 2743, 2744, 2745, 2746, 2747, 2748, 2749, 2750, 2751, 2752, 2753, 2754, 2755, 2756, 2757, 2758, 2759, 2760, 2761, 2762, 2763, 2764, 2765, 2766, 2767, 2768, 2769, 2770, 2771, 2772, 2773, 2774, 2775, 2776, 2777, 2778, 2779, 2780, 2781, 2782, 2783, 2784, 2785, 2786, 2787, 2788, 2789, 2790, 2791, 2792, 2793, 2794, 2795, 2796, 2797, 2798, 2799, 2800, 2801, 2802, 2803, 2804, 2805, 2806, 2807, 2808, 2809, 2810, 2811, 2812, 2813, 2814, 2815, 2816, 2817, 2818, 2819, 2820, 2821, 2822, 2823, 2824, 2825, 2826, 2827, 2828, 2829, 2830, 2831, 2832, 2833, 2834, 2835, 2836, 2837, 2838, 2839, 2840, 2841, 2842, 2843, 2844, 2845, 2846, 2847, 2849, 2850, 2851, 2852, 2853, 2854, 2855, 2856, 2857, 2858, 2859, 2860, 2861, 2862, 2863, 2864, 2865, 2866, 2867, 2868, 2869, 2870, 2871, 2872, 2873, 2874, 2875, 2876, 2877, 2878, 2879, 2880, 2881, 2883, 2884, 2885, 2886, 2887, 2888, 2889, 2890, 2891, 2892, 2893, 2894, 2895, 2896, 2897, 2898, 2899, 2900, 2901, 2902, 2903, 2904, 2905, 2906, 2907, 2908, 2909, 2910, 2911, 2912, 2913, 2914, 2915, 2916, 2917, 2918, 2919, 2920, 2921, 2922, 2923, 2924, 2925, 2926, 2927, 2928, 2929, 2931, 2932, 2933, 2934, 2935, 2936, 2937, 2938, 2939, 2940, 2941, 2942, 2943, 2944, 2945, 2947, 2948, 2949, 2950, 2951, 2952, 2953, 2954, 2955, 2956, 2957, 2958, 2959, 2960, 2961, 2962, 2963, 2964, 2965, 2966, 2967, 2968, 2969, 2970, 2971, 2972, 2973, 2974, 2975, 2976, 2977, 2978, 2979, 2980, 2981, 2982, 2983, 2984, 2985, 2986, 2987, 2988, 2989, 2990, 2991, 2992, 2993, 2994, 2995, 2996, 2997, 2998, 2999, 3000, 3001, 3002, 3003, 3004, 3005, 3006, 3007, 3008, 3009, 3010, 3011, 3012, 3013, 3014, 3015, 3016, 3017, 3018, 3019, 3020, 3021, 3022, 3024, 3025, 3026, 3027, 3028, 3029, 3030, 3031, 3032, 3033, 3034, 3035, 3036, 3037, 3038, 3039, 3040, 3041, 3042, 3043, 3044, 3045, 3046, 3047, 3048, 3049, 3050, 3051, 3052, 3053, 3055, 3056, 3057, 3058, 3059, 3060, 3061, 3062, 3063, 3064, 3065, 3066, 3068, 3069, 3070, 3071, 3072, 3073, 3075, 3077, 3078, 3079, 3080, 3081, 3082, 3083, 3084, 3085, 3086, 3087, 3088, 3089, 3090, 3091, 3092, 3093, 3094, 3095, 3096, 3097, 3098, 3099, 3100, 3101, 3102, 3103, 3104, 3105, 3106, 3107, 3109, 3110, 3111, 3112, 3113, 3114, 3115];
        foreach($clients as $client){
            if(!in_array($client->id, $client_ids)){
                continue;
            }
            echo $j++ . ' -- ' . $client->id . "<br>";
            $attachProducts = [];
            $attachProducts_gets = [];
            $client_pay = 0;
            
            foreach ($voucherProducts as $vp) {
                $publicPrice = Public_Price($vp->product_id, $vp->runID);
                array_push($attachProducts,[
                    'product_id' => $vp->product_id,
                    'runID' => $vp->runID,
                    'invoice_quantity' => 40,
                    'invoice_bounce' => 0,
                    'invoice_public_price' =>  $publicPrice,
                    'discount' => 27,
                ]);
                
                array_push($attachProducts_gets,[
                    'product_id' => $vp->product_id,
                    'runID' => $vp->runID,
                    'get_quantity'=> 40,
                ]);
                $client_pay += $publicPrice * 0.75 * 40;
            }


            // Start: Get Last Invoice_code
            $lastInvoice = Invoice::orderBy('id', 'desc')->first();
            $lastInvoice_id = ($lastInvoice)? $lastInvoice->id : 1;
                        if(Invoice::where('invoice_code', $lastInvoice_id)->count()>0){
                do{
                    $lastInvoice_id +=1;
                }while(Invoice::where('invoice_code', $lastInvoice_id)->count()!=0);
            }
            $invoice_code = $lastInvoice_id;
            // End: Get Last Invoice_code

            // Start: Cteate Invoice
            $invoice = Invoice::create([
                'voucher_id' => $user->voucher_id,
                'invoice_status_id' => 1,
                'client_id' => $client->id,
                'user_rep_id' => $user->id,
                'image' => '',
                'invoice_code' => $invoice_code,
                'invoice_details' => '',
                'invoice_date' => '2020-01-21',
            ]);

            $invoice->products()->sync($attachProducts);

            $lastGet = Get::orderBy('id', 'desc')->first();
            $lastGet_id = ($lastGet)? $lastGet->id : 1;
                        if(Get::where('Get_code', $lastGet_id)->count()>0){
                do{
                    $lastGet_id +=1;
                }while(Get::where('Get_code', $lastGet_id)->count()!=0);
            }
            $get_code = $lastGet_id;

            $get = Get::create([
                'invoice_id' => $invoice->id,
                'get_code' => $get_code,
                'get_date' => $invoice->invoice_date,
                'user_rep_id' => $user->id,
                'get_overPrice' => 0,
                'paid_from_client_balance' => 0,
                'client_pay' => $client_pay,
            ]);

            $arr_i = 0;
            foreach ($attachProducts_gets as $attachProducts_get) {
                
                $product = $invoice->products()->wherePivot('product_id', $attachProducts_get['product_id'])
                ->wherePivot('runID',$attachProducts_get['runID'])->first();
                unset($attachProducts_gets[$arr_i]['product_id'] );
                unset($attachProducts_gets[$arr_i]['runID'] );
                $attachProducts_gets[$arr_i]['invoice_product_id'] = $product->pivot->id;
                $arr_i++;
            }
            $get->GetProducts()->sync($attachProducts_gets);
        }
        DB::commit();
        return "Done";
    }



    public function excelinvoices(Request $request, User $user)
    {
        
        $store_region_ids = Region::whereHas('stores', function($q) use($user){
            $q->whereIn('store_id', $user->stores->pluck('id'));
        })->pluck('id');

        $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');


        $clients = ViewClient::whereIn('region_id', $region_ids)->where('is_active', 1)->orderBy('client_name', 'ASC')->get();
        $clientIds = $clients->pluck('id')->toArray();
        
        $CSVfp = fopen("dbupload/Sohag-Hatem-51.csv", "r");
        // return 
        DB::beginTransaction();
        if ($CSVfp !== FALSE) {
            $r=0;
            $isFirst = true;
            $cclient = "";

            $attachProducts = [];
            $attachProducts_gets = [];
            $client_pay = 0;
            $client_id = '';

            $z = 0;
            $userPay = 0;
            $k=2;
            while (! feof($CSVfp)) {
                $data = fgetcsv($CSVfp, 10000, ",");
                if (! empty($data) && !$isFirst) {
                    $z++;
                    $invoice_date = $data[0];
                    $excel_client_id = $data[4];

                    $client_name = $data[2];
                    $city = $data[3];
                    $product_id = (int) $data[7];
                    $invoice_quantity = (int) $data[10];
                    $runID = number_format((float)$data[11], 2, '.', '');;
                    $discount = floatval($data[12]) * 100;
                    $lasttClient_id = '';
                    
                    if($client_name != $cclient){
                        $cclient = $client_name;
                        
                        $r++;
                        

                        if($r >1){
                            // var_dump($attachProducts);
                            // var_dump($attachProducts_gets);
                            // echo "client_pay: {$client_pay}";
                            // echo "client_id: {$client_id}";
                            // echo "<hr>{$invoice_date}";

                            // $inv_date_split = explode('/', $invoice_date);
                            $invoice_date = '2022-09-01'; //$inv_date_split[2] . '-' . $inv_date_split[0] . '-' . $inv_date_split[1];
                            // return $invoice_date;
                            $inv = [
                                'voucher_id' => $user->voucher_id,
                                'invoice_status_id' => 20,
                                'client_id' => $client_id,
                                'user_rep_id' => $user->id,
                                'invoice_date' => $invoice_date,
                                'image' => '',
                                'invoice_code' => '',
                                'invoice_details' => '',
                            ];
    
                            Invoice::createInvoice($inv, $attachProducts, 0);
                        }
                        // echo "<span style='color:blue;'>" . $client_name . "</span><br>";
                        
                        
                        if($excel_client_id){
                            $client_id = $excel_client_id;
                            
                        }else{
                            $like_clients = Client::whereIn('id', $clientIds)->where('client_name', 'LIKE', "%{$client_name}%")->get();
                            if($like_clients->count()>1){
                                $client_name2 = $data[2] . ' ' .$data[3];
                                $like_clients = Client::whereIn('id', $clientIds)->where('client_name', 'LIKE', "%{$client_name2}%")->get();
                            }
                            $client_id = $like_clients->first()->id;
                            // $invoice_date = $data[0];
                            // echo "$invoice_date <br>";
                            if($like_clients->count() !== 1){
                                echo "<br> <hr> %$z%#".$k++."-{$like_clients->count()}# {$r} <span style='color:blue;'>" . $client_name . "</span> : {$city}<br>";
                                foreach ($like_clients as $lc) {
                                    echo $lc->client_name . ", " . $like_clients->count();
                                }
                            }
                            // $cc =  ViewClient::whereIn('region_id', $region_ids)->where('id', $client_id)->first();
                            // if($cc->state != 'سوهاج'){
                            //     return "Nooooooooooooooooooooooooooooooooo";
                            // }
                            // echo $cc->client_name . "____ ".$cc->state."<br>";
                            if($clients->where('id', $client_id)->first()->count()<0){
                                return $client_id;
                            }
                        }
                        // echo Client::find($client_id)->view_client->city . "<br>";

                        
                        $userPay +=$client_pay;
                        $attachProducts = [];
                        $attachProducts_gets = [];
                        $client_pay = 0;

                        
                    }
                    // echo "<br>{$client_name} => {$cclient} <br>";

                    // echo "<br> = Public_Price($product_id, $runID);";
                    $publicPrice = Public_Price($product_id, $runID);
                    // echo "<br> $publicPrice = Public_Price($product_id, $runID);";
                    array_push($attachProducts,[
                        'product_id' => $product_id,
                        'runID' => $runID,
                        'invoice_quantity' => $invoice_quantity,
                        'invoice_bounce' => 0,
                        'invoice_public_price' =>  $publicPrice,
                        'discount' => $discount,
                    ]);
                    
                    array_push($attachProducts_gets,[
                        'product_id' => $product_id,
                        'runID' => $runID,
                        'get_quantity'=> $invoice_quantity,
                    ]);
                    // echo "<br> $publicPrice * (100-$discount)/100 * $invoice_quantity";
                    $client_pay += $publicPrice * (100-$discount)/100 * $invoice_quantity;

                }
                $isFirst = false;
            }

            // var_dump($attachProducts);
            // var_dump($attachProducts_gets);
            // echo "client_pay: {$client_pay}";
            // echo "client_id: {$client_id}";
            // echo "<hr>";

            // $inv_date_split = explode('/', $invoice_date);
            $invoice_date = '2022-9-01';
            // return $invoice_date;
            $inv = [
                'voucher_id' => $user->voucher_id,
                'invoice_status_id' => 20,
                'client_id' => $client_id,
                'user_rep_id' => $user->id,
                'invoice_date' => $invoice_date,
                'image' => '',
                'invoice_code' => '',
                'invoice_details' => '',
            ];
            // $cc =  ViewClient::whereIn('region_id', $region_ids)->where('id', $client_id)->first();
            // if($cc->state != 'سوهاج'){
            //     return "Nooooooooooooooooooooooooooooooooo";
            // }
            // echo $cc->client_name . "____ ".$cc->state."<br>";
            $userPay +=$client_pay;
            Invoice::createInvoice($inv, $attachProducts, 0);
        }

        DB::commit();
        return "ok: ". $userPay;
        fclose($CSVfp);
    }
}
