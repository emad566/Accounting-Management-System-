<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductsaleRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth;

class productsalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAll()
    {
        $products = Product::all();
        return view('dashboard.productsales.indexAll', compact(['products']));
    }

    public function index()
    {
        $products = Product::all();
        return view('dashboard.productsales.index', compact(['products']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($product)
    {
        $product = Product::findOrFail($product);
        return view('dashboard.productsales.edit', compact(['product']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ProductsaleRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductsaleRequest $request, $product)
    {
        $product = Product::findOrFail($product);

        DB::beginTransaction();
        $inputs = $request->only(['Max_Discount', 'Min_Discount']);
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $product = $product->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('productsales.index')->with($notification);
    }
}
