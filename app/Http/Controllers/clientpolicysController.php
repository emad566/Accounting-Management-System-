<?php
namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Region;
use App\Models\Clientarea;
use App\Models\Invoice;
use App\Models\Isinherit;
use App\Models\Product;
use App\Models\ProductPolicy;
use App\Models\ViewClient;
use App\Models\ViewClientAccounting;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class clientpolicysController extends Controller
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
        return view('dashboard.policys.clientpolicys.index', compact(['clients', 'states', 'client_types']));

    }

    public function yajraclients()
    {
        return DataTables::of(ViewClient::query())
        ->addColumn('actions',
            function($client) {
                $html ='<span class="actionLinks">';
                $html .= indexEdit($client, 'clientpolicys');
                $html .= '</span>';
                return new HtmlString($html);
            }
        )
        ->setRowData([
            'data-client_due_limit' => function($client) {
                // return "";
                return $client->client_due_limit();
            },
            'data-is_multi_due_inherit_id' => function($client) {
                // return "";
                return $client->is_multi_due_inherit_name();
            },
        ])
        ->make(true);
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
        $products = Product::orderBy('Product_code', 'ASC')->get();
        $isinherits = Isinherit::whereIn('id', [10,20,60])->orderBy('id')->get();
        return view('dashboard.policys.clientpolicys.edit', compact(['client', 'states', 'client_types', 'isinherits', 'products']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ClientRequest  $request
     * @param  \App\Models\Supplier  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        $this->validate($request, [
            'client_due_limit' => 'nullable|numeric',
            'is_multi_due_inherit_id' => 'required|numeric',
        ]);
        $inputs = [
            'client_due_limit'=>$request->client_due_limit,
            'is_multi_due_inherit_id'=>$request->is_multi_due_inherit_id,
        ];
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;



        DB::beginTransaction();
        $client->update($inputs);
        DB::commit();

        $notification = array('تم الحفظ بنجاح', true);
        return redirect()->route('clientpolicys.index')->with($notification);
    }

    public function productpolicys(Request $request)
    {
        if($request){
            $this->validate($request, [
                'product_id' => 'required|numeric',
                'client_id' => 'required|numeric',
                'is_multi_due_inherit_id' => 'required|numeric',

                'paid_discount' => 'nullable|numeric|between:0,100',
                'due_discount' => 'nullable|numeric|between:0,100',
            ]);

            $client = Client::find($request->client_id);
            if($client){
                $inputs = [
                    'client_id' => $client->id,
                    'product_id' => $request->product_id,
                    'is_multi_due_inherit_id' => $request->is_multi_due_inherit_id,
                    'paid_discount' => $request->paid_discount,
                    'due_discount' => $request->due_discount,
                ];
                $productpolicy = ProductPolicy::where(['client_id'=>$client->id, 'product_id'=>$request->product_id])->first();

                if($productpolicy){
                    $productpolicy->update($inputs);
                }else{
                    ProductPolicy::create($inputs);
                }
            }

            $client = Client::find($request->client_id);

            $trs = "";
            if($client->productpolicys && $client->productpolicys->count()>0){
                $productpolicys = $client->productpolicys->sortBy(function($q){
                    return $q->product->Product_code;
                })
                ->all();
                foreach($productpolicys as $productpolicy){
                     $trs .= '
                        <tr id="policy_'. $productpolicy->id. '">
                            <td>'. $productpolicy->product->Product_Name .'</td>
                            <td>'. $productpolicy->product->Product_code .'</td>
                            <td>'. $productpolicy->paid_discount .'</td>
                            <td>'. $productpolicy->due_discount .'</td>
                            <td>'. $productpolicy->is_multi_due_inherit->name .'</td>
                            <td><a policy_id="'. $productpolicy->id. '" href="'.route('clientpolicys.destroy', $productpolicy->id). '" class="deletePolicy"><i class="fas fa-trash-alt delEdit"></i></a></td>
                        </tr>
                     ';
                }
            }
        }
        return $trs;
    }

    public function destroy($productpolicy_id)
    {
        $ProductPolicy = ProductPolicy::find($productpolicy_id);
        if($ProductPolicy){
            $ProductPolicy->delete();
            return true;
        }
        return false;
    }
}
