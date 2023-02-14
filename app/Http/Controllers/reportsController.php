<?php
namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cat;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Get;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Region;
use App\Models\InvoicePayStatus;
use App\Models\Returns;
use App\Models\Spend;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\UserStore;
use App\Models\ViewCheckQuantities;
use App\Models\ViewCheckQuantitiesGroup;
use App\Models\ViewClient;
use App\Models\viewGetsReport;
use App\Models\ViewPermission;
use App\Models\ViewReportBank;
use App\Models\ViewReportClientBalance;
use App\Models\ViewReportInvoiceGet;
use App\Models\ViewSpend;
use App\Models\ViewStock_Sql_Storestocks_Diff;
use App\Models\ViewStockClosed;
use App\Models\ViewTransfer;
use App\Models\ViewUsersReport;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use DB;

class reportsController  extends BaseController
{
    public function usergets(Request $request, $user_id="")
    {
        $time = microtime(true);
        $firstOfMonth = Carbon::parse(Carbon::now())->firstOfMonth()->isoFormat('YYYY-MM-DD');
        $endOfMonth = Carbon::parse(Carbon::now())->endOfMonth()->isoFormat('YYYY-MM-DD');

        $search_period = ($request && $request->from_search_date && $request->to_search_date )?
                                 ['from_search_date'=>$request->from_search_date , 'to_search_date'=>$request->to_search_date] 
                                 : ['from_search_date'=>$firstOfMonth , 'to_search_date'=>$endOfMonth];

        $user_id =($user_id && Auth::user()->can(['Shaw_all_safemoney']))? $user_id : Auth::user()->id;
        $user = User::find($user_id);
        if(!$user) return "#1020: عذرا هذا العضو غير موجود أو تم حزفه";

        $q_response = [];

        $q_response['gets'] = $user->gets->where('get_date', '>=', $search_period['from_search_date'])->where('get_date', '<=', $search_period['to_search_date'])->sum('client_pay');
        $q_response['transactions'] = Transaction::where('from_user_id', $user->id)->where('transaction_date', '>=', $search_period['from_search_date'])->where('transaction_date', '<=', $search_period['to_search_date'])->sum('amount');
        $q_response['spends'] = Spend::where('spend_user_id', $user->id)->where('spend_date', '>=', $search_period['from_search_date'])->where('spend_date', '<=', $search_period['to_search_date'])->sum('spend_amount');
        
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.usergets', compact(['user', 'search_period', 'q_response']));
    }

    public function usergetsall()
    {
        $time = microtime(true);
        $users = User::has('usergets')->orderBy('name', 'ASC')->get();
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.usergetsall', compact(['users']));
    }



    public function invoiceget()
    {
        $time = microtime(true);
        $clients = [];
        if(Auth::user()->can(['Reports_Accounting']) || Auth::user()->can(['Reports_His_Invoiceget'])){
            $store_region_ids = Region::whereHas('stores', function($q){
                $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
            })->pluck('id');

            $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
            $clients = ViewClient::has('invoices')->whereIn('region_id', $region_ids)->where('is_active', 1)->has('view_client_accounting')->orderBy('client_name', 'ASC')->get();
        }

        // $clients = ViewClient::has('invoices')->orderBy('client_name', 'ASC')->get();
        $client_types = ClientType::orderBy('name', 'ASC')->get();

        
        $loged_store_ids = Auth::user()->stores->pluck('id');
        $rep_ids = UserStore::whereIn('store_id', $loged_store_ids)->pluck('user_id');
        $reps = User::has('invoices')->whereIn('id', $rep_ids)->orderBy('fName', 'ASC')->get();

        $products = Product::orderBy('Product_code', 'ASC')->get();
        $states = Region::allStates()->get();
        $invoice_pay_status = InvoicePayStatus::orderBy('id', 'ASC')->get();
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.invoiceget', compact(['clients', 'client_types', 'reps', 'products', 'states', 'invoice_pay_status']));
    }

