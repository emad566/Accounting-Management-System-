<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\RequestUpdatePass;
use App\Models\Isinherit;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use DB;


class userpolicysController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('dashboard.policys.userpolicys.index', compact(['users']));
    }

    public function edit($user_id)
    {
        $stores = Store::where('is_active', '1')->where('id', '<>', 1)->orderBy('Store_Name')->get();
        $user = User::findOrFail($user_id);
        $roles = Role::all();
        $select_id = $user->getRole('id');
        $isinherits = Isinherit::where('id', '<=', 30)->orderBy('id')->get();
        return view('dashboard.policys.userpolicys.edit', compact(['user', 'roles', 'select_id', 'stores', 'isinherits']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $user_id)
    {
        $this->validate($request, [
            'last_time' => 'nullable|date_format:H:i',
            'rep_limit' => 'nullable|numeric',
            // 'is_multi_due_inherit_id' => 'required|numeric',
        ]);

        $user = User::findOrFail($user_id);
        $inputs = $request->except('_token');
        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;

        $user->syncRoles($inputs['role_id']);

        if($request->stores)
            $user->stores()->sync($request->stores);

        $user->update($inputs);

        $notification = array(
            'message' => 'تم حفظ التعديلات بنجاح',
            'alert-type' => 'success',
            'success' => 'تم حفظ التعديلات بنجاح',
        );
        return redirect()->route('userpolicys.index')->with($notification);
    }
}
