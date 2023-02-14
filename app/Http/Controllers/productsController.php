<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth;

class productsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return view('dashboard.products.index', compact(['products']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        $inputs = $request->except('_token');
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $inputs['user_id'] = Auth::id();
        $product = Product::create($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('products.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('dashboard.products.edit', compact(['product']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('dashboard.products.edit', compact(['product']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();
        $inputs = $request->except('_token');
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $product = $product->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('products.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('products.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $product_ids = $request->products;
        if($product_ids){
            foreach($product_ids as $product_id){
                $product = Product::find($product_id);
                if($product)
                    $product->delete();
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

        return redirect()->route('products.index')->with($notification);
    }

    public function updateIsActive(Request $request, $product_id)
    {
        try {
            $product = Product::findOrFail($product_id);
            DB::beginTransaction();
            if($product){
                $is_active = ($product->is_active)? 0 : 1;
            }

            $product->update(['is_active'=>$is_active]);
            DB::commit();

            $notification = array(
                'message' => 'تم حفظ التعديلات بنجاح',
                'alert-type' => 'success',
                'success' => 'تم حفظ التعديلات بنجاح',
            );

            return redirect()->route('products.index')->with($notification);

        } catch (\Exception $ex) {
            return redirect()->route('products.index')->with(['error' => $this->getFileNameError('updateIsActive')]);
        }
    }
}