    public function yajrainvoiceget(Request $request)
    {
        $time = microtime(true);

        // $invoicegets = ViewReportInvoiceGet::orderBy('invoice_date', 'ASC')->get();
        $invoicegets = ViewReportInvoiceGet::orderBy('invoice_date', 'ASC');
        if($request && $request->client_id){
            $invoicegets = $invoicegets->where('client_id', $request->client_id);
        }else{
            $clients = [];
            if(Auth::user()->can(['Reports_Accounting']) || Auth::user()->can(['Reports_His_Invoiceget'])){
                $store_region_ids = Region::whereHas('stores', function($q){
                    $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
                })->pluck('id');

                $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
                $clients = ViewClient::has('invoices')->whereIn('region_id', $region_ids)->where('is_active', 1)->has('view_client_accounting')->orderBy('client_name', 'ASC')->get();
            }

            $invoicegets = $invoicegets->whereIn('client_id', $clients->pluck('id'));
        }

        

        if($request){
            if($request->invoice_id){
                $invoicegets = $invoicegets->where('invoice_id', $request->invoice_id);
            }

            if($request->client_type_id){
                $invoicegets = $invoicegets->where('client_type_id', $request->client_type_id);
            }

            if($request->rep_id){
                $invoicegets = $invoicegets->where('user_rep_id', $request->rep_id);
            }

            if($request->product_id){
                $invoicegets = $invoicegets->where('product_id', $request->product_id);
            }

            if($request->runID){
                $invoicegets = $invoicegets->where('runID', '===', $request->runID);
            }

            if($request->invoice_pay_status_id){
                $ips_id = $request->invoice_pay_status_id;
                if($ips_id == 10){
                    $invoicegets = $invoicegets->where('get_next', 0);
                }
                if($ips_id == 20){
                    $invoicegets = $invoicegets->where('get_paid', 0)->where('get_required', '>', 0);
                }
                if($ips_id == 30){
                    $invoicegets = $invoicegets->where('get_paid', '>', 0);
                }
                if($ips_id == 40){
                    $invoicegets = $invoicegets->where('get_next', '>', 0);
                }
            }

            if($request->report_period){
                $invoicegets == $invoicegets
                ->where('get_next', '>', 0)
                ->where('invoice_date', '<', Carbon::now()->subDays(intval($request->report_period)));
            }

            if($request->invoice_date_from){
                $invoicegets = $invoicegets
                ->where('invoice_date', '>=', $request->invoice_date_from);
            }

            if($request->invoice_date_to){
                $invoicegets = $invoicegets
                ->where('invoice_date', '<=', $request->invoice_date_to);
            }

            if($request->state_id){
                $invoicegets = $invoicegets->where('state_id', $request->state_id);
            }

            if($request->city_id){
                $invoicegets = $invoicegets->where(function($q) use($request){
                    return $q->where('city_id', $request->city_id)
                            ->orWhere('region_id', $request->city_id);
                });
            }

            if($request->region_id){
                $invoicegets = $invoicegets->where('region_id', $request->region_id);
            }

        }

        $invoicegets = $invoicegets->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($invoicegets)
        ->addColumn('actions',
            function($invoicegets) {
                if(!$invoicegets->invoice_id)
                    return "";

                $html ='<span class="actionLinks">';
                // $html .= '<a href=""><i class="fas fa-eye delEdit"></i></a>';
                $html .= '<a target="_blank" href="'.route('invoices.show', $invoicegets->invoice_id).'"><i class="fas fa-eye delEdit"></i></a>';
                $html .= '<a target="_blank" href="'.route('gets.create', $invoicegets->invoice_id).'"><i class="fas fa-hand-holding-usd"></i></a>';
                $html .= '<a target="_blank" href="'.route('returns.create', $invoicegets->invoice_id).'"><i class="fas fa-box-open"></i></a>';
                $html .= '</span>';
                return new HtmlString($html);
            }
        )
        ->with([
            'totalQ'=> $invoicegets->sum('invoice_net_q_withoutbounce'),
            'totalBounces'=> $invoicegets->sum('invoice_bounce_net'),
            'totalRequireds'=> $invoicegets->sum('get_required'),
            'totalPaids'=> $invoicegets->sum('get_paid'),
            'totalNexts'=> $invoicegets->sum('get_next'),
            'totalReturns'=> $invoicegets->sum('return_quantity'),
            ])
        ->make(true);
    }

    public function getsreport(Request $request)
    {
        $time = microtime(true);
        $clients = [];
        if(Auth::user()->can(['Reports_Accounting']) || Auth::user()->can(['Reports_His_Invoiceget'])){
            $store_region_ids = Region::whereHas('stores', function($q){
                $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
            })->pluck('id');

            $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
            $clients = ViewClient::has('invoices')->whereIn('region_id', $region_ids)->where('is_active', 1)->has('view_client_accounting')->orderBy('client_name', 'ASC')->get();
        }

        $products = Product::orderBy('Product_code', 'ASC')->get();
        $states = Region::allStates()->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.getsreport', compact(['products', 'states', 'clients']));
    }
    
