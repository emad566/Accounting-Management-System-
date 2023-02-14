<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Bank;
use App\Models\Notif;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class transactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $time = microtime(true);
        $transactions = null;
        if(Auth::user()->can(['show_all_transactions'])){
            $transactions = Transaction::orderBy('transaction_status_id', 'asc')->orderBy('created_at', 'DESC')->get();
        }else if(Auth::user()->can(['show_his_transactions'])){
            $transactions = Transaction::where('from_user_id', Auth::user()->id)->orderBy('transaction_status_id', 'asc')->orderBy('created_at', 'DESC')->get();
        }
        $banks = Bank::all();
        if(!$banks){
            return 'من فضلك <a href="' .route('banks.create') .'">أضف حساب مالي</a> أولا';
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transactions.index', compact(['transactions', 'banks']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $time = microtime(true);
        if(!Auth::user()->usergets){
            $notification = notification('ليس لديك رصيد في الخزينة للتحويل', false);
            return back()->withInput()->with($notification);
        }

        $banks = Bank::all();
        if(!$banks){
            return 'من فضلك <a href="' .route('banks.create') .'">أضف حساب مالي</a> أولا';
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transactions.create', compact(['banks']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\TransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $time = microtime(true);
        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);

        if(!Auth::user()->usergets){
            $notification = notification('ليس لديك رصيد في الخزينة للتحويل', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $user_gets = Auth::user()->usergets->user_safer_balance;
        if($user_gets < $request->amount){
            $notification = notification('ليس لديك رصيد كافي في الخزينة للتحويل', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $this->validate($request, [
            'transaction_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'amount' => 'required|min:0|max:100000',
            'transaction_code' => 'nullable|unique:transactions,transaction_code,',
            'bank_id' => 'required|numeric',
        ]);


        if (!file_exists(Transaction::files_path(Auth::id()))) {
            mkdir(Transaction::files_path(Auth::id()), 0755, true);
        }

        $imageName = '';
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'Transaction-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Transaction::files_path(Auth::id()).$imageName;
            file_put_contents($imagePath, $imageData);
        }

        $users_notif = User::users_allow(['change_transaction_status'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $transaction_code = $request->transaction_code;
        if(!$request->transaction_code){
            do{
                $transaction_code = tb_code();
            }while(Transaction::where('transaction_code',$transaction_code)->count()!=0);
        }

        $transaction = Transaction::create([
            'transaction_code'=>$transaction_code,
            'transaction_date'=>$request->transaction_date,
            'from_user_id'=>Auth::id(),
            'bank_id'=>$request->bank_id,
            'amount'=>$request->amount,
            'transaction_details'=>$request->transaction_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'createTransaction',
            'table_name' => 'transactions',
            'noteType' => 'Transaction',
            'notifiable_type' => 'App\\Transaction',
            'notifiable_id' => $transaction->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم طلب التحويل بنجاح',
            'alert-type' => 'success',
            'success' => 'تم طلب التحويل بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transactions.index')->with($notification);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        $time = microtime(true);
        return view('dashboard.transactions.show', compact(['transaction']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        $time = microtime(true);
        $banks = Bank::all();
        if(!$banks){
            return 'من فضلك <a href="' .route('banks.create') .'">أضف حساب مالي</a> أولا';
        }

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return view('dashboard.transactions.edit', compact(['transaction', 'banks']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\TransactionRequest  $request
     * @param  \App\Models\Supplier  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $time = microtime(true);
        $imgData = '';
        if($request->imgData)
            $imgData = $request->imgData;

        $request->merge([
            'imgData' => '',
        ]);

        if(!Auth::user()->usergets){
            $notification = notification('ليس لديك رصيد في الخزينة للتحويل', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }

        $user_gets = Auth::user()->usergets->user_gets;
        if($user_gets < $request->amount){
            $notification = notification('ليس لديك رصيد كافي في الخزينة للتحويل', false);
            return back()->withInput($request->except('imgData'))->with($notification);
        }


        // return $transaction->transaction_code;
        $this->validate($request, [
            'transaction_date' => 'required|date|before_or_equal:'.Carbon::now(),
            'amount' => 'required|min:0|max:100000',
            'transaction_code' => 'nullable|unique:transactions,transaction_code,'.$transaction->id,
            'bank_id' => 'required|numeric',
        ]);


        if (!file_exists(Transaction::files_path(Auth::id()))) {
            mkdir(Transaction::files_path(Auth::id()), 0755, true);
        }

        $imageName = $transaction->getRawOriginal('image');
        if($imgData){
            $img = $imgData;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $imageData = base64_decode($img);
            $imageName = 'Transaction-'.time().'-'.hexdec(uniqid()) .'.png';
            $imagePath = Transaction::files_path(Auth::id()).$imageName;
            file_put_contents($imagePath, $imageData);

            if (file_exists($transaction->image_rel_path())) {
                $transaction->image_delete();
            }
        }

        $users_notif = User::users_allow(['change_transaction_status'], User::where('id', '<>', Auth::id())->get());

        DB::beginTransaction();

        $transaction_code = $request->transaction_code;
        if(!$request->transaction_code){
            do{
                $transaction_code = tb_code();
            }while(Transaction::where('transaction_code', $transaction_code)->count()!=0);
        }

        $transaction = Transaction::create([
            'transaction_code'=>$transaction_code,
            'transaction_date'=>$request->transaction_date,
            'from_user_id'=>Auth::id(),
            'bank_id'=>$request->bank_id,
            'amount'=>$request->amount,
            'transaction_details'=>$request->transaction_details,
            'transaction_status_id'=>10,
            'image'=>$imageName
        ]);

        $notif = Notif::create([
            'user_create_id' => Auth::id(),
            'notefun' => 'updateTransaction',
            'table_name' => 'transactions',
            'noteType' => 'Transaction',
            'notifiable_type' => 'App\\Transaction',
            'notifiable_id' => $transaction->id,
        ]);

        $notif->users()->sync($users_notif);

        DB::commit();

        event(new TransferEvt('', $notif, $notif->notif_html()));

        $notification = array(
            'message' => 'تم التعديل بنجاح',
            'alert-type' => 'success',
            'success' => 'تم التعديل بنجاح',
        );

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transactions.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy($transaction_id)
    {
        $time = microtime(true);
        $transaction = Transaction::findOrFail($transaction_id);

        if($transaction->status->id <30 && $transaction->from_user_id == Auth::id()){
            if (file_exists($transaction->image_rel_path())) {
                $transaction->image_delete();
            }

            $transaction->delete();

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


        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transactions.index')->with($notification);
    }

    public function delete(Request $request)
    {
        $time = microtime(true);
        $transaction_ids = $request->transactions;
        if($transaction_ids){
            foreach($transaction_ids as $transaction_id){
                $transaction = Transaction::find($transaction_id);
                if($transaction){
                    if($transaction->status->id <30 && $transaction->from_user_id == Auth::id()){
                        DB::beginTransaction();
                        if (file_exists($transaction->image_rel_path())) {
                            $transaction->image_delete();
                        }
                        $transaction->delete();
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

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return redirect()->route('transactions.index')->with($notification);
    }

    public function changestatus(Transaction $transaction, $status)
    {
        $time = microtime(true);
        if($transaction->amount > $transaction->from_user->usergets->user_safer_balance && $status <40){
            $notification = notification('ليس لديك رصيد كافي في الخزينة للتحويل', false);
            return back()->withInput()->with($notification);
        }

        $transaction->update(['transaction_status_id'=>$status]);

        if($transaction->from_user->id != Auth::id()){
            $notif = Notif::create([
                'user_create_id' => Auth::id(),
                'notefun' => 'transaction_status_'.$status,
                'table_name' => 'transactions',
                'noteType' => 'Transaction',
                'notifiable_type' => 'App\\Transaction',
                'notifiable_id' => $transaction->id,
            ]);

            $notif->users()->sync($transaction->from_user);
            event(new TransferEvt('', $notif, $notif->notif_html()));
        }

        DB::commit();



        $notification = notification('تم تنفيذ الأجراء بنجاح', true);

        execution_time_php($time, __class__ . '@' .__FUNCTION__);
        return back()->withInput()->with($notification);
    }


}
