<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class rolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('dashboard.users.roles.index', compact(['roles', 'permissions']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('dashboard.users.roles.create', compact(['permissions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = Role::where('name', $request->name)->first();
        if($role){
            $notification = array(
                'message' => 'يوجد بالفعل رتبة بهذا الاسم',
                'alert-type' => 'error',
                'error' => 'يوجد بالفعل رتبة بهذا الاسم',
            );
            return back()->withInput($request->all())->with($notification);
        }

        $role = Role::create(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        $notification = array(
            'message' => 'تم إضافة الرتبة بنجاح',
            'alert-type' => 'success',
            'success' => 'تم إضافة الرتبة بنجاح',
        );
        return redirect()->route('roles.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissionIds =  $role->permissions->pluck('id')->toArray();
        return view('dashboard.users.roles.edit', compact(['role', 'permissions', 'rolePermissionIds']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissionIds =  $role->permissions->pluck('id')->toArray();
        return view('dashboard.users.roles.edit', compact(['role', 'permissions', 'rolePermissionIds']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\RoleRequest  $request
     * @param  \App\Models\Permission  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $roleOld = Role::where('name', $request->name)->first();
        if($roleOld && ($role->id != $roleOld->id)){
            $notification = array(
                'message' => 'يوجد بالفعل رتبة بهذا الاسم',
                'alert-type' => 'error',
                'error' => 'يوجد بالفعل رتبة بهذا الاسم',
            );
            return back()->withInput($request->all())->with($notification);
        }
        $role->syncPermissions($request->permissions);
        $role->update(['name' => $request->name]);


        $notification = array(
            'message' => 'تم حفظ التعديلات بنجاح',
            'alert-type' => 'success',
            'success' => 'تم حفظ التعديلات بنجاح',
        );
        return redirect()->route('roles.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('roles.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $role_ids = $request->roles;
        if($role_ids){
            foreach($role_ids as $role_id){
                $role = Role::find($role_id);
                if($role)
                    $role->delete();
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

        return redirect()->route('roles.index')->with($notification);
    }
}