    public function yajragetsreport(Request $request)
    {
        $time = microtime(true);
        $sql='';
        // return ViewGetsReport::client_pay_sum($request);
        if(!($request && $request->product_id)){
            if($request->client_id){
                $client_id = $request->client_id;
                $get = Get::whereHas('invoice', function ($q) use($client_id)
                {
                    return $q->where('client_id', $client_id);
                });

                if($request->get_date_from){
                    $get = $get->where('get_date', '>=', $request->get_date_from);
                }
    
                if($request->get_date_to){
                    $get = $get->where('get_date', '<=', $request->get_date_to);
                }

                $get = $get->selectRaw('sum(IFNULL(client_pay, 0)) as client_pay')->first();
                $client_pay = ($get->client_pay)? $get->client_pay : 0;
            }else{
                $client_pay_sum_arr =  ViewGetsReport::client_pay_sum($request);
                $client_pay =  $client_pay_sum_arr['q_result'][0]->client_pay;
                $sql =  $client_pay_sum_arr['sql'];
            }
        }

        $getsreport = ViewGetsReport::orderBy('Product_Name', 'DESC');
        $groupBy = [
            'product_id',
            'discount',
            'invoice_public_price',
        ];
        
        if($request){

            if($request->client_id){
                array_push($groupBy, 'client_id');
                array_push($groupBy, 'get_date');
                $getsreport->where('client_id', $request->client_id);
            }
            if($request->product_id){
                $getsreport = $getsreport->where('product_id', $request->product_id);
            }


            if($request->get_date_from){
                $getsreport = $getsreport
                ->where('get_date', '>=', $request->get_date_from);
            }

            if($request->get_date_to){
                $getsreport = $getsreport
                ->where('get_date', '<=', $request->get_date_to);
            }

            array_push($groupBy, 'state_id');
            if($request->state_id){
                $getsreport = $getsreport->where('state_id', $request->state_id);
            }

            if($request->city_id){
                array_push($groupBy, 'city_id');
                $getsreport = $getsreport->where(function($q) use($request){
                    return $q->where('city_id', $request->city_id)
                            ->orWhere('region_id', $request->city_id);
                });
            }

            if($request->region_id){
                array_push($groupBy, 'vilage_id');
                $getsreport = $getsreport->where('vilage_id', $request->region_id);
            }

        }


        $getsreport = $getsreport->groupBy(
            $groupBy 
        )
        ->selectRaw(
        'product_id,
        client_name,
        Product_Name,
        runID,
        discount,
        invoice_public_price,
        invoice_product_id,
        sum(IFNULL(get_quantity, 0)) as get_quantity,
        get_date,
        invoice_status_id,
        sum(IFNULL(client_pay_for_q, 0)) as client_pay_for_q,
        state_id,
        state,
        city_id,
        city,
        vilage_id,
        vilage')->orderBy('Product_Name', 'ASC')->get();
        // return $getsreport;
        
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($getsreport)
        ->with([
            'totalQ'=> $getsreport->sum('get_quantity'),
            'totalPaids'=> $getsreport->sum('client_pay_for_q'),
            'wallet_sum'=>(!($request && $request->product_id))? $client_pay - $getsreport->sum('client_pay_for_q') : 0,
            'client_pay'=>(!($request && $request->product_id))? $client_pay : $getsreport->sum('client_pay_for_q'),
            'sql'=> (!($request && $request->product_id))? $sql : 'Product_id'
        ])
        ->make(true);
    }

    public function clientbalance()
    {
        $time = microtime(true);
        $clients = Client::has('view_report_clientbalance')->orderBy('client_name', 'ASC')->get();
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.clientbalance', compact(['clients']));
    }

    public function yajraclientbalance(Request $request)
    {
        $time = microtime(true);
        $client_id = "";
        $date_str_from = "";
        $date_str_to = "";
        $elq_q= ViewReportClientBalance::where('client_id', '>', 0);
        $clientOverPrice = "0";
        if($request){
            $client_id = $request->client_id;
            $date_str_from = $request->date_str_from;
            $date_str_to = $request->date_str_to;

            if($request->client_id && is_numeric($request->client_id)){
                $elq_q = $elq_q
                ->where('client_id', $request->client_id);

                $client = Client::find($request->client_id);
                if($client){
                    $clientOverPrice = $client->view_client->get_overPrice_sum;
                }
            }else{
                $clients = ViewClient::all();
                $clientOverPrice = $clients->sum('get_overPrice_sum');
            }

            if($date_str_to){
                $elq_q = $elq_q
                ->where('date_str', '<=', $date_str_to);
            }
        }else{
            $clients = ViewClient::all();
            $clientOverPrice = $clients->sum('get_overPrice_sum');
        }

        $elq_q = $elq_q->get();

        // $clientbalances = ViewReportClientBalance::clientbalance($client_id, $date_str_from, $date_str_to);
        $clientbalances = ViewReportClientBalance::clientbalance($client_id, $date_str_from, $date_str_to);
        // return $clientbalances;

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($clientbalances)
        ->addColumn('actions',
            function($clientbalance) {
                if(!$clientbalance->invoice_id)
                    return "";

                $html ='<span class="actionLinks">';
                // $html .= '<a href=""><i class="fas fa-eye delEdit"></i></a>';
                $html .= '<a target="_blank" href="'.route('invoices.show', $clientbalance->invoice_id).'"><i class="fas fa-eye delEdit"></i></a>';
                $html .= '</span>';
            }
        )
        ->with([
            // 'date_str_from'=>$clientbalances->sum('date_str_from'),
            'totalRequireds'=>$elq_q->sum('get_requireds'),
            'totalPaids'=>$elq_q->sum('client_pay'),
            'clientOverPrice'=>$clientOverPrice,
        ])
        ->make(true);
    }

    public function clientbalancesum()
    {
        $time = microtime(true);
        // $clients = Client::has('invoices')->orderBy('client_name', 'ASC')->get();
        $client_types = ClientType::orderBy('name', 'ASC')->get();
        
        $states = Region::allStates()->get();


        $clients = [];
        if(Auth::user()->can(['Reports_Accounting'])){
            $store_region_ids = Region::whereHas('stores', function($q){
                $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
            })->pluck('id');

            $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
            $clients = ViewClient::has('invoices')->whereIn('region_id', $region_ids)->where('is_active', 1)->has('view_client_accounting')->orderBy('client_name', 'ASC')->get();
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.clientbalancesum', compact(['clients', 'client_types', 'states']));
    }

    public function yajraclientbalancesum(Request $request)
    {
        $time = microtime(true);
        if(Auth::user()->can(['Reports_Accounting'])){
            $store_region_ids = Region::whereHas('stores', function($q){
                $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
            })->pluck('id');

            $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
            $clientbalancesum = ViewClient::has('invoices')->whereIn('region_id', $region_ids)->orderBy('client_name', 'ASC')->where('is_balance', '>', 0);
        }elseif(Auth::user()->can(['show_reports'])){
            $clientbalancesum = ViewClient::has('invoices')->orderBy('client_name', 'ASC')->where('is_balance', '>', 0);
        }



        if($request){
            if($request->client_id){
                $clientbalancesum = $clientbalancesum->where('id', $request->client_id);
            }

            if($request->client_type_id){
                $clientbalancesum = $clientbalancesum->where('client_type_id', $request->client_type_id);
            }

            if($request->state_id){
                $clientbalancesum = $clientbalancesum->where('state_id', $request->state_id);
            }

            if($request->city_id){
                $clientbalancesum = $clientbalancesum->where(function($q) use($request){
                    return $q->where('city_id', $request->city_id)
                            ->orWhere('region_id', $request->city_id);
                });
            }

            if($request->region_id){
                $clientbalancesum = $clientbalancesum->where('region_id', $request->region_id);
            }
        }
        $clientbalancesum = $clientbalancesum->where(function ($q)
        {
            $q->where('get_overPrice_sum', '>', 3)
                ->orWhere('get_overPrice_sum', '<', -3)
                ->orWhere('get_client_nexts', '>', 3)
                ->orWhere('get_client_nexts', '<', -3);
        })->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($clientbalancesum)
        ->addColumn('client_name',
            function($clientbalancesum) {
                
                if(Auth::id()==1){
                    $html = '<a style="color:blue;" target="_blank" href="'.route('gets.zeroWalit', ['client'=>$clientbalancesum->id]).'">'.$clientbalancesum->client_name.'</a>';
                }else{
                    $html = '<a style="color:blue;" target="_blank" href="'.route('clients.accounting', ['client_id'=>$clientbalancesum->id]).'">'.$clientbalancesum->client_name.'</a>';
                }
                return new HtmlString($html);
            }
        )
        ->with([
            'get_overPrice_sum'=>$clientbalancesum->sum('get_overPrice_sum'),
            'get_requireds'=>$clientbalancesum->sum('get_requireds'),
            'client_pays'=>$clientbalancesum->sum('client_pays'),
            'get_client_nexts'=>$clientbalancesum->sum('get_client_nexts'),
        ])
        ->make(true);
    }

    public function storeproducttransfer()
    {
        $time = microtime(true);
        $products = Product::orderBy('Product_code')->get();
        $stores = Store::orderBy('Store_Name')->get();
        $fd = "2021-01-01";
        $ld = Carbon::now()->isoFormat('YYYY-MM-DD');

        if (ViewTransfer::orderBy('created_at', 'ASC')->count()){
            $fd = ViewTransfer::orderBy('created_at', 'ASC')->first()->created_at->format('Y-m-d');
            $ld = ViewTransfer::orderBy('created_at', 'DESC')->first()->created_at->format('Y-m-d');
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.storeproducttransfer', compact(['products', 'stores', 'fd', 'ld']));
    }

    public function yajrastoreproducttransfer(Request $request)
    {
        $time = microtime(true);
        $storeproducttransfer = ViewTransfer::storeproducttransfer($request);
        // return $storeproducttransfer;
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($storeproducttransfer)
        ->make(true);
    }

    public function bankbalance()
    {
        $time = microtime(true);
        $banks = Bank::orderBy('bank_name', 'ASC')->get();
        $bank_created_date = "2021-01-01 00:00:00";
        if(Bank::orderBy('created_at', 'ASC')->count()>0)
            $bank_created_date = Bank::orderBy('created_at', 'ASC')->first()->created_at;
        
            execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.bankbalance', compact(['banks', 'bank_created_date']));
    }

    public function yajrabankbalance(Request $request)
    {
        $time = microtime(true);
        $bank_id = "";
        $created_at_from = "";
        $created_at_to = "";
        $elq_q= ViewReportBank::where('bank_id', '>', 0);

        if($request){
            $bank_id = $request->bank_id;
            $created_at_from = $request->created_at_from . " 00:00:00";
            $created_at_to = $request->created_at_to. " 23:59:59";

            if($bank_id){
                $elq_q = $elq_q
                ->where('bank_id', $request->bank_id);
            }

            if($created_at_to){
                $elq_q = $elq_q
                ->where('created_at', '<=', $created_at_to);
            }
        }

        $elq_q = $elq_q->get();

        $bankbalances = ViewReportBank::bank_balance($bank_id, $created_at_from, $created_at_to);
        // return $bankbalances;

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($bankbalances)
        ->with([
            // 'created_at_from'=>$bankbalances->sum('created_at_from'),
            'depit'=>$elq_q->sum('depit'),
            'credit'=>$elq_q->sum('credit'),
        ])
        ->make(true);
    }

    public function catbalance()
    {
        $time = microtime(true);
        $cats = Cat::orderBy('cat_name', 'ASC')->get();
        $states = Region::allStates()->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.catbalance', compact(['cats', 'states']));
    }

    public function yajracatbalance(Request $request)
    {
        $time = microtime(true);
        $cat_id = "";
        $spend_date_from = "";
        $spend_date_to = "";
        $elq_q= ViewSpend::where('cat_id', '>', 0);

        if($request){
            $cat_id = $request->cat_id;
            $spend_date_from = ($request->spend_date_from)? $request->spend_date_from : "2021-01-01";
            $spend_date_to = ($request->spend_date_to)? $request->spend_date_to : Carbon::now()->isoFormat('YYYY-MM-DD');

            if($cat_id){
                $elq_q = $elq_q
                ->where('cat_id', $request->cat_id);
            }

            if($spend_date_to){
                $elq_q = $elq_q
                ->where('spend_date', "<=",  $spend_date_to);
            }

            if($request->state_id){
                $elq_q = $elq_q->where('state_id', $request->state_id);
            }

            if($request->city_id){
                $elq_q = $elq_q->where(function($q) use($request){
                    return $q->where('city_id', $request->city_id)
                            ->orWhere('region_id', $request->city_id);
                });
            }

            if($request->region_id){
                $elq_q = $elq_q->where('region_id', $request->region_id);
            }
        }



        $elq_q = $elq_q->get();
        $request = ($request)? $request : "";
        $catbalances = ViewSpend::cat_balance($cat_id, $spend_date_from, $spend_date_to, $request);
        // return $catbalances;

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($catbalances)
        ->with([
            // 'spend_date_from'=>$catbalances->sum('spend_date_from'),
            'depit'=>$elq_q->sum('depit'),
            'credit'=>$elq_q->sum('credit'),
        ])
        ->make(true);
    }

    public function productcount()
    {
        $time = microtime(true);
        $products = Product::orderBy('Product_code')->get();
        $states = Region::allStates()->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.productcount', compact(['products', 'states']));
    }

    public function yajraproductcount(Request $request)
    {
        $time = microtime(true);
        $product_id = "";
        $elq_q= ViewStockClosed::where('product_id', '>', 0);

        if($request){
            $product_id = $request->product_id;
            if($product_id){
                $elq_q = $elq_q
                ->where('product_id', $request->product_id);
            }
        }



        $elq_q = $elq_q->get();
        $request = ($request)? $request : "";
        $productcounts = ViewStockClosed::productcount($product_id, $request);
        // return $productcounts;

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($productcounts)
        ->with([
            'q_in_stores'=>$elq_q->sum('q_in_store'),
            'q_reverseds'=>$elq_q->sum('q_reversed'),
            'transfer_q_reserveds'=>$elq_q->sum('transfer_q_reserved'),
            'store_q_nets'=>$elq_q->sum('store_q_net'),
            'transfer_ins'=>$elq_q->sum('transfer_in'),
            'transfer_outs'=>$elq_q->sum('transfer_out'),
        ])
        ->make(true);
    }

    public function usersreport()
    {
        $time = microtime(true);
        $usersreports = ViewUsersReport::all();
        $invoice_pay_status = InvoicePayStatus::orderBy('id', 'ASC')->get();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.usersreport', compact(['invoice_pay_status', 'usersreports']));
    }

    public function yajrausersreport(Request $request)
    {
        $time = microtime(true);

        // fullName
        // invoicesCount
        // invoicesQuantitySum
        // returnsCount
        // returnsQuantitySum
        // getsCount
        // getsValue
        // spendsValue
        // transactionsValue
        // userBoxSave
        // requiredNextValue
        // invoicesNotPaiedCount
        // invoicesPartialPaiedCount
        // invoicesFullPaiedCount
        $dNow = Carbon::now()->isoFormat('YYYY-MM-DD 00:00:00');

        $fd = ($request->date_from)? $request->date_from : now();
        $td = ($request->date_to)? $request->date_to : now();

        $fd = (new Carbon($fd))->isoFormat('YYYY-MM-DD 00:00:00');
        $td = (new Carbon($td))->isoFormat('YYYY-MM-DD 23:59:59');

        $whereUsers = '';
        if(Auth::user()->can(['show_usersreport_reps'])){
            $userPerIds = ViewPermission::where(['name'=> 'Delegate'])->pluck('model_id')->implode(',');
            $whereUsers = " where u.is_active=1 and u.id IN ($userPerIds)";
        }else if(Auth::user()->can(['show_his_usersreport'])){
            $whereUsers = ' where u.is_active=1 and  u.id='.Auth::user()->id;
        }

        $query = "
        SELECT u.id, u.fullName,

        IFNULL((SELECT COUNT(invoices.id) FROM invoices WHERE invoices.user_rep_id = u.id and invoices.invoice_status_id=20 and invoices.created_at >= '$fd' and invoices.created_at <= '$td'), 0) invoicesCount,
        IFNULL((SELECT COUNT(gets.id) from gets left outer join invoices on gets.invoice_id = invoices.id WHERE gets.user_rep_id = u.id and invoices.invoice_status_id=20 and gets.created_at >= '$fd' and gets.created_at <= '$td'), 0) getsCount,
        IFNULL((SELECT sum(gets.client_pay) from gets left outer join invoices on gets.invoice_id = invoices.id WHERE gets.user_rep_id = u.id and invoices.invoice_status_id=20 and gets.created_at >= '$fd' and gets.created_at <= '$td'), 0) getsValue,

        IFNULL((SELECT IFNULL(sum(invoice_product.invoice_quantity),0) + IFNULL(sum(invoice_product.invoice_bounce), 0)  from invoice_product left outer join invoices on invoice_product.invoice_id = invoices.id WHERE invoices.user_rep_id = u.id and invoices.invoice_status_id=20 and invoices.created_at >= '$fd' and invoices.created_at <= '$td'), 0) invoicesQuantitySum,

        IFNULL((SELECT sum(view_invoices.get_client_nexts) from view_invoices WHERE view_invoices.user_rep_id = u.id and view_invoices.get_client_nexts > 0 and view_invoices.invoice_status_id=20 and view_invoices.created_at >= '$fd' and view_invoices.created_at <= '$td'), 0) requiredNextValue,

        IFNULL((SELECT count(view_invoices.id) from view_invoices WHERE view_invoices.user_rep_id = u.id and view_invoices.client_pays <= 0 and view_invoices.get_client_nexts>0 and view_invoices.invoice_status_id=20 and view_invoices.created_at >= '$fd' and view_invoices.created_at <= '$td'), 0) invoicesNotPaiedCount,

        IFNULL((SELECT count(view_invoices.id) from view_invoices WHERE view_invoices.user_rep_id = u.id and view_invoices.client_pays > 0 and view_invoices.get_client_nexts > 0 and view_invoices.invoice_status_id=20 and view_invoices.created_at >= '$fd' and view_invoices.created_at <= '$td'), 0) invoicesPartialPaiedCount,

        IFNULL((SELECT count(view_invoices.id) from view_invoices WHERE view_invoices.user_rep_id = u.id and view_invoices.get_client_nexts <= 0 and view_invoices.invoice_status_id=20 and view_invoices.created_at >= '$fd' and view_invoices.created_at <= '$td'), 0) invoicesFullPaiedCount,


        IFNULL((SELECT COUNT(returns.id) from returns left outer join invoices on returns.invoice_id = invoices.id WHERE invoices.user_rep_id = u.id and invoices.invoice_status_id=20 and returns.created_at >= '$fd' and returns.created_at <= '$td'), 0) returnsCount,

        IFNULL((SELECT sum(return_products.return_quantity) from return_products left outer join returns on return_products.return_id = returns.id left outer join invoices on returns.invoice_id = invoices.id WHERE invoices.user_rep_id = u.id and invoices.invoice_status_id=20 and returns.created_at >= '$fd' and returns.created_at <= '$td'), 0) returnsQuantitySum,

        IFNULL((SELECT sum(spends.spend_amount) from spends WHERE spends.spend_user_id = u.id and spends.transaction_status_id=30 and spends.created_at >= '$fd' and spends.created_at <= '$td'), 0) as spendsValue,

        IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.from_user_id = u.id and transactions.transaction_status_id=30 and transactions.created_at >= '$fd' and transactions.created_at <= '$td'), 0) as transactionsValue,
        
        IFNULL((SELECT count(date_diff) from invoices WHERE invoice_status_id=20 and user_rep_id=u.id and date_diff>0 and invoices.created_at >= '$fd' and invoices.created_at <= '$td'), 0) as date_diff,
        
        IFNULL((SELECT sum(date_diff) from invoices WHERE invoice_status_id=20 and user_rep_id=u.id and date_diff>0 and invoices.created_at >= '$fd' and invoices.created_at <= '$td'), 0) as date_diff_sum,
        

        (IFNULL((SELECT sum(gets.client_pay) from gets left outer join invoices on gets.invoice_id = invoices.id WHERE gets.user_rep_id = u.id and invoices.invoice_status_id=20 and gets.created_at >= '$fd' and gets.created_at <= '$td'), 0)
        -(IFNULL((SELECT sum(spends.spend_amount) from spends WHERE spends.spend_user_id = u.id and spends.transaction_status_id=30 and spends.created_at >= '$fd' and spends.created_at <= '$td'), 0)
        + IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.from_user_id = u.id and transactions.transaction_status_id=30 and transactions.created_at >= '$fd' and transactions.created_at <= '$td'), 0)) ) userBoxSave
        FROM users u  $whereUsers
        ";

