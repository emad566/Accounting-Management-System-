<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionRequest;
use Illuminate\Http\Request;
use App\Models\Region;
use DB;
use Illuminate\Support\Facades\Auth;

class regionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states = Region::allStates()->get();
        $cities = Region::allCities()->get();
        $regions = Region::allRegions()->get();
        return view('dashboard.regions.index', compact(['regions', 'cities', 'states']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = Region::allStates()->get();
        $cities = Region::allCities()->get();
        return view('dashboard.regions.create', compact(['cities', 'states']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RegionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionRequest $request)
    {
        if(!$request->city_id){
            $notification = notification('من فضلك اختر المدينة.', false);
            return back()->withInput($request->all())->with($notification);
        }
        $region = Region::where(['r_name'=> $request->r_name, 'city_id'=> $request->city_id, 'state_id'=> $request->state_id])->first();

        if($region){
            $notification = array(
                'message' => 'عذرا يوجد منطقة مسجلة بهذا الاسم بالفعل بنفس المحافظة.',
                'alert-type' => 'error',
                'error' => 'عذرا يوجد منطقة مسجلة بهذا الاسم بالفعل بنفس المحافظة.',
            );
            return back()->withInput($request->all())->with($notification);
        }

        DB::beginTransaction();
        $inputs = $request->except('_token');

        Region::create($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('regions.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function show(Region $region)
    {
        $states = Region::allStates()->get();
        $cities = Region::allCities()->get();
        return view('dashboard.regions.edit', compact(['region', 'cities', 'states']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        $states = Region::allStates()->get();
        $cities = Region::allCities()->get();
        return view('dashboard.regions.edit', compact(['region', 'cities', 'states']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\RegionRequest  $request
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function update(RegionRequest $request, Region $region)
    {
        $regionOld = Region::where(['r_name'=> $request->r_name, 'city_id'=> $request->city_id, 'state_id'=> $request->state_id])->first();
        if($regionOld && $regionOld->id != $region->id){
            $notification = array(
                'message' => 'عذرا يوجد منطقة مسجلة بهذا الاسم بالفعل بنفس المحافظة.',
                'alert-type' => 'error',
                'error' => 'عذرا يوجد منطقة مسجلة بهذا الاسم بالفعل بنفس المحافظة.',
            );
            return back()->withInput($request->all())->with($notification);
        }

        DB::beginTransaction();
        $inputs = $request->except('_token');

        $region->update ($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('regions.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function destroy($region_id)
    {
        $region = Region::findOrFail($region_id);

        try {
            $region->forceDelete();
        } catch (\Throwable $th) {
            $region = Region::findOrFail($region_id);
            $region->delete();
        }

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('regions.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $region_ids = $request->regions;
        if($region_ids){
            foreach($region_ids as $region_id){
                $region = Region::find($region_id);
                if($region){
                    try {
                        $region->forceDelete();
                    } catch (\Throwable $th) {
                        $region = Region::find($region_id);
                        $region->delete();
                    }
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

        return redirect()->route('regions.index')->with($notification);
    }

    public function updateIsActive(Request $request, $region_id)
    {
        try {
            $region = Region::findOrFail($region_id);
            DB::beginTransaction();
            if($region){
                $is_active = ($region->is_active)? 0 : 1;
            }

            $region->update(['is_active'=>$is_active]);
            DB::commit();

            $notification = array(
                'message' => 'تم حفظ التعديلات بنجاح',
                'alert-type' => 'success',
                'success' => 'تم حفظ التعديلات بنجاح',
            );

            return redirect()->route('regions.index')->with($notification);

        } catch (\Exception $ex) {
            return redirect()->route('regions.index')->with(['error' => $this->getFileNameError('updateIsActive')]);
        }
    }

    public function cities(Request $request)
    {
        $state = Region::allStates()->where('id', $request->state_id)->first();
        if(!$state)
            return '';

        $cities = $state->cities;

        if(!$cities)
            return '';

        $edit = false;

        if($request->has('edit_id'))  $edit = $request->edit_id;

        $html = select(['errors'=>false, 'edit'=>$edit, 'name'=>'city_id', 'frkName'=>'r_name', 'rows'=>$cities, 'label'=>true, 'transval'=>'المدينة',  'cols'=>12 ]);

        // return 'ok';
        return $html;
    }

    public function regions(Request $request)
    {
        $city = Region::allCities()->where('id', $request->city_id)->first();
        // return $city;
        if(!$city)
            return '';

        $regions = $city->regions;

        if($regions->isEmpty())
            return '';

        $edit = false;

        if($request->has('edit_id'))  $edit = $request->edit_id;

        $html = select(['errors'=>false, 'edit'=>$edit, 'name'=>'region_id', 'frkName'=>'r_name', 'rows'=>$regions, 'label'=>true, 'transval'=>'المنطقة', 'cols'=>12 ]);

        // return 'ok';
        return $html;
    }
}
