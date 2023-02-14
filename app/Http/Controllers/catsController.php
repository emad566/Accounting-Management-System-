<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatRequest;
use Illuminate\Http\Request;
use App\Models\Cat;
use DB;
use Illuminate\Support\Facades\Auth;

class catsController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cats = Cat::orderBy('cat_name', 'asc')->get();
        return view('dashboard.cats.index', compact(['cats']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.cats.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CatRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CatRequest $request)
    {
        $inputs = $request->except('_token');
        $inputs['is_user'] = ($request->is_user)? 1 : '';

        DB::beginTransaction();
        $cat = Cat::create($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('cats.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier$cat
     * @return \Illuminate\Http\Response
     */
    public function show(Cat $cat)
    {
        return view('dashboard.cats.show', compact(['cat']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier $cat
     * @return \Illuminate\Http\Response
     */
    public function edit(Cat $cat)
    {
        return view('dashboard.cats.edit', compact(['cat']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\CatRequest  $request
     * @param  \App\Models\Supplier$cat
     * @return \Illuminate\Http\Response
     */
    public function update(CatRequest $request, Cat $cat)
    {
        $inputs = $request->except('_token');
        $inputs['is_user'] = ($request->is_user)? 1 : '';

        DB::beginTransaction();
        $cat->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('cats.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier$cat
     * @return \Illuminate\Http\Response
     */
    public function destroy($cat_id)
    {
        $cat = Cat::findOrFail($cat_id);
        $cat->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('cats.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $cat_ids = $request->cats;
        if($cat_ids){
            foreach($cat_ids as $cat_id){
                $cat = Cat::find($cat_id);
                if($cat)
                    $cat->delete();
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

        return redirect()->route('cats.index')->with($notification);
    }
}
