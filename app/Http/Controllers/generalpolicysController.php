<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generalpolicy;


class generalpolicysController extends Controller
{
    public function edit(Request $generalpolicy)
    {
        $generalpolicy = Generalpolicy::find(1);
        
        return view('dashboard.policys.generalpolicys.edit', compact(['generalpolicy']));
    }

    public function update(Request $request, Generalpolicy $generalpolicy)
    {
        $this->validate($request, [
            'last_time' => 'required|date_format:H:i',
            'rep_limit' => 'required|numeric',
            'client_due_limit' => 'required|numeric',
            'paid_discount' => 'required|numeric|between:0,100',
            'due_discount' => 'required|numeric|between:0,100',
            'auot_accept_permission_name' => 'nullable|between:0,100',
        ]);
        $inputs = $request->except('_token');
        $inputs['is_multi_due'] = ($request->has('is_multi_due')) ? 1 : 0;
        $inputs['pay_more_invoice_balance'] = ($request->has('pay_more_invoice_balance')) ? 1 : 0;
        $generalpolicy->update($inputs);

        $notification = notification('تم الحفظ بنجاح', true);
        return redirect()->back()->with($notification);
    }

}
