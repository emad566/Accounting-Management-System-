<?php

namespace App\Http\Controllers;

use App\Http\Requests\getRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Invoice;
use App\Models\Get;
use App\Models\Client;
use App\Models\Clientarea;
use App\Models\Generalpolicy;
use App\Models\InvoicePayType;
use App\Models\ViewInvoiceProduct;
use App\Models\GetProduct;
use App\Models\Region;
use App\Models\Returns;
use App\Models\Transfer;
use App\Models\ViewInvoice;
use App\Models\VoucherProduct;
use App\Models\VoucherReturn;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class getsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gets = Get::orderBy('id', 'DESC')->get();
        return view('dashboard.gets.index', compact(['gets']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Invoice $invoice, $payType=30)
    {
        return redirect()->route('gets.newget');

        if($invoice->view_invoice->get_nexts<=0){
            $notification = array(
                'message' => 'هذه الفاتورة محصلة بالكامل ولا تحتاج لتحصيل',
                'alert-type' => 'error',
                'error' => 'هذه الفاتورة محصلة بالكامل ولا تحتاج لتحصيل',
            );
            return back()->withInput()->with($notification);
        }

        if($invoice->status->id!=20){
            $notification = array(
                'message' => 'لا يمكن تحصيل فاتورة لم يتم الموافقة عليها.!',
                'alert-type' => 'error',
                'error' => 'لا يمكن تحصيل فاتورة لم يتم الموافقة عليها.!',
            );
            return back()->withInput()->with($notification);
        }

        $client = Client::where(['id'=>$invoice->client_id])->first();
        if(!$client){
            $notification = notification('لا يمكن تحصيل فاتورة لعميل غير موجود!', false);
            return back()->withInput()->with($notification);
        }

        $pay_types = InvoicePayType::where('id', '<>', 20)->get();
        // $payType = 30;
        $products_ids = $invoice->products->pluck('id');
        $products = Product::whereIn('id', $products_ids)->get();
        $generalpolicy = Generalpolicy::find(1);
        return view('dashboard.gets.create', compact(['invoice', 'pay_types', 'products', 'payType', 'generalpolicy']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\getRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(getRequest $request)
    {
        $count = count($request->invoice_product_ids);

        if($count != count($request->pay_quantitys)){
            $notification = array(
                'message' => 'من فضلك ادخل تفاصيل الاصناف في الفاتورة بشكل صحيح',
                'alert-type' => 'error',
                'error' => 'من فضلك ادخل تفاصيل الاصناف في الفاتورة بشكل صحيح',
            );
            return back()->withInput()->with($notification);
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        if($invoice->view_invoice->get_nexts<=0){
            $notification = array(
                'message' => 'هذه الفاتورة محصلة بالكامل ولا تحتاج لتحصيل',
                'alert-type' => 'error',
                'error' => 'هذه الفاتورة محصلة بالكامل ولا تحتاج لتحصيل',
            );
            return back()->withInput()->with($notification);
        }

        if($invoice->status->id!=20){
            $notification = array(
                'message' => 'لا يمكن تحصيل فاتورة لم يتم الموافقة عليها.!',
                'alert-type' => 'error',
                'error' => 'لا يمكن تحصيل فاتورة لم يتم الموافقة عليها.!',
            );
            return back()->withInput()->with($notification);
        }

        $client = Client::where(['id'=>$invoice->client_id])->first();
        if(!$client){
            $notification = notification('لا يمكن تحصيل فاتورة لعميل غير موجود!', false);
            return back()->withInput()->with($notification);
        }

        $error_flag=false;
        $i=0;
        $totalPaid = 0;
        $attachProducts = [];
        $is_q_at_less = false;
        foreach ($request->invoice_product_ids as $invoice_product_id) {
            $invoice_product = ViewInvoiceProduct::find($invoice_product_id);
            if(!$invoice_product){
                $error_flag= true;
                break;
            }

            if(is_numeric($request->pay_quantitys[$i]) && $request->pay_quantitys[$i]>0){
                $is_q_at_less = true;
                if($request->pay_quantitys[$i]>$invoice_product->get_quantity_next || $request->pay_quantitys[$i]<0){
                    $error_flag= true;
                    break;
                }

                $totalPaid += round($invoice_product->invoice_public_price*(100-$invoice_product->discount) * $request->pay_quantitys[$i] / 100, 2);
                $attachProducts[$i] = [
                    'invoice_product_id' => $invoice_product->id,
                    'get_quantity' => $request->pay_quantitys[$i],
                ];
            }
            $i++;
        }

        if(!$is_q_at_less){
            $notification = notification('لا يمكن تحصيل فاتورة بدون كميات', false);
            return back()->withInput()->with($notification);
        }



        $get_overPrice_sum = $client->view_client->get_overPrice_sum;
        $paid_from_client_balance = ($get_overPrice_sum > $totalPaid)? $totalPaid : $get_overPrice_sum;


        $get_overPrice = $request->client_pay - ($totalPaid - $paid_from_client_balance);

        if($get_overPrice < -4.99){
            return $paid_from_client_balance;
            $notification = notification('فرق الحساب الذي يضاف الي حساب العميل يجب ان لا يقل علي عن -4.99 (القيمة بالسالب).!', false);
            return back()->withInput()->with($notification);
        }

        DB::beginTransaction();
        $get_code = $request->get_code;
        if(!$request->get_code){
            do{
                $get_code = tb_code();
            }while(Get::where('get_code', $get_code)->count()!=0);
        }
        try {
            $get = Get::create([
                'invoice_id'=>$invoice->id,
                'get_date'=>$request->get_date,
                'get_code'=>$get_code,
                'user_rep_id'=>Auth::id(),
                'get_overPrice'=> $get_overPrice,
                'client_pay'=>$request->client_pay,
                'paid_from_client_balance'=>$paid_from_client_balance,
            ]);

            $get->GetProducts()->sync($attachProducts);

            DB::commit();

            $notification = notification('تم التحصيل بنجاح');
            return redirect()->route('invoices.show', [$invoice->id])->with($notification);
        } catch (\Throwable $th) {
            $notification = notification('#102 حدث خطأ أثناء الحفظ، حاول مجددا أو تواصل مع الإدارة. .!','#102 حدث خطأ أثناء الحفظ، حاول مجددا أو تواصل مع الإدارة. .!', false);
            return back()->withInput()->with($notification);
        }

    }

    public function delete($get_id)
    {
        $get = Get::findOrFail($get_id);

        $invoice = $get->invoice;

        $get->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('invoices.show', $invoice->id)->with($notification);
    }
    
    public function deleteget(Request $request)
    {
        $getProduct = GetProduct::where('get_id', $request->get_id)->where('invoice_product_id', $request->invoice_product_id)->first();
        $invoice =$getProduct->get->invoice;
        if($getProduct && $getProduct->get_quantity >= $request->delGetQuantity  ){
            $delPrice = $request->delGetQuantity *  $getProduct->invoice_product->invoice_public_price * ((100 - $getProduct->invoice_product->discount)/100);
            if($getProduct->get->client_pay>=$delPrice ){
                if(($getProduct->get->client_pay + $getProduct->get->paid_from_client_balance - $delPrice)>$getProduct->get->get_overPrice ){
                    DB::beginTransaction();
                    $getProduct->get->update(['client_pay'=> $getProduct->get->client_pay - $delPrice ]);
                    $getProduct->update(['get_quantity'=> $getProduct->get_quantity - $request->delGetQuantity ]);
                    DB::commit();

                    $notification = array(
                        'message' => 'تم حذف تحصيل '. $request->delGetQuantity . 'علبه بملغ' . $delPrice,
                        'alert-type' => 'success',
                        'success' => 'تم حذف تحصيل '. $request->delGetQuantity . 'علبه بملغ' . $delPrice,
                    );
                }else{
                    $notification = array(
                    'message' => 'هذا الحذف للتحصيل سيؤثر علي محفظة العميل، الأولي قم بحذف التحصيل بالكامل!',
                    'alert-type' => 'error',
                    'success' => 'هذا الحذف للتحصيل سيؤثر علي محفظة العميل، الأولي قم بحذف التحصيل بالكامل!',
                );
                }
                

            }else{
                $notification = array(
                    'message' => 'لا يمكن خصم تحصيل أكثر مما دفعة العميل',
                    'alert-type' => 'error',
                    'success' => 'لا يمكن خصم تحصيل أكثر مما دفعة العميل',
                );
            }

        }else{
            $notification = array(
                'message' => 'الكمية يجب ان تكون مساوية أو أقل من الكمية المحصلة!',
                'alert-type' => 'error',
                'success' => 'الكمية يجب ان تكون مساوية أو أقل من الكمية المحصلة!',
            );
        }
        
        return redirect()->route('invoices.show', $invoice->id)->with($notification);
    }

    public function newget()
    {
        $clients = [];
        $store_region_ids = Region::whereHas('stores', function($q){
            $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
        })->pluck('id');
        $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
        
        $client_ids = ViewInvoiceProduct::where('get_next', '>', 0)->pluck('client_id')->toArray();

        $clients = Clientarea::whereIn('region_id', $region_ids)->whereIn('id', $client_ids)->orderBy('client_name', 'ASC')->get();



        return view('dashboard.gets.newcreate', compact(['clients']));
    }
    
    public function newgetdata(Client $client, $json=false)
    {
        $inv_ids = $client->invoices->pluck('id')->toArray();
        $invoices = ViewInvoiceProduct::whereIn('invoice_id', $inv_ids)->where('get_next', '>', 0)->with('product:id,Product_Name')->orderBy('invoice_public_price', 'ASC')->get();
        $inv_ids = array_unique($invoices->pluck('invoice_id')->toArray());

        $inv_pro = [];
        // return $invoices;
        $next_pro_total = 0;
        foreach($invoices as $inv){
            $Product_Name = $inv->product->Product_Name;
            $ip = [
                'invoice_product_id'=>$inv->id,
                'invoice_date'=>$inv->invoice->invoice_date,
                'invoice_id'=>$inv->invoice_id,
                'product_id'=>$inv->product_id,
                'Product_Name'=>$Product_Name,
                'runID'=>$inv->runID,
                'Public_Price'=>$inv->invoice_public_price,
                'proprice'=> $Product_Name . " [" . $inv->invoice_public_price . "]",
                'discount'=>$inv->discount,
                'get_quantity_next'=>$inv->get_quantity_next,
                'return_quantity'=>0,
                'get_quantity'=>0,
                'get_pay'=>0,
                'get_pay_next'=>$inv->get_next,
            ];
            $next_pro_total += $inv->get_next;
            array_push($inv_pro, $ip);
        }
        $walit = $client->view_client->get_overPrice_sum;
        if($json=='json'){
            return response()->json(['client'=>$client, 'inv_pro'=>$inv_pro, 'next_pro_total'=>$next_pro_total, 'walit'=>$walit, 'inv_ids'=>$inv_ids]);
        }

        return  view('dashboard.gets.newgetdata', compact(['client', 'inv_pro', 'next_pro_total', 'walit']));
    }

    public function storenewget(Client $client, Request $request)
    {
        $trueMsg = '';
        DB::beginTransaction();
        /* ===============================
        ||  Start: if gets
        ================================== */
        // return  $request->all();
        if($request->getLength>0 || $request->client_paid){
            $clientPaySingleSum = 0; // Store Total single paid from client pay
            $getsLength = $request->getLength;
            
            $j = 0; // To know the last get
            // return "=======0";
            if($request->getLength>0){
                foreach ($request->gets as $inv_id => $get) {
    
                    $j++;
    
                    $invoice = Invoice::findOrFail($inv_id);
                    if($invoice->view_invoice && $invoice->view_invoice->get_nexts<=0){
                        $msg = 'هذه الفاتورة محصلة بالكامل ولا تحتاج لتحصيل';
                        return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
                    
                    if($invoice->status->id!=20){
                        $msg = 'لا يمكن تحصيل فاتورة لم يتم الموافقة عليها.!';
                        return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
                    
                    $client = Client::where(['id'=>$invoice->client_id])->first();
                    if(!$client){
                        $msg = 'لا يمكن تحصيل فاتورة لعميل غير موجود!';
                        return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
                    
                    
                    $error_flag=false;
                    $i=0;
                    $totalPaid = 0;
                    $attachProducts = [];
                    $is_q_at_less = false;
                    foreach ($get as $invpro) {
                        // convvert array to object
                        $invpro = json_decode(json_encode($invpro));
    
                        $invoice_product = ViewInvoiceProduct::where('id', $invpro->invoice_product_id)->first();
                        if(!$invoice_product){
                            $msg = 'عذرا لا يوجد منتج لتحصيله';
                            return $this->sendResponse(false, $request->all(), $msg , 200);
                        }
    
                        
                        if(is_numeric($invpro->get_quantity) && $invpro->get_quantity>0){
                            $is_q_at_less = true;
                            if($invpro->get_quantity>$invoice_product->get_quantity_next){
                                $error_flag= true;
                                break;
                            }
    
                            $totalPaid += round($invoice_product->invoice_public_price*(100-$invoice_product->discount) * $invpro->get_quantity / 100, 2);
                            $attachProducts[$i] = [
                                'invoice_product_id' => $invoice_product->id,
                                'get_quantity' => $invpro->get_quantity,
                            ];
                        }
                        $i++;
                    }
                    
                    if(!$is_q_at_less && !$request->client_paid){
                        $msg = 'لا يمكن تحصيل فاتورة بدون كميات';
                        return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
                    
                    
                    $get_overPrice_sum = $client->view_client->get_overPrice_sum;
                    $paid_from_client_balance = ($get_overPrice_sum > $totalPaid)? $totalPaid : $get_overPrice_sum;
                    
                    $clientPaySingle = ($get_overPrice_sum > $totalPaid)? 0 : $totalPaid - $get_overPrice_sum;
                    $clientPaySingleSum += $clientPaySingle;
                    
                    $get_overPrice = $clientPaySingle  - ($totalPaid - $paid_from_client_balance);
                    // return $getsLength;
                    if($getsLength == $j){
                        if($request->client_paid - $clientPaySingleSum < 0 && $request->is_client_pay){
                            // $msg = 'خطأ: يجب ان يكون مجموع  المحفظة + قيمة الدفع أكبر من أو يساوي المطلوب للسداد';
                            // return $this->sendResponse(false, $request->all(), $msg , 200);
                        }
                        $get_overPrice += $request->client_paid - $clientPaySingleSum;
                    }
                    
                    if($get_overPrice < 0){
                        // $msg = 'لا يمكن يمكن اضافة رصيد بالسالب لمحفظة العميل في هذا النوع من التحصيل: ' . $get_overPrice ; 
                        // return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
                    
                    
                    
                    
                    $lastGet = Get::orderBy('id', 'desc')->first();
                    $lastGet_id = ($lastGet)? $lastGet->id : 1;
                    
                    if(Get::where('get_code', $lastGet_id)->count()>0){
                        do{
                            $lastGet_id +=1;
                        }while(Get::where('get_code', $lastGet_id)->count()!=0);
                    }
                    
                    try {
                        // Start Creat single get Database
                        // return "E11";
                        $get = Get::create([
                            'invoice_id'=> $invoice->id,
                            'get_date'=> $request->insert_date,
                            'get_code'=> $lastGet_id,
                            'user_rep_id'=> Auth::id(),
                            'get_overPrice'=> $get_overPrice,
                            'client_pay'=> $clientPaySingle+$get_overPrice,
                            'paid_from_client_balance'=> $paid_from_client_balance,
                        ]);
                        $get->GetProducts()->sync($attachProducts);
                        // End Creat single get Database
                    } catch (\Throwable $th) {
                        $msg = 'حدث خطأ أثناء حفظ التحصيل ، حاول مجددا أو تواصل مع الإدارة. .!';
                        return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
                }
            }else if($request->client_paid> 0 && $request->client_paid < 200){
                $invget = ViewInvoice::where('client_id', $request->client_id)->where('get_nexts', '>', 0)->orderBy('get_nexts', 'DESC')->first();
                
                $lastGet = Get::orderBy('id', 'desc')->first();
                $lastGet_id = ($lastGet)? $lastGet->id : 1;
                
                if(Get::where('get_code', $lastGet_id)->count()>0){
                    do{
                        $lastGet_id +=1;
                    }while(Get::where('get_code', $lastGet_id)->count()!=0);
                }

                $get = Get::create([
                    'invoice_id'=> $invget->id,
                    'get_date'=> $request->insert_date,
                    'get_code'=> $lastGet_id,
                    'user_rep_id'=> Auth::id(),
                    'get_overPrice'=> $request->client_paid,
                    'client_pay'=> $request->client_paid,
                    'paid_from_client_balance'=> 0,
                ]);

            }else{
                $msg = 'لا يمكن وضع رصيد في محفظة العميل أكبر من 200 دفعة واحدة ولا اقل من صفر';
                return $this->sendResponse(false, $request->all(), $msg , 200);
            }
            
            

            $trueMsg .= 'تم التحصيل بنجاح, ' ;
        }


        /* End: if gets */ 

        /* ===============================
        ||  Start: Store Returns
        ================================== */
        if($request->returnLength>0){
            foreach ($request->returns as $inv_id => $return) {
                /* ===============================
                ||  Start: Check return Validations
                ================================== */
                if(Auth::user()->voucher_id && Auth::user()->voucher->voucher_status != 3){
                    $msg = 'عذرا، لا يمكنك عمل مرتجع وانت لديك اذن صرف مفتوح حالته غير خرج من المخزن';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
        
                $invoice = Invoice::findOrFail($inv_id);
        
                if($invoice->view_invoice->get_nexts<=0){
                    $msg = 'لا يوجد كميات قابلة للإرتجاع في هذه الفاتورة';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
        
                if($invoice->status->id!=20){
                    $msg = 'لا يمكن عمل مرتع علي فاتورة لم يتم الموافقة عليها بدل من ذلك قم بتعديل كميات الفاتورة.!';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
        
                $client = Client::where(['id'=>$invoice->client_id])->first();
                if(!$client){
                    $msg = 'لا يمكن مرتجع فاتورة لعميل غير موجود!';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
                /* End: Check return Validations */ 
        
                /* ===============================
                ||  Start: Prepare create return array
                ================================== */
                $i=0;
                $attachProducts = [];
                $attachProducts_transfer = [];
                $is_q_at_less = false;
                foreach ($return as $invpro) {
                    $invpro = json_decode(json_encode($invpro));
                    $invoice_product_id = $invpro->invoice_product_id;

                    $invoice_product = ViewInvoiceProduct::where('id', $invoice_product_id)->first();
                    if(!$invoice_product){
                        $msg = 'هذا المنتج لا يوجد منها كميات أجلة للعميل';
                        return $this->sendResponse(false, $request->all(), $msg , 200);
                    }
        
                    if(is_numeric($invpro->return_quantity) && $invpro->return_quantity>0)
                    {
                        $is_q_at_less = true;
                        if($invpro->return_quantity>$invoice_product->invoice_net_q_withoutbounce || $invpro->return_quantity<0){
                            $msg = 'عذرا: المرتجع يجب أن لا يكون اكبر من الأجل';
                            return $this->sendResponse(false, $request->all(), $msg , 200);
                        }

                        $attachProducts[$i] = [
                            'invoice_product_id' => $invoice_product->id,
                            'return_quantity' => $invpro->return_quantity,
                            'return_bounce' => 0,
                        ];
                        
                        $attachProducts_transfer[$i] = [
                            'product_id'=>$invoice_product->product_id,
                            'Quantity'=> (int) $invpro->return_quantity,
                            'RunID'=>$invoice_product->runID,
                        ];
                    }
                    $i++;
                }

                if(!$is_q_at_less){
                    $msg = 'لا يمكن عمل مرتجع بدون كميات';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
                /* End: Prepare create return array */ 
                
        
                /* ===============================
                || Start: If return to different store
                || Create a transfer to the the differnt store
                ================================== */
                $store = $invoice->voucher->store;
        
                if(Auth::user()->voucher_id && $request->to_store_id != Auth::user()->voucher->store->id){
                    $msg = 'خطأ، عذرا انت تحاول عمل مرتجع علي مخزن وانت لديك اذن صرف مفتوح علي مخزن أخر، من فضلك اعمل المرتجع علي مخزن اذن الصرف المفتوح!!';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
        
                $to_store_id = $request->to_store_id;
                if( $request->to_store_id == $invoice->voucher->store->id && 
                    Auth::user()->stores->where('id',$request->to_store_id)->where('is_active', 1)->first()
                ){
                    $to_store_id = NULL;
                }else if( !Auth::user()->stores->where('id',$request->to_store_id)->where('is_active', 1)->first() ){
                    $msg = 'ليس لديك صلاحيات علي هذا المخزن لعمل مرتجع علية!!';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }

                /*-- create transfer -----------------------------------------------*/
                $transfer= NULL;
                $transfer_id=NULL;
                $voucher_id=NULL;
                if($to_store_id){

                    $lastTransfer = Transfer::orderBy('id', 'desc')->first();
                    $lastTransfer_id = ($lastTransfer)? $lastTransfer->id : 1;
                    if(Transfer::where('transfer_code', $lastTransfer_id)->count()>0){
                        do{
                            $lastTransfer_id +=1;
                        }while(Transfer::where('Transfer_code', $lastTransfer_id)->count()!=0);
                    }
                    $transfer_code = $lastTransfer_id;

                    $transfer = Transfer::create([
                        'transfer_code' => $transfer_code,
                        'transfer_status_id' => 40,
                        'transfer_name' => Auth::id(),
                        'transfer_date' => Carbon::now()->isoFormat('YYYY-MM-DD'),
                        'transfer_details' => 'امر تحويل تلقائي بسبب مرتجع فاتورة علي مخزن غير الذي تمت منه',
                        'from_store_id' => $invoice->voucher->store->id,
                        'to_store_id' => $to_store_id,
                        'user_id' => Auth::id(),
                    ]);
                    
                    $transfer->products()->sync($attachProducts_transfer);
                    
                    $transfer_id = $transfer->id;
                    $voucher_id = $invoice->voucher->id;
                }
                /*-- /create transfer -----------------------------------------------*/
                /* End: If return to different store */ 

                /* ===============================
                ||  Start: Create the retrun
                ================================== */
                $lastReturn = Returns::orderBy('id', 'desc')->first();
                $lastReturn_id = ($lastReturn)? $lastReturn->id : 1;
                if(Returns::where('return_code', $lastReturn_id)->count()>0){
                    do{
                        $lastReturn_id +=1;
                    }while(Returns::where('return_code', $lastReturn_id)->count()!=0);
                }
                $return_code = $lastReturn_id; 
                
                try {
                    $returnNew = Returns::create([
                        'invoice_id'=>$invoice->id,
                        'return_date'=>$request->insert_date,
                        'return_code'=>$return_code,
                        'to_store_id'=>$to_store_id,
                        'transfer_id'=>$transfer_id,
                        'voucher_id'=>$voucher_id,
                        'user_rep_id'=>Auth::id(),
                    ]);
                    
                    $return_id = $returnNew->id;
                    $returnNew->returnProductsSync()->sync($attachProducts);
                    
                    if(Auth::user()->voucher_id && $invoice->voucher_id != Auth::user()->voucher_id){
                        
                        
                        $i=0;
                        foreach ($return as $invpro) {
                            $invpro = json_decode(json_encode($invpro));
                            $invoice_product_id = $invpro->invoice_product_id;

                            $quantity = $invpro->return_quantity;

                            if($quantity && is_numeric($quantity)){
                                $invoice_product = ViewInvoiceProduct::where('id', $invoice_product_id)->first();

                                $oldVoucher = VoucherProduct::where([
                                    'voucher_id'=>Auth::user()->voucher_id,
                                    'product_id'=>$invoice_product->product_id,
                                    'runID'=>$invoice_product->runID,
                                ])->first();
    
                                if($oldVoucher){
                                    $oldVoucher->update([
                                        'voucher_quantity'=>$oldVoucher->voucher_quantity + $quantity,
                                    ]);
                                }else{
                                    $newVoucherProduct = VoucherProduct::create([
                                        'voucher_id'=>Auth::user()->voucher_id,
                                        'product_id'=>$invoice_product->product_id,
                                        'runID'=>$invoice_product->runID,
                                        'voucher_quantity'=>$quantity,
                                    ]);
                                }
    
                            }

                            $i++;
                        }
                        $trueMsg .= " تم إضافة المرتجع إلي كميات  إذن الصرف المفتوح لك. ";
        
                    }else if(!Auth::user()->voucher_id){
                        $i=0;
        
                        $attachProducts = [];
                        foreach ($return as $invpro) {
                            $invpro = json_decode(json_encode($invpro));

                            $invoice_product_id = $invpro->invoice_product_id;

                            $quantity = $invpro->return_quantity;
                            $invoice_product = ViewInvoiceProduct::where('id', $invoice_product_id)->first();
                            $attachProducts[$i] = [
                                'product_id'=>$invoice_product->product_id,
                                'runID'=>$invoice_product->runID,
                                'voucher_quantity'=>$quantity,
                            ];
                            $i++;
                        }

                        
                        
                        $lastVoucher = Voucher::orderBy('id', 'desc')->first();
                        $lastVoucher_id = ($lastVoucher)? $lastVoucher->id : 1;
                        if(Voucher::where('Voucher_code', $lastVoucher_id)->count()>0){
                            do{
                                $lastVoucher_id +=1;
                            }while(Voucher::where('Voucher_code', $lastVoucher_id)->count()!=0);
                        }
                        $voucher_code = $lastVoucher_id;
        
                        // return $attachProducts;
                        $newVoucher = [
                            'store_id'=> ($transfer) ? $to_store_id: $store->id,
                            'user_rep_id'=>Auth::id(),
                            'user_accountant_id'=>Auth::id(),
                            'user_keeper_id'=>Auth::id(),
                            'voucher_status'=>3,
                            'voucher_code'=>$voucher_code,
                            'voucher_details'=>'',
                            'voucher_date'=>Carbon::now(),
                            'settlement_request_id'=>Auth::id(),
                        ];

                        $newVoucher = Voucher::create($newVoucher);
                        $newVoucher->products()->sync($attachProducts);
                        Auth::user()->update(['voucher_id'=>$newVoucher->id]);
        
                        $trueMsg .=" المرتجع يجب ان يتم علي اذن صرف، لم يكن لك اذن اصرف مفتوح-ولذا تم انشاء إذن لك وعمل المرتجع عليه, ";
                    }
        
                    VoucherReturn::create([
                        'voucher_id'=>Auth::user()->voucher_id,
                        'return_id'=>$return_id
                    ]);
        
                    if($transfer){
                        foreach ($attachProducts_transfer as $pro) {
                            $item = VoucherProduct::where([
                                'voucher_id'=>$invoice->voucher->id,
                                'product_id'=>$pro['product_id'],
                                'runID'=>$pro['RunID']
                            ]);
        
                             $item->update(['voucher_quantity'=> $item->first()->voucher_quantity - (int) $pro['Quantity'] ]);
                            
                        }
                    }
        
                } catch (\Throwable $th) {
                    $msg = '#103 حدث خطأ أثناء الحفظ، حاول مجددا أو تواصل مع الإدارة. .!';
                    return $this->sendResponse(false, $request->all(), $msg , 200);
                }
                /* End: Create the retrun */ 

                
            }
            $trueMsg .= ' تم عمل المرتجعات, ';
        }
        /* End: Store Returns */ 

        DB::commit();
        return $this->sendResponse(true, $request->all(), $trueMsg , 200);
    }

    public function zeroWalit(Client $client)
    {
        
        $get_overPrice_sum = $client->view_client->get_overPrice_sum;
        if($get_overPrice_sum != 0){
            $invoice = Invoice::where('client_id', $client->id)->orderBy('id', 'DESC')->first();
            $get = Get::where('invoice_id', $invoice->id)->orderBy('id', 'ASC')->first();
            // return $get;
            $get_id = $get->id;
            // return $get_overPrice_sum;
            // return [
            //     'client_pay' => $get->client_pay - $get_overPrice_sum, 
            //     'get_overPrice' => $get->get_overPrice - $get_overPrice_sum, 
            // ];
            // return $get;
            $get->update([
                'client_pay' => $get->client_pay - $get_overPrice_sum, 
                'get_overPrice' => $get->get_overPrice - $get_overPrice_sum, 
            ]);
            return Get::find($get_id);
        }
        
    }
    
    // public function zeroWalitAll(Client $client)
    // {
    //     $clients = Client::all();
    //     foreach ($client as $client) {
    //         $view_client =ViewClient::where('id', $client->id)->first();
    //         if($view_client){
    //             $get_overPrice_sum = $view_client->get_overPrice_sum;
    //             if($get_overPrice_sum < 0){
    //                 $invoice = Invoice::where('client_id', $client->id)->orderBy('id', 'DESC')->first();
    //                 $get = Get::where('invoice_id', $invoice->id)->orderBy('id', 'ASC')->first();
    //                 // return $get;
    //                 $get_id = $get->id;
    //                 // return $get_overPrice_sum;
    //                 // return [
    //                 //     'client_pay' => $get->client_pay - $get_overPrice_sum, 
    //                 //     'get_overPrice' => $get->get_overPrice - $get_overPrice_sum, 
    //                 // ];
    //                 // return $get;
    //                 $get->update([
    //                     'client_pay' => $get->client_pay - $get_overPrice_sum, 
    //                     'get_overPrice' => $get->get_overPrice - $get_overPrice_sum, 
    //                 ]);
    //                 // return Get::find($get_id);
    //             }
    //         }
            
    //     }
    // }

    public function newgetinvoice(Request $request, $invoice_id = false){
        $invid = ($invoice_id)? $invoice_id : $request->invid;
        $invoice = Invoice::find($invid);

        $trueMsg = 'تم الاستعلام بنجاح';
        $is_get = true;
        return $this->sendResponse(true, [
            'strHTML' =>  view('dashboard.invoices.showModel', compact(['invoice' , 'is_get']))->render()
        ], $trueMsg , 200);
    }
}
