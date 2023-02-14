<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use Illuminate\Http\Request;
use App\Models\Spend;
use App\Models\ViewSpend;
use App\Models\Cat;
use App\Models\User;
use App\Models\Bank;
use App\Models\Notif;
use App\Models\Region;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class spendsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $spends = null;
        if(Auth::user()->can(['show_all_spends'])){
            $spends = Spend::orderBy('transaction_status_id', 'asc')->orderBy('created_at', 'DESC')->get();
        }else if(Auth::user()->can(['show_his_spends'])){
            $spends = Spend::where('spend_user_id', Auth::user()->id)->orderBy('transaction_status_id', 'asc')->orderBy('created_at', 'DESC')->get();
        }

        $states = Region::allStates()->get();
        $banks = Bank::orderBy('bank_name', 'asc')->get();

        if(Auth::user()->can(['main_safer_admin'])){
            $users = User::where('is_active', 1)->get();
            $cats = Cat::all();
        }else{
            $users = User::where('is_active', 1)->where('id', Auth::id())->get();
            $cats = Cat::where('is_user', 0)->get();
        }

        if(!$cats){
            return 'من فضلك <a href="' .route('cats.create') .'">أضف فئة صرف</a> أولا';
        }

        return view('dashboard.spends.index', compact(['spends', 'cats', 'users', 'banks', 'states']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->usergets && !Auth::user()->can(['main_safer_admin'])){
            $notification = notification('ليس لديك رصيد كافي في صندوق خزينة المندوب للمصروف', false);
            return back()->withInput()->with($notification);
        }

        if(Auth::user()->can(['main_safer_admin'])){
            $users = User::where('is_active', 1)->get();
            $cats = Cat::all();
        }else{
            $users = User::where('is_active', 1)->where('id', Auth::id())->get();
            $cats = Cat::where('is_user', 0)->get();
        }

        if(!$cats){
            return 'من فضلك <a href="' .route('cats.create') .'">أضف فئة صرف</a> أولا';
        }

        $states = Region::allStates()->get();

        $banks = Bank::orderBy('bank_name', 'asc')->get();

        return view('dashboard.spends.create', compact(['cats', 'users', 'banks', 'states']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\SpendRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->except('_token');

        if(!$request->region_id){
            $inputs['region_id'] = $request->city_id;
        }

        if(!$request->city_id){
            $inputs['region_id'] = $request->state_id;
        }

        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);

        if (!$request->region_id && !$request->city_id && !$request->state_id){
            $notification = notification('من فضلك اختار المحافظة.', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $delegate = Auth::user()->can(['Delegate']);
        $main_safer = Auth::user()->can(['main_safer_admin']);

        if($main_safer){
            $recieve_user_id = ($request->recieve_user_id)? $request->recieve_user_id : NULL;
        }else{
            $recieve_user_id = NULL;
        }

        $bank_id = ($request->bank_id)? $request->bank_id : NULL;

        if(floatval($request->spend_amount) < 0.5){
            $notification = notification('المبلغ يجب أن يكون أكبر من 0.5', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        if(!Auth::user()->usergets && !$bank_id){
            $notification = notification('ليس لديك رصيد كافي في صندوق خزينة المندوب للمصروف', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $user_gets = (Auth::user()->usergets)? Auth::user()->usergets->user_gets : false;
        if($user_gets < $request->spend_amount && !$bank_id){
            $notification = notification('ليس لديك رصيد كافي في صندوق خزينة المندوب للمصروف', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        if($bank_id){
            $bank = Bank::findOrFail($bank_id);
            if($request->spend_amount > $bank->view_bank->bank_amounts_net){
                $notification = notification('ليس لديك رصيد كافي في الحساب المالي للمصروف', false);
                return back()->withInput($request->except('imgData'))->with($notification);
            }
        }

        $this->validate($request, [
            'spend_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'spend_amount' => 'required|min:0.5|max:100000',
            'spend_code' => 'nullable|unique:spends,spend_code,',
            'cat_id' => 'required|numeric',
            'recieve_user_id' => 'nullable|numeric',
            'spend_details' => 'nullable|min:0|max:191',
            'recieve_details' => 'nullable|min:0|max:191',
        ]);


        if (!file_exists(Spend::files_path(Auth::id()))) {
            mkdir(Spend::files_path(Auth::id()), 0755, true);
        }

        $imageName = '';
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'spend-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Spend::files_path(Auth::id()).$imageName;
            file_put_contents($imagePath, $imageData);
        }

        $users_notif = User::users_allow(['change_spend_status'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $spend_code = $request->spend_code;
        if(!$request->spend_code){
            do{
                $spend_code = tb_code();
            }while(Spend::where('spend_code', $spend_code)->count()!=0);
        }

        $spend = Spend::create([
            'spend_code'=>$spend_code,
            'region_id'=>$inputs['region_id'],
            'bank_id'=>$bank_id,
            'spend_date'=>$request->spend_date,
            'spend_user_id'=>Auth::id(),
            'recieve_user_id'=>$recieve_user_id,
            'cat_id'=>$request->cat_id,
            'spend_amount'=>$request->spend_amount,
            'spend_details'=>$request->spend_details,
            'recieve_details'=>$request->recieve_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'createSpend',
            'table_name' => 'spends',
            'noteType' => 'Spend',
            'notifiable_type' => 'App\\Spend',
            'notifiable_id' => $spend->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم طلب الصرف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم طلب الصرف بنجاح',
        );
        return redirect()->route('spends.index')->with($notification);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $spend
     * @return \Illuminate\Http\Response
     */
    public function show(ViewSpend $spend)
    {
        return view('dashboard.spends.show', compact(['spend']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $spend
     * @return \Illuminate\Http\Response
     */
    public function edit(Spend $spend)
    {
        if(Auth::user()->can(['main_safer_admin'])){
            $users = User::where('is_active', 1)->get();
        }else{
            $users = User::where('is_active', 1)->where('id', Auth::id())->get();
        }

        if(Auth::user()->can(['main_safer_admin'])){
            $users = User::where('is_active', 1)->get();
            $cats = Cat::all();
        }else{
            $users = User::where('is_active', 1)->where('id', Auth::id())->get();
            $cats = Cat::where('is_user', 0)->get();
        }

        if(!$cats){
            return 'من فضلك <a href="' .route('cats.create') .'">أضف فئة صرف</a> أولا';
        }

        $states = Region::allStates()->get();

        $banks = Bank::orderBy('bank_name', 'asc')->get();

        return view('dashboard.spends.edit', compact(['spend', 'cats', 'users', 'banks', 'states']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\SpendRequest  $request
     * @param  \App\Models\Supplier  $spend
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Spend $spend)
    {
        $inputs = $request->except('_token');

        if(!$request->region_id){
            $inputs['region_id'] = $request->city_id;
        }

        if(!$request->city_id){
            $inputs['region_id'] = $request->state_id;
        }

        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);

        $delegate = Auth::user()->can(['Delegate']);
        $main_safer = Auth::user()->can(['main_safer_admin']);

        if($main_safer){
            $recieve_user_id = ($request->recieve_user_id)? $request->recieve_user_id : NULL;
        }else{
            $recieve_user_id = NULL;
        }

        $bank_id = ($request->bank_id)? $request->bank_id : NULL;

        if(!Auth::user()->usergets && !$bank_id){
            $notification = notification('ليس لديك رصيد كافي في صندوق خزينة المندوب للمصروف', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        if(floatval($request->spend_amount) < 0.5){
            $notification = notification('المبلغ يجب أن يكون أكبر من 0.5', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $user_gets = (Auth::user()->usergets)? Auth::user()->usergets->user_gets : false;
        if($user_gets < $request->spend_amount && !$bank_id){
            $notification = notification('ليس لديك رصيد كافي في صندوق خزينة المندوب للمصروف', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        if($bank_id){
            $bank = Bank::findOrFail($bank_id);
            if($request->spend_amount > $bank->view_bank->bank_amounts_net){
                $notification = notification('ليس لديك رصيد كافي في الحساب المالي للمصروف', false);
                return back()->withInput($request->except('imgData'))->with($notification);
            }
        }

        // return $spend->spend_code;
        $this->validate($request, [
            'spend_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'spend_amount' => 'required|min:0.5|max:100000',
            'spend_code' => 'nullable|unique:spends,spend_code,'.$spend->id,
            'cat_id' => 'required|numeric',
            'recieve_user_id' => 'nullable|numeric',
            'spend_details' => 'nullable|min:0|max:191',
            'recieve_details' => 'nullable|min:0|max:191',
        ]);


        if (!file_exists(Spend::files_path(Auth::id()))) {
            mkdir(Spend::files_path(Auth::id()), 0755, true);
        }

        $imageName = $spend->getRawOriginal('image');
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'spend-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Spend::files_path(Auth::id()).$imageName;
            file_put_contents($imagePath, $imageData);

            if (file_exists($spend->image_rel_path())) {
                $spend->image_delete();
            }
        }

        $users_notif = User::users_allow(['change_spend_status'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $spend_code = $request->spend_code;
        if(!$request->spend_code){
            do{
                $spend_code = tb_code();
            }while(Spend::where('spend_code', $spend_code)->count()!=0);
        }

        $spend->update([
            'spend_code'=>$spend_code,
            'region_id'=>$inputs['region_id'],
            'bank_id'=>$bank_id,
            'spend_date'=>$request->spend_date,
            'spend_user_id'=>Auth::id(),
            'recieve_user_id'=>$recieve_user_id,
            'cat_id'=>$request->cat_id,
            'spend_amount'=>$request->spend_amount,
            'spend_details'=>$request->spend_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'updateSpend',
            'table_name' => 'spends',
            'noteType' => 'Spend',
            'notifiable_type' => 'App\\Spend',
            'notifiable_id' => $spend->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم التعديل بنجاح',
            'alert-type' => 'success',
            'success' => 'تم التعديل بنجاح',
        );
        return redirect()->route('spends.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $spend
     * @return \Illuminate\Http\Response
     */
    public function destroy($spend_id)
    {
        $spend = Spend::findOrFail($spend_id);

        if($spend->status->id <30 && $spend->spend_user_id == Auth::id()){
            if (file_exists($spend->image_rel_path())) {
                $spend->image_delete();
            }

            $spend->delete();

            $notification = array(
                'message' => 'تم الحذف بنجاح',
                'alert-type' => 'success',
                'success' => 'تم الحذف بنجاح',
            );
        }else{
            $notification = array(
                'message' => 'لا يمكن الحذف بعد الموافقة',
                'alert-type' => 'success',
                'success' => 'لا يمكن الحذف بعد الموافقة',
            );
        }


        return redirect()->route('spends.index')->with($notification);
    }

    public function delete(Request $request)
    {

        $spend_ids = $request->spends;
        if($spend_ids){
            foreach($spend_ids as $spend_id){
                $spend = Spend::find($spend_id);
                if($spend){
                    if($spend->status->id <30 && $spend->spend_user_id == Auth::id()){
                        DB::beginTransaction();
                        if (file_exists($spend->image_rel_path())) {
                            $spend->image_delete();
                        }
                        $spend->delete();
                        DB::commit();
                    }
                }
            }

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

        return redirect()->route('spends.index')->with($notification);
    }

    public function changestatus(Spend $spend, $status)
    {

        if($status == 30 && $spend->bank_id){
            $bank = $spend->bank;
            if($spend->spend_amount > $bank->view_bank->bank_amounts_net){
                $notification = notification('لا يوجد رصيد كافي في الحساب المالي للمصروف', false);
                return back()->withInput()->with($notification);
            }
        }elseif($status == 30){
            $user_gets = ($spend->spend_user->usergets)? $spend->spend_user->usergets->user_safer_balance : false;
            
            if($user_gets < $spend->spend_amount){
                $notification = notification('ليس لديك رصيد كافي في صندوق خزينة المندوب للمصروف', false);
                return back()->withInput()->with($notification);
            }
        }
        $old_spend = $spend;
        DB::beginTransaction();
        if($status == 30){
            $spend->update(['transaction_status_id'=>$status, 'accept_user_id'=> Auth::id()]);
        }else{
            $spend->update(['transaction_status_id'=>$status]);
        }

        try {
            $notif = Notif::create([
                'user_create_id' => Auth::id(),
                'notefun' => 'spend_status_'.$status,
                'table_name' => 'spends',
                'noteType' => 'Spend',
                'notifiable_type' => 'App\\Spend',
                'notifiable_id' => $old_spend->id,
            ]);
            $notif->users()->sync($old_spend->spend_user);
            event(new TransferEvt('', $notif, $notif->notif_html()));
        } catch (Exception $e) {

        }

        DB::commit();


        $notification = notification('تم تنفيذ الأجراء بنجاح', true);
        return back()->withInput()->with($notification);
    }


}
