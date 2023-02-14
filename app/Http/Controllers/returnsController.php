<?php
namespace App\Http\Controllers;

use App\Http\Requests\getRequest;
use App\Http\Requests\ReturnRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Invoice;
use App\Models\Returns;
use App\Models\Client;
use App\Models\InvoicePayType;
use App\Models\Transfer;
use App\Models\ViewInvoiceProduct;
use App\Models\ViewReturnProduct;
use App\Models\ViewReturns;
use App\Models\VoucherProduct;
use App\Models\VoucherReturn;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class returnsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $returns = ViewReturns::orderBy('id', 'DESC')->get();
        // return $returns;
        return view('dashboard.returns.index', compact(['returns']));
    }

    public function yajrareturns()
    {
       $returns = ViewReturns::orderBy('id', 'DESC')->get();

        return DataTables::of($returns)
        ->editColumn('created_at', function($returns) {
            $html = Carbon::createFromFormat('Y-m-d H:i:s', $returns->created_at)->format('H:i:s Y-m-d');
            return new HtmlString($html);
        })
        ->editColumn('invoice_code', function($returns) {
            $html = '<a href="'. route("invoices.show", $returns->invoice_id) .'">' .$returns->invoice_code. '</a>';
            return new HtmlString($html);
        })
        ->addColumn('actions',
            function($returns) {
                $html ='<span class="actionLinks">';
                $html .= indexView($returns, 'returns');
                $html .= '</span>';
                return new HtmlString($html);
            }
        )
        ->make(true);
    }

    public function indexReturnProducts()
    {
        $returns = ViewReturnProduct::orderBy('created_at', 'DESC')->orderBy('client_name', 'DESC')->get();
        // return $returns;
        return view('dashboard.returns.indexReturnProducts', compact(['returns']));
    }

    public function yajraindexReturnProducts()
    {
        $returns = ViewReturnProduct::where('invoice_product_id', '>', 0)->orderBy('created_at', 'DESC')->orderBy('client_name', 'DESC')->get();

        return DataTables::of($returns)
        ->editColumn('created_at', function($returns) {
            $html = Carbon::createFromFormat('Y-m-d H:i:s', $returns->created_at)->format('H:i:s Y-m-d');
            return new HtmlString($html);
        })
        ->editColumn('invoice_code', function($returns) {
            $html = '<a href="'. route("invoices.show", $returns->invoice_id) .'">' .$returns->invoice_code. '</a>';
            return new HtmlString($html);
        })
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($return)
    {
        $return = ViewReturns::findOrFail($return);
        // return $return;
        return view('dashboard.returns.show', compact(['return']));
    }

    public function create(Invoice $invoice)
    {
        if(Auth::user()->voucher_id && Auth::user()->voucher->voucher_status != 3){
            $notification = notification('عذرا، لا يمكنك عمل مرتجع وانت لديك اذن صرف مفتوح حالته غير خرج من المخزن', false);
            return back()->withInput()->with($notification);
        }
        if($invoice->view_invoice->get_nexts<=0 && $invoice->view_invoice->invoice_bounce_net<=0){
            $notification = notification('لا يوجد كميات قابلة للإرتجاع في هذه الفاتورة', false);
            return back()->withInput()->with($notification);
        }

        if($invoice->status->id!=20){
            $notification = notification('لا يمكن عمل مرتع علي فاتورة لم يتم الموافقة عليها بدل من ذلك قم بتعديل كميات الفاتورة.!', false);
            return back()->withInput()->with($notification);
        }

        $client = Client::where(['id'=>$invoice->client_id])->first();
        if(!$client){
            $notification = notification('لا يمكن مرتجع فاتورة لعميل غير موجود!', false);
            return back()->withInput()->with($notification);
        }

        // $invoiceStore =$invoice->voucher->store;

        $products_ids = $invoice->products->pluck('id');
        $products = Product::whereIn('id', $products_ids)->get();
        return view('dashboard.returns.create', compact(['invoice', 'products']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\getRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReturnRequest $request)
    {
        if(Auth::user()->voucher_id && Auth::user()->voucher->voucher_status != 3){
            $notification = notification('عذرا، لا يمكنك عمل مرتجع وانت لديك اذن صرف مفتوح حالته غير خرج من المخزن', false);
            return back()->withInput()->with($notification);
        }
        $count = count($request->invoice_product_ids);

        if($count != count($request->return_quantitys)
        || $count != count($request->return_bounces)
        ){
            $notification = notification('من فضلك ادخل تفاصيل الاصناف في الفاتورة بشكل صحيح', false);
            return back()->withInput()->with($notification);
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        if($invoice->view_invoice->get_nexts<=0 && $invoice->view_invoice->invoice_bounce_net<=0){
            $notification = notification('لا يوجد كميات قابلة للإرتجاع في هذه الفاتورة', false);
            return back()->withInput()->with($notification);
        }

        if($invoice->status->id!=20){
            $notification = notification('لا يمكن عمل مرتع علي فاتورة لم يتم الموافقة عليها بدل من ذلك قم بتعديل كميات الفاتورة.!', false);
            return back()->withInput()->with($notification);
        }

        $client = Client::where(['id'=>$invoice->client_id])->first();
        if(!$client){
            $notification = notification('لا يمكن مرتجع فاتورة لعميل غير موجود!', false);
            return back()->withInput()->with($notification);
        }

        $error_flag=false;
        $i=0;
        $attachProducts = [];
        $attachProducts_transfer = [];
        $is_q_at_less = false;

        foreach ($request->invoice_product_ids as $invoice_product_id) {
            $invoice_product = ViewInvoiceProduct::find($invoice_product_id);
            if(!$invoice_product){
                $error_flag= true;
                break;
            }

            if(
                (is_numeric($request->return_quantitys[$i]) && $request->return_quantitys[$i]>0)
                || (is_numeric($request->return_bounces[$i]) && $request->return_bounces[$i]>0)
            ){
                $is_q_at_less = true;
                if($request->return_quantitys[$i]>$invoice_product->invoice_net_q_withoutbounce || $request->return_quantitys[$i]<0){
                    $error_flag= true;
                    break;
                }

                if($request->return_bounces[$i]>$invoice_product->invoice_bounce_net || $request->return_bounces[$i]<0){
                    $error_flag= true;
                    break;
                }

                $attachProducts[$i] = [
                    'invoice_product_id' => $invoice_product->id,
                    'return_quantity' => $request->return_quantitys[$i],
                    'return_bounce' => $request->return_bounces[$i],
                ];



                $attachProducts_transfer[$i] = [
                    'product_id'=>$invoice_product->product_id,
                    'Quantity'=> (int) $request->return_quantitys[$i] + (int) $request->return_bounces[$i],
                    'RunID'=>$invoice_product->runID,
                ];
            }
            $i++;
        }

        if(!$is_q_at_less){
            $notification = notification('لا يمكن عمل مرتجع بدون كميات', false);
            return back()->withInput()->with($notification);
        }

        $store =$invoice->voucher->store;

        // if(!Auth::user()->stores->where('id', $store->id)->where('is_active', 1)->first()){
        //     $notification = array(
        //         'message' => 'ليس لديك صلاحيات علي هذا المخزن!',
        //         'alert-type' => 'error',
        //         'error' => 'ليس لديك صلاحيات علي هذا المخزن!',
        //     );
        //     return back()->withInput()->with($notification);
        // }
        $msg = "";

        if(Auth::user()->voucher_id && $request->to_store_id != Auth::user()->voucher->store->id){
            $notification = notification('خطأ، عذرا ان تحاول عمل مرتجع علي مخزن وانت لديك اذن صرف مفتوح علي مخزن أخر، من فضلك اعمل المرتجع علي مخزن اذن الصرف المفتوح!!', false);
            return back()->withInput()->with($notification);
        }

        $to_store_id = $request->to_store_id;
        if(
            $request->to_store_id == $invoice->voucher->store->id && 
            Auth::user()->stores->where('id',$request->to_store_id)->where('is_active', 1)->first()
        ){
            $to_store_id = NULL;
        }else if( !Auth::user()->stores->where('id',$request->to_store_id)->where('is_active', 1)->first() ){
            $notification = array(
                'message' => 'ليس لديك صلاحيات علي هذا المخزن لعمل مرتجع علية!!',
                'alert-type' => 'error',
                'error' => 'ليس لديك صلاحيات علي هذا المخزن لعمل مرتجع علية!!',
            );
            return back()->withInput()->with($notification);
        }
        



        DB::beginTransaction();

        /*-- create transfer -----------------------------------------------*/
        // return $request;
        $transfer= NULL;
        $transfer_id=NULL;
        $voucher_id=NULL;
        if($to_store_id){
            $transfer_code = NULL;
            do{
                $transfer_code = tb_code();
            }while(Transfer::where('transfer_code', $transfer_code)->count()!=0);

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
            
            $transfer_id=$transfer->id;
            $voucher_id=$invoice->voucher->id;
        }
        /*-- /create transfer -----------------------------------------------*/
        
        $return_code = $request->return_code;
        if(!$request->return_code){
            do{
                $return_code = tb_code();
            }while(Returns::where('return_code', $return_code)->count()!=0);
        }

        
        // try {
            $return = Returns::create([
                'invoice_id'=>$invoice->id,
                'return_date'=>$request->return_date,
                'return_code'=>$return_code,
                'to_store_id'=>$to_store_id,
                'transfer_id'=>$transfer_id,
                'voucher_id'=>$voucher_id,
                'user_rep_id'=>Auth::id(),
            ]);

            

            $return_id = $return->id;

            $return->returnProductsSync()->sync($attachProducts);

            if(Auth::user()->voucher_id && $invoice->voucher_id != Auth::user()->voucher_id){
                $voucher_current = Auth::user()->voucher;
                $quantities = $request->return_quantitys;
                $return_bounces = $request->return_bounces;
                $i=0;
                foreach($request->invoice_product_ids as $invoice_product_id){
                    if(is_numeric($invoice_product_id) && array_key_exists($i, $quantities) && array_key_exists($i, $return_bounces)){
                        $quantity =$quantities[$i]+$return_bounces[$i];

                        if($quantity && is_numeric($quantity)){
                            $invoice_product = ViewInvoiceProduct::findOrFail($invoice_product_id);
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
                    }
                    $i++;
                }


                $msg = "تم إضافة المرتجع إلي كميات  إذن الصرف المفتوح لك.";

            }else if(!Auth::user()->voucher_id){
                $i=0;

                $attachProducts = [];
                $quantities = $request->return_quantitys;
                $return_bounces = $request->return_bounces;

                $flagReturn = false;
                foreach($request->invoice_product_ids as $invoice_product_id){
                    if(is_numeric($invoice_product_id) && array_key_exists($i, $quantities) && array_key_exists($i, $return_bounces)){
                        $quantity =$quantities[$i]+$return_bounces[$i];
                        $invoice_product = ViewInvoiceProduct::findOrFail($invoice_product_id);

                        if($quantity && is_numeric($quantity)){
                            $attachProducts[$i] = [
                                'product_id'=>$invoice_product->product_id,
                                'runID'=>$invoice_product->runID,
                                'voucher_quantity'=>$quantity,
                            ];
                        }
                    }else $flagReturn = true;

                    if($flagReturn){
                        $notification = array(
                            'message' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                            'alert-type' => 'error',
                            'error' => 'يجب إدخال الأصناف والكميات بشكل صحيح',
                        );
                        return back()->withInput()->with($notification);
                    }
                    $i++;
                }
                $voucher_code = rand(1,999999);
                do{
                    $voucher_code = rand(1,999999);
                    $isVoucher = Voucher::where(['voucher_code'=>$voucher_code])->first();
                }while($isVoucher);

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

                $msg ="المرتجع يجب ان يتم علي اذن صرف، لم يكن لك اذن اصرف مفتوح-ولذا تم انشاء إذن لك وعمل المرتجع عليه";
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

            DB::commit();

            $notification = notification('تم عمل المرتجع بنجاح :'.$msg);
            return redirect()->route('invoices.show', [$invoice->id])->with($notification);
        // } catch (\Throwable $th) {
        //     $notification = notification('#102 حدث خطأ أثناء الحفظ، حاول مجددا أو تواصل مع الإدارة. .!', false);
        //     return back()->withInput()->with($notification);
        // }

    }
}
