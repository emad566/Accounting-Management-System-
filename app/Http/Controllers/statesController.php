<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionRequest;
use Illuminate\Http\Request;
use App\Models\Region;
use DB;
use Illuminate\Support\Facades\Auth;

class statesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states = Region::allStates()->get();
        return view('dashboard.states.index', compact(['states']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.states.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RegionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionRequest $request)
    {
        $oldState = Region::allStates()->where('r_name', $request->r_name)->first();
        if($oldState){
            $notification = array(
                'message' => 'عذرا يوجد محافظة مسجلة بهذا الاسم بالفعل.',
                'alert-type' => 'error',
                'error' => 'عذرا يوجد محافظة مسجلة بهذا الاسم بالفعل.',
            );
            return back()->withInput($request->all())->with($notification);
        }

        DB::beginTransaction();
        $inputs = $request->except('_token');

        $state = Region::create($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('states.index')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Region  $state
     * @return \Illuminate\Http\Response
     */
    public function show(Region $state)
    {
        return view('dashboard.states.edit', compact(['state']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Region  $state
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $state)
    {
        return view('dashboard.states.edit', compact(['state']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\RegionRequest  $request
     * @param  \App\Models\Region  $state
     * @return \Illuminate\Http\Response
     */
    public function update(RegionRequest $request, Region $state)
    {
        $oldState = Region::allStates()->where('r_name', $request->r_name)->first();
        if($oldState && $oldState->id != $state->id){
            $notification = array(
                'message' => 'عذرا يوجد محافظة مسجلة بهذا الاسم بالفعل.',
                'alert-type' => 'error',
                'error' => 'عذرا يوجد محافظة مسجلة بهذا الاسم بالفعل.',
            );
            return back()->withInput($request->all())->with($notification);
        }

        DB::beginTransaction();
        $inputs = $request->except('_token');

        $state = $state->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('states.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Region  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy($state_id)
    {
        $state = Region::findOrFail($state_id);
        $state->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('states.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $state_ids = $request->states;
        if($state_ids){
            foreach($state_ids as $state_id){
                $state = Region::find($state_id);
                if($state){
                    $state->delete();
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

        return redirect()->route('states.index')->with($notification);
    }
}
