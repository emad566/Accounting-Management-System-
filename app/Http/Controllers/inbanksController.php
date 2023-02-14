<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Inbank;
use App\Models\ViewBank;
use App\Models\Notif;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class inbanksController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inbanks = Inbank::orderBy('transaction_status_id', 'ASC')->orderBy('inbank_date', 'DESC')->get();
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        return view('dashboard.inbanks.index', compact(['inbanks', 'banks']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        return view('dashboard.inbanks.create', compact(['banks']));
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


        $this->validate($request, [
            'inbank_code' => 'nullable|unique:inbanks,inbank_code',
            'inbank_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'inbank_amount' => 'required|min:0|max:100000',
            'to_bank_id' => 'required|numeric',
            'inbank_details' => 'nullable|min:0|max:191',
        ]);


        if (!file_exists(Inbank::files_path($request->to_bank_id))) {
            mkdir(Inbank::files_path($request->to_bank_id), 0755, true);
        }

        $imageName = '';
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'inbank-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Inbank::files_path($request->to_bank_id).$imageName;
            file_put_contents($imagePath, $imageData);
        }

        $users_notif = User::users_allow(['CRUD_inbank'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $inbank_code = $request->inbank_code;
        if(!$request->inbank_code){
            do{
                $inbank_code = tb_code();
            }while(Inbank::where('inbank_code', $inbank_code)->count()!=0);
        }

        $inbank = Inbank::create([
            'inbank_code'=>$inbank_code,
            'to_bank_id'=>$request->to_bank_id,
            'inbank_amount'=>$request->inbank_amount,
            'inbank_date'=>$request->inbank_date,
            'create_user_id'=>Auth::id(),
            'inbank_details'=>$request->inbank_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'createInbank',
            'table_name' => 'inbanks',
            'noteType' => 'Inbank',
            'notifiable_type' => 'App\\Inbank',
            'notifiable_id' => $inbank->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم طلب إيداع مالي خارجي بنجاح',
            'alert-type' => 'success',
            'success' => 'تم طلب إيداع مالي خارجي بنجاح',
        );

        return redirect()->route('inbanks.index')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier$inbank
     * @return \Illuminate\Http\Response
     */
    public function show(Inbank $inbank)
    {
        return view('dashboard.inbanks.show', compact(['inbank']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier $inbank
     * @return \Illuminate\Http\Response
     */
    public function edit(Inbank $inbank)
    {
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        return view('dashboard.inbanks.edit', compact(['inbank', 'banks']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier$inbank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inbank $inbank)
    {
        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);
        $bank = Bank::findOrFail($request->to_bank_id);

        $this->validate($request, [
            'inbank_code' => 'nullable|unique:inbanks,inbank_code,'.$inbank->id,
            'inbank_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'inbank_amount' => 'required|min:0|max:100000',
            'to_bank_id' => 'required|numeric',
            'inbank_details' => 'nullable|min:0|max:191',
        ]);


        if (!file_exists(Inbank::files_path($request->to_bank_id))) {
            mkdir(Inbank::files_path($request->to_bank_id), 0755, true);
        }

        $imageName = $inbank->getRawOriginal('image');
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'inbank-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Inbank::files_path($request->to_bank_id).$imageName;
            file_put_contents($imagePath, $imageData);
            if (file_exists($inbank->image_rel_path())) {
                $inbank->image_delete();
            }
        }

        $users_notif = User::users_allow(['CRUD_inbank'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $inbank_code = $request->inbank_code;
        if(!$request->inbank_code){
            do{
                $inbank_code = tb_code();
            }while(Inbank::where('inbank_code', $inbank_code)->count()!=0);
        }

        $inbank->update([
            'inbank_code'=>$inbank_code,
            'to_bank_id'=>$request->to_bank_id,
            'inbank_amount'=>$request->inbank_amount,
            'inbank_date'=>$request->inbank_date,
            'create_user_id'=>Auth::id(),
            'inbank_details'=>$request->inbank_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'updateInbank',
            'table_name' => 'inbanks',
            'noteType' => 'Inbank',
            'notifiable_type' => 'App\\Inbank',
            'notifiable_id' => $inbank->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم تعديل إيداع مالي خارجي بنجاح',
            'alert-type' => 'success',
            'success' => 'تم تعديل إيداع مالي خارجي بنجاح',
        );
        return redirect()->route('inbanks.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier$inbank
     * @return \Illuminate\Http\Response
     */
    public function destroy($inbank_id)
    {
        $inbank = Inbank::findOrFail($inbank_id);
        if($inbank->status->id ==30){
            $notification = notification('عذرا لا يمكن حذف سند بعد الموافقة', false);
            return back()->withInput()->with($notification);
        }

        $inbank->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('inbanks.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $inbank_ids = $request->inbanks;
        if($inbank_ids){
            foreach($inbank_ids as $inbank_id){
                $inbank = Inbank::find($inbank_id);
                if($inbank){
                    if($inbank->status->id ==30){
                        $notification = notification('عذرا لا يمكن حذف سند بعد الموافقة', false);
                        return back()->withInput()->with($notification);
                    }
                    $inbank->delete();
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

        return redirect()->route('inbanks.index')->with($notification);
    }

    public function changestatus(Inbank $inbank, $status)
    {
        DB::beginTransaction();
        if($status == 30){
            $inbank->update(['transaction_status_id'=>$status, 'accept_user_id'=> Auth::id()]);
        }else{
            $inbank->update(['transaction_status_id'=>$status]);
        }

        $users_notif = User::users_allow(['CRUD_inbank'], User::where('id', '<>', Auth::id())->get());

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'inbank_status_'.$status,
            'table_name' => 'inbanks',
            'noteType' => 'Inbank',
            'notifiable_type' => 'App\\Inbank',
            'notifiable_id' => $inbank->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();
        event(new TransferEvt('', $notif, $notif->notif_html()));


        $notification = notification('تم تنفيذ الأجراء بنجاح', true);
        return back()->withInput()->with($notification);
    }
}
