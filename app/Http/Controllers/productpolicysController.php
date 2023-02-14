<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Isinherit;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth;

class productpolicysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return view('dashboard.policys.productpolicys.index', compact(['products']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $productpolicy)
    {
        $isinherits = Isinherit::whereIn('id', [10,20,50])->orderBy('id')->get();
        return view('dashboard.policys.productpolicys.edit', compact(['productpolicy', 'isinherits']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $productpolicy)
    {
        $this->validate($request, [
            'paid_discount' => 'nullable|numeric|between:0,100',
            'due_discount' => 'nullable|numeric|between:0,100',
            'is_multi_due_inherit_id' => 'required|numeric',
        ]);

        DB::beginTransaction();
        $inputs = $request->except('_token');
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $productpolicy = $productpolicy->update($inputs);
        DB::commit();

        $notification = notification('تم الحفظ بنجاح', true);
        return redirect()->route('productpolicys.index')->with($notification);
    }

}
