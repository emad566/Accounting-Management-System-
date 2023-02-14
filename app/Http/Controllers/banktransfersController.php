<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\ViewBank;
use App\Models\BankTransfer;
use App\Models\Notif;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class banktransfersController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banktransfers = BankTransfer::orderBy('transaction_status_id', 'ASC')->orderBy('transfer_date', 'DESC')->get();
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        return view('dashboard.banktransfers.index', compact(['banktransfers', 'banks']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        return view('dashboard.banktransfers.create', compact(['banks']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);

        if($request->from_bank_id == $request->to_bank_id){
            $notification = notification('لا يمكن التحويل الي نفس الحساب المالي', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $bank = Bank::findOrFail($request->from_bank_id);
        if($bank->view_bank->bank_amounts_net < $request->transfer_amount){
            $notification = notification('ليس لديك رصيد كافي في الحساب المالي للتحويل', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $this->validate($request, [
            'banktransfer_code' => 'nullable|unique:bank_transfer,banktransfer_code',
            'transfer_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'transfer_amount' => 'required|min:0|max:100000',
            'from_bank_id' => 'required|numeric',
            'to_bank_id' => 'required|numeric',
            'transfer_details' => 'nullable|min:0|max:191',
        ]);


        if (!file_exists(BankTransfer::files_path($request->from_bank_id))) {
            mkdir(BankTransfer::files_path($request->from_bank_id), 0755, true);
        }

        $imageName = '';
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'banktransfer-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = BankTransfer::files_path($request->from_bank_id).$imageName;
            file_put_contents($imagePath, $imageData);
        }

        $users_notif = User::users_allow(['CRUD_banktransfer'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $banktransfer_code = $request->banktransfer_code;
        if(!$request->banktransfer_code){
            do{
                $banktransfer_code = tb_code();
            }while(Banktransfer::where('banktransfer_code', $banktransfer_code)->count()!=0);
        }

        $transfer = BankTransfer::create([
            'banktransfer_code'=>$banktransfer_code,
            'from_bank_id'=>$request->from_bank_id,
            'to_bank_id'=>$request->to_bank_id,
            'transfer_amount'=>$request->transfer_amount,
            'transfer_date'=>$request->transfer_date,
            'create_user_id'=>Auth::id(),
            'transfer_details'=>$request->transfer_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'createBanktransfer',
            'table_name' => 'banktransfers',
            'noteType' => 'BankTransfer',
            'notifiable_type' => 'App\\BankTransfer',
            'notifiable_id' => $transfer->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم طلب تحويل الحساب المالي بنجاح',
            'alert-type' => 'success',
            'success' => 'تم طلب تحويل الحساب المالي بنجاح',
        );

        return redirect()->route('banktransfers.index')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier$banktransfer
     * @return \Illuminate\Http\Response
     */
    public function show(BankTransfer $banktransfer)
    {
        return view('dashboard.banktransfers.show', compact(['banktransfer']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier $banktransfer
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransfer $banktransfer)
    {
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        return view('dashboard.banktransfers.edit', compact(['banktransfer', 'banks']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier$banktransfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankTransfer $banktransfer)
    {
        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);

        if($request->from_bank_id == $request->to_bank_id){
            $notification = notification('لا يمكن التحويل الي نفس الحساب المالي', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $bank = Bank::findOrFail($request->from_bank_id);



        if($bank->view_bank->bank_amounts_net < $request->transfer_amount){
            $notification = notification('ليس لديك رصيد كافي في الحساب المالي للتحويل', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $this->validate($request, [
            'banktransfer_code' => 'nullable|unique:bank_transfer,banktransfer_code,'.$banktransfer->id,
            'transfer_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'transfer_amount' => 'required|min:0|max:100000',
            'from_bank_id' => 'required|numeric',
            'to_bank_id' => 'required|numeric',
            'transfer_details' => 'nullable|min:0|max:191',
        ]);


        if (!file_exists(BankTransfer::files_path($request->from_bank_id))) {
            mkdir(BankTransfer::files_path($request->from_bank_id), 0755, true);
        }

        $imageName = $banktransfer->getRawOriginal('image');
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'banktransfer-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = BankTransfer::files_path($request->from_bank_id).$imageName;
            file_put_contents($imagePath, $imageData);
            if (file_exists($banktransfer->image_rel_path())) {
                $banktransfer->image_delete();
            }
        }

        $users_notif = User::users_allow(['CRUD_banktransfer'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $banktransfer_code = $request->banktransfer_code;
        if(!$request->banktransfer_code){
            do{
                $banktransfer_code = tb_code();
            }while(Banktransfer::where('banktransfer_code', $banktransfer_code)->count()!=0);
        }

        $banktransfer->update([
            'banktransfer_code'=>$banktransfer_code,
            'from_bank_id'=>$request->from_bank_id,
            'to_bank_id'=>$request->to_bank_id,
            'transfer_amount'=>$request->transfer_amount,
            'transfer_date'=>$request->transfer_date,
            'create_user_id'=>Auth::id(),
            'transfer_details'=>$request->transfer_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'updateBanktransfer',
            'table_name' => 'banktransfers',
            'noteType' => 'BankTransfer',
            'notifiable_type' => 'App\\BankTransfer',
            'notifiable_id' => $banktransfer->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم تعديل تحويل الحساب المالي بنجاح',
            'alert-type' => 'success',
            'success' => 'تم تعديل تحويل الحساب المالي بنجاح',
        );
        return redirect()->route('banktransfers.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier$banktransfer
     * @return \Illuminate\Http\Response
     */
    public function destroy($banktransfer_id)
    {
        $banktransfer = BankTransfer::findOrFail($banktransfer_id);
        if($banktransfer->status->id ==30){
            $notification = notification('عذرا لا يمكن حذف سند بعد الموافقة', false);
            return back()->withInput()->with($notification);
        }

        $banktransfer->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('banktransfers.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $banktransfer_ids = $request->banks;
        if($banktransfer_ids){
            foreach($banktransfer_ids as $banktransfer_id){
                $banktransfer = BankTransfer::find($banktransfer_id);
                if($banktransfer){
                    if($banktransfer->status->id ==30){
                        $notification = notification('عذرا لا يمكن حذف سند بعد الموافقة', false);
                        return back()->withInput()->with($notification);
                    }
                    $banktransfer->delete();
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

        return redirect()->route('banktransfers.index')->with($notification);
    }

    public function changestatus(BankTransfer $banktransfer, $status)
    {
        if($status ==30 && $banktransfer->transfer_amount > $banktransfer->from_bank->view_bank->bank_amounts_net){
            $notification = notification('ليس لديك رصيد كافي في الحساب المالي للتحويل', false);
            return back()->withInput()->with($notification);
        }

        DB::beginTransaction();
        if($status == 30){
            $banktransfer->update(['transaction_status_id'=>$status, 'accept_user_id'=> Auth::id()]);
        }else{
            $banktransfer->update(['transaction_status_id'=>$status]);
        }

        $users_notif = User::users_allow(['CRUD_banktransfer'], User::where('id', '<>', Auth::id())->get());

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'banktransfer_status_'.$status,
            'table_name' => 'banktransfers',
            'noteType' => 'BankTransfer',
            'notifiable_type' => 'App\\BankTransfer',
            'notifiable_id' => $banktransfer->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));


        $notification = notification('تم تنفيذ الأجراء بنجاح', true);
        return back()->withInput()->with($notification);
    }
}
