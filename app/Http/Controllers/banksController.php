<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\User;
use App\Models\ViewBank;
use DB;
use Illuminate\Support\Facades\Auth;

class banksController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::orderBy('bank_name', 'asc')->get();
        $viewbanks = ViewBank::all();
        $users = User::orderBy('fullName', 'ASC')->get();
        return view('dashboard.banks.index', compact(['banks', 'viewbanks', 'users']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::orderBy('fullName', 'ASC')->get();
        return view('dashboard.banks.create', compact(['users']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\BankRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankRequest $request)
    {
        $inputs = $request->except('_token');

        DB::beginTransaction();
        $bank = Bank::create($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الإضافة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الإضافة بنجاح',
        );
        return redirect()->route('banks.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier$bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        return view('dashboard.banks.show', compact(['bank']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        $users = User::orderBy('fullName', 'ASC')->get();
        return view('dashboard.banks.edit', compact(['bank', 'users']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\BankRequest  $request
     * @param  \App\Models\Supplier$bank
     * @return \Illuminate\Http\Response
     */
    public function update(BankRequest $request, Bank $bank)
    {
        $inputs = $request->except('_token');
        DB::beginTransaction();
        $bank->update($inputs);
        DB::commit();

        $notification = array(
            'message' => 'تم الحفظ بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحفظ بنجاح',
        );
        return redirect()->route('banks.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier$bank
     * @return \Illuminate\Http\Response
     */
    public function destroy($bank_id)
    {
        $bank = Bank::findOrFail($bank_id);
        if($bank->transactions && $bank->transactions->count()>0){
            $notification = notification('عذرا لا يمكن الحذف نظرا لأن الحساب مرتبط بسندات تحويل', false);
            return back()->withInput()->with($notification);
        }

        if($bank->inbanks && $bank->inbanks->count()>0){
            $notification = notification('عذرا لا يمكن الحذف نظرا لأن الحساب مرتبط بسندات إيداعات خارجية', false);
            return back()->withInput()->with($notification);
        }

        if($bank->spends && $bank->spends->count()>0){
            $notification = notification('عذرا لا يمكن الحذف نظرا لأن الحساب مرتبط بسندات مصروفات', false);
            return back()->withInput()->with($notification);
        }

        $bank->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('banks.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $bank_ids = $request->banks;
        if($bank_ids){
            foreach($bank_ids as $bank_id){
                $bank = Bank::find($bank_id);
                if($bank){
                    if($bank->transactions && $bank->transactions->count()>0){
                        $notification = notification('عذرا لا يمكن الحذف نظرا لأن الحساب مرتبط بسندات تحويل', false);
                        return back()->withInput()->with($notification);
                    }

                    if($bank->inbanks && $bank->inbanks->count()>0){
                        $notification = notification('عذرا لا يمكن الحذف نظرا لأن الحساب مرتبط بسندات إيداعات خارجية', false);
                        return back()->withInput()->with($notification);
                    }

                    if($bank->spends && $bank->spends->count()>0){
                        $notification = notification('عذرا لا يمكن الحذف نظرا لأن الحساب مرتبط بسندات مصروفات', false);
                        return back()->withInput()->with($notification);
                    }
                    $bank->delete();
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

        return redirect()->route('banks.index')->with($notification);
    }
}