        // TRUNCATE(
        //     (
        //     IFNULL((SELECT sum(date_diff) from invoices WHERE invoice_status_id=20 and user_rep_id=u.id and date_diff>0 and invoices.created_at >= '$fd' and invoices.created_at <= '$td')
        //     /
        //     IFNULL((SELECT count(date_diff) from invoices WHERE invoice_status_id=20 and user_rep_id=u.id and date_diff>0 and invoices.created_at >= '$fd' and invoices.created_at <= '$td'), 1)
        //     , 0)
        //     )
        // , 0)
        //  as date_diff_sum,

        // return $query;

        $usersreports = DB::select($query);

        // $usersreports = ViewUsersReport::all();

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return DataTables::of($usersreports)
        ->with([
            'fd'=> $fd,
            'td'=> $td,
            // 'invoicesQuantitySum'=> $usersreports->sum('invoicesQuantitySum'),
            // 'returnsCount'=> $usersreports->sum('returnsCount'),
            // 'returnsQuantitySum'=> $usersreports->sum('returnsQuantitySum'),
            // 'getsCount'=> $usersreports->sum('getsCount'),
            // 'getsValue'=> $usersreports->sum('getsValue'),
            // 'spendsValue'=> $usersreports->sum('spendsValue'),
            // 'transactionsValue'=> $usersreports->sum('transactionsValue'),
            // 'userBoxSave'=> $usersreports->sum('userBoxSave'),
            // 'requiredNextValue'=> $usersreports->sum('requiredNextValue'),
            // 'invoicesNotPaiedCount'=> $usersreports->sum('invoicesNotPaiedCount'),
            // 'invoicesPartialPaiedCount'=> $usersreports->sum('invoicesPartialPaiedCount'),
            // 'invoicesFullPaiedCount'=> $usersreports->sum('invoicesFullPaiedCount')
            ])
        ->make(true);
    }

    public function checkquantities($group=false)
    {
        $time = microtime(true);
        // return $group;
        if($group){
            $checks = ViewCheckQuantitiesGroup::orderBy('Product_Name', 'ASC')->get();
        }else{
            $checks = ViewCheckQuantities::orderBy('Product_Name', 'ASC')->get();
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.checkquantities', compact(['checks', 'group']));
    }

    public function sqlcheckdiff()
    {
        $time = microtime(true);
        $diff = ViewStock_Sql_Storestocks_Diff::all();
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.reports.sqlcheckdiff', compact(['diff']));
    }

    public function repprocesses(Request $request){
        if(Auth::user()->can(['Reports_Rep_Processes_ALL'])){
            $users = User::orderBy('fullName', "ASC")->get();
        }else{
            $users = User::where('id', Auth::id())->get();
        }
        $products = Product::orderBy('Product_Name', 'DESC')->get();
        return view('dashboard.reports.reprocesses.reprocesses', compact(['users', 'products']));
    }
    
    public function repprocessesresponse(Request $request){
        $user_id = 55;
        $user_id = $request->user_id;
        $product_id = $request->product_id;
        $is_sys_date = $request->is_sys_date;
        $from = '2010-01-01';
        $from = Carbon::parse($request->created_at_from); 
        $to = '2024-01-01';
        $to = Carbon::parse($request->created_at_to)->addDays(1); 
        
        $is_transfer = $request->is_transfer;
        $is_invoices = $request->is_invoices;
        $is_returns = $request->is_returns;
        $is_gets = $request->is_gets;
        $is_spends = $request->is_spends;
        $is_transactions = $request->is_transactions;
        $is_vouchers = $request->is_vouchers;
        
        $transfers = null;
        $invoices = null;
        $returns = null;
        $gets = null;
        $spends = null;
        $transactions = null;
        $vouchers = null;
        
        if($is_transfer == "true"){
            $date = ($is_sys_date == 'true')? 'created_at' : 'transfer_date';
            $transfers = Transfer::where('user_id', $user_id )->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }
        
        if($is_invoices == "true"){
            $date = ($is_sys_date == 'true')? 'created_at' : 'invoice_date';

            $invoices = Invoice::whereHas('view_invoice_products', function($q) use($product_id)
            {
                if($product_id){
                    $q->where('product_id', $product_id);
                }
            })->where('user_rep_id', $user_id )->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }
        
        if($is_returns == "true"){
            $date = ($is_sys_date == "true")? 'created_at' : 'return_date';
            $returns = Returns::where('user_rep_id', $user_id )->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }
        
        if($is_gets == "true"){
            $date = ($is_sys_date == "true")? 'created_at' : 'get_date';
            $gets = Get::where('user_rep_id', $user_id )
            ->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }
        
        if($is_spends == "true"){
            $date = ($is_sys_date == "true")? 'created_at' : 'spend_date';
            $spends = Spend::where('spend_user_id', $user_id )
            ->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }


        if($is_transactions == "true"){
            $date = ($is_sys_date == "true")? 'created_at' : 'transaction_date';
            $transactions = Transaction::where('from_user_id', $user_id )
            ->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }
        
        if($is_vouchers == "true"){
            $date = ($is_sys_date == "true")? 'created_at' : 'voucher_date';
            $vouchers = Voucher::where('user_rep_id', $user_id )
            ->where( $date, '>=', $from)->where( $date, '<=', $to)->orderBy('id', 'DESC')->get();
        }
        
        $requestdata = $request->all();
        $trueMsg = 'تم الاستعلام';
        // return $gets;
        // return view('dashboard.reports.reprocesses.repprocessesresponse', compact([
        //     'transfers',
        //     'invoices',
        //     'returns',
        //     'gets',
        //     'spends',
        //     'transactions',
        //     'vouchers',
        //     'trueMsg',
        //     'requestdata'
        // ]));
        
        return $this->sendResponse(true, [
            'strHTML' =>  view('dashboard.reports.reprocesses.repprocessesresponse', compact([
                'transfers',
                'invoices',
                'returns',
                'gets',
                'spends',
                'transactions',
                'vouchers',
                'trueMsg',
                'requestdata'
            ]))->render(),
            'requestdata'=>$requestdata
        ], $trueMsg , 200);
    }

}
