<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\RequestUpdatePass;
use App\Models\Isinherit;
use App\Models\Region;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use DB;


class regionpolicysController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('dashboard.policys.regionpolicys.index', compact(['users']));
    }

    public function edit()
    {
        $states = Region::allStates()->get();
        $isinherits = Isinherit::where('id', '<=', 30)->orderBy('id')->get();
        return view('dashboard.policys.regionpolicys.edit', compact(['states', 'isinherits']));
    }

    public function featch($region_id)
    {
        $region = Region::find($region_id);
        $isinherits = Isinherit::where('id', '<=', 30)->orderBy('id')->get();
        $html = "";
        $html .= input(['errors'=>'', 'edit'=>$region, 'type'=>'number', 'name'=>'client_due_limit', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من السياسات العامة']);
        $html .= input(['errors'=>'', 'edit'=>$region, 'type'=>'text', 'name'=>'paid_discount', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من السياسات العامة']);
        $html .= input(['errors'=>'', 'edit'=>$region, 'type'=>'text', 'name'=>'due_discount', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من السياسات العامة']);
        $html .= select(['errors'=>'', 'edit'=>$region, 'name'=>'is_multi_due_inherit_id', 'frkName'=>'name', 'rows'=>$isinherits, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]);
        return $html;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'edit_id' => 'required|numeric',
            'client_due_limit' => 'nullable|numeric',
            'is_multi_due_inherit_id' => 'required|numeric',
            'paid_discount' => 'nullable|numeric|between:0,100',
            'due_discount' => 'nullable|numeric|between:0,100',
        ]);

        $region = Region::find($request->edit_id);
        if($region){
            $data = [
                'client_due_limit' => $request->client_due_limit,
                'is_multi_due_inherit_id' => $request->is_multi_due_inherit_id,
                'paid_discount' => $request->paid_discount,
                'due_discount' => $request->due_discount,
            ];
            $region->update($data);
            $notification = notification('تم حفظ التعديلات بنجاح', true);
        }else{
            $notification = notification('هذه المنطقة غير موجوده!!', false);
            return redirect()->back()->withInput()->with($notification);
        }

        return redirect()->route('regionpolicys.edit')->with($notification);
    }
}
