<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Region;
use App\Models\Clientarea;
use App\Models\Invoice;
use App\Models\ViewClient;
use App\Models\ViewInvoice;
use App\Models\ViewClientAccounting;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class clientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $clients = Clientarea::where('id', '>', 1800)->get();
        $client_types = ClientType::all();
        $states = Region::allStates()->get();
        return view('dashboard.clients.index', compact(['clients', 'states', 'client_types']));

    }

    public function yajraclients()
    {
        return DataTables::of(Clientarea::query())
        ->addColumn('actions',
            function($client) {
                $html ='<span class="actionLinks">';
                $html .= indexEdit($client, 'clients');
                if(Auth::user()->can(['Delete_Client'])){
                    $html .= indexDel(['del'=>$client, 'table'=>'clients', 'title'=>'client_name', 'indexDel'=>true, 'vars'=>false, 'transval'=> 'العميل', 'nodel'=>false]);
                }
                $html .= '</span>';
                return new HtmlString($html);
            }
        )
        
        ->setRowData([
            'data-id' => function($client) {
                if(Auth::user()->can(['Delete_Client'])){
                $html = '<input type="checkbox" name="clients[]" value="' . $client->id. '" class="boxItem"> ';
                return new HtmlString($html);
                }else{
                    return "";
                }
            },
            'data-is_active' => function($client) {
                $html = updateIsActive($client, 'clients', 'is_active');
                $html .= "<p class='pclientname' style='display:inline-block; color: blue;' data-clientid='{$client->id}'>[{$client->id}]</p>";
                return new HtmlString($html);
            },
        ])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client_types = ClientType::all();

        $states = Region::allStates()->get();
        return view('dashboard.clients.create', compact(['states', 'client_types']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        $inputs = $request->except('_token');
        if(!$request->region_id){
            $inputs['region_id'] = $request->city_id;
        }

        if(Auth::user()->can(['Delete_Client'])){
            $inputs['is_first_add']=1;
        }else{
            unset($inputs['inital_balance']);
            $inputs['is_first_add']=0;
        }

        DB::beginTransaction();
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $client = Client::create($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('clients.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        // return view('dashboard.clients.edit', compact(['client']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $client_types = ClientType::all();
        $states = Region::allStates()->get();
        return view('dashboard.clients.edit', compact(['client', 'states', 'client_types']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ClientRequest  $request
     * @param  \App\Models\Supplier  $client
     * @return \Illuminate\Http\Response
     */
    public function update(ClientRequest $request, Client $client)
    {
        $inputs = $request->except('_token');
        if(!$request->region_id){
            $inputs['region_id'] = $request->city_id;
        }

        DB::beginTransaction();
        if(Auth::user()->can(['Delete_Client'])){
            $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;
        }else{
            unset($inputs['inital_balance']);
            $inputs['is_active'] = $client->is_active;
        }
        

        $client->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('clients.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($client_id)
    {
        if(!Auth::user()->can(['Delete_Client'])){
            $notification = notification('عذرا ليس لديك صلاحية لحذف العملاء', false);
            return back()->with($notification);
        }
        $client = Client::findOrFail($client_id);

        if($client && $client->invoices->count()>0){
            $notification = notification('لا يمكن حذف عميل عليه فواتير', false);
            return back()->with($notification);
        }

        $client->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('clients.index')->with($notification);
    }

    public function delete(Request $request)
    {
        if(!Auth::user()->can(['Delete_Client'])){
            $notification = notification('عذرا ليس لديك صلاحية لحذف العملاء', false);
            return back()->with($notification);
        }
        DB::beginTransaction();
        $client_ids = $request->clients;
        if($client_ids){
            foreach($client_ids as $client_id){
                $client = Client::find($client_id);
                if($client){
                    if($client->invoices && $client->invoices->count()>0){
                        $notification = notification('لا يمكن حذف عميل عليه فواتير', false);
                        return back()->with($notification);
                    }
                    $client->delete();
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

        return redirect()->route('clients.index')->with($notification);
    }

    public function updateIsActive(Request $request, $client_id)
    {
        $client = Client::findOrFail($client_id);

        if(!Auth::user()->can(['updateIsActive_Client'])){
            $notification = notification('عذرا ليس لديك صلاحية لتعطيل أو تفعيل عميل', false);
            return back()->with($notification);
        }

        try {
            DB::beginTransaction();
            if($client){
                $is_active = ($client->is_active)? 0 : 1;
            }

            $client->update(['is_active'=>$is_active]);
            DB::commit();

            $notification = array(
                'message' => 'تم حفظ التعديلات بنجاح',
                'alert-type' => 'success',
                'success' => 'تم حفظ التعديلات بنجاح',
            );

            return redirect()->route('clients.index')->with($notification);

        } catch (\Exception $ex) {
            return redirect()->route('clients.index')->with(['error' => $this->getFileNameError('updateIsActive')]);
        }
    }

    public function getOverPriceSum(Client $client)
    {
        if($client){
            $get_client_nexts = $client->view_client->get_client_nexts;
            $client_due_limit = $client->client_due_limit();
            $is_client_due_limit = ($get_client_nexts>$client_due_limit)? true : false;
            $get_overPrice_sum =  $client->view_client->get_overPrice_sum;
            return response()->json([
                'is_client_due_limit' => $is_client_due_limit,
                'get_overPrice_sum' => $get_overPrice_sum,
            ]);
        }else{
            return false;
        }
    }

    public function accounting(Request $request, $client_id='')
    {
        $time = microtime(true);
        $sarchPeriod = '';
        $client_id = ($request->client_id)? $request->client_id : '';
        $is_period = ($request->is_period)? $request->is_period : '';
        

        $client = Clientarea::find($client_id);
        $states = Region::allStates()->get();
        if($client_id){
            if($is_period){
                $sarchPeriod = "كشف حساب في الفترة من $request->start_date الي $request->end_date";
            }else{
                $sarchPeriod = "كشف حساب طوال فترة العمل";
            }
        }
        $client_accounting = "";
        $invoices = '';
        $is_invoices = ($request->is_invoices)? true : false;
        $is_invoices = ($client_id)? true : false;
        $invoice_all = ($request->invoice_all)? true : false;
        $min_next = ($invoice_all)? -1000000000000 : 0;

        
        if(!$is_period){
            $client_accounting = ViewClientAccounting::where('client_id', $request->client_id)->first();
            $invoices = ViewInvoice::where('get_nexts', '>', $min_next)->where(['invoice_status_id'=>20, 'client_id'=>$client_id])->orderBy("get_nexts", "DESC")->get();
        }else{
            $client_accounting = ViewClientAccounting::period($request->start_date, $request->end_date, $client_id);
            $client_accounting = (is_array($client_accounting) && key_exists(0, $client_accounting))? $client_accounting[0] : '';
            // return $request->start_date;
            $invoices = ViewInvoice::where('get_nexts', '>', $min_next)->where(['invoice_status_id'=>20, 'client_id'=>$client_id])->where('invoice_date', '>=', $request->start_date)->where('invoice_date', '>=', $request->end_date)->orderBy("get_nexts", "DESC")->get();
        }

        
        $clients = [];
        if(Auth::user()->can(['Create Invoices'])){
            $store_region_ids = Region::whereHas('stores', function($q){
                $q->whereIn('store_id', Auth::user()->stores->pluck('id'));
            })->pluck('id');
            $region_ids = Region::whereIn('id', $store_region_ids)->orWhereIn('state_id', $store_region_ids)->orWhereIn('city_id', $store_region_ids)->pluck('id');
            // $clients = ViewClient::whereIn('region_id', $region_ids)->where('is_active', 1)->has('view_client_accounting')->get();
            $clients = Clientarea::whereIn('region_id', $region_ids)->get();
            // return microtime(true) - $time;
        }

        // $clients = ViewClient::where('is_active', 1)->orderBy('client_name', 'ASC')->get();
        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.clients.accounting', compact(['invoice_all', 'clients', 'client', 'states', 'sarchPeriod', 'client_accounting', 'invoices', 'is_invoices', 'is_period']));
    }

    public function getclients(Request $request)
    {
        if($request->type=='city_id'){
           $clients = Clientarea::where('city_id', $request->region_id)
                    ->orWhere('region_id', $request->region_id)->orderBy('client_name', 'ASC')->get();
        }else{
            $clients = Clientarea::where($request->type, $request->region_id)->has('view_client_accounting')->orderBy('client_name', 'ASC')->get();
        }
       $html = select(['errors'=>false, 'name'=>'client_id', 'frkName'=>'client_name', 'rows'=>$clients, 'label'=>true, 'transval'=>'العميل',  'cols'=>12 ]);

       return $html;
    }
}
