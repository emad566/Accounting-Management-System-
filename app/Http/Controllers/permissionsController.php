<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class permissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::orderBy('id', 'desc')->get();
        $roles = Role::orderBy('id', 'desc')->get();
        return view('dashboard.users.permissions.index', compact(['permissions', 'roles']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('dashboard.users.permissions.create', compact(['roles']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PermissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $permission = Permission::where('name', $request->name)->first();
        if($permission){
            $notification = array(
                'message' => 'يوجد بالفعل صلاحية بهذا الاسم',
                'alert-type' => 'error',
                'error' => 'يوجد بالفعل صلاحية بهذا الاسم',
            );
            return back()->withInput($request->all())->with($notification);
        }

        $permission = Permission::create(['name' => $request->name]);
        $permission->syncRoles($request->roles);


        $notification = array(
            'message' => 'تم إضافة الصلاحية بنجاح',
            'alert-type' => 'success',
            'success' => 'تم إضافة الصلاحية بنجاح',
        );
        return redirect()->route('permissions.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        $roles = Role::all();
        $permissionRoleIds =  $permission->roles->pluck('id')->toArray();
        return view('dashboard.users.permissions.edit', compact(['permission', 'roles', 'permissionRoleIds']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        $roles = Role::all();
        $permissionRoleIds =  $permission->roles->pluck('id')->toArray();
        return view('dashboard.users.permissions.edit', compact(['permission', 'roles', 'permissionRoleIds']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PermissionRequest  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        $permissionOld = Permission::where('name', $request->name)->first();
        if($permissionOld && ($permission->id != $permissionOld->id)){
            $notification = array(
                'message' => 'يوجد بالفعل صلاحية بهذا الاسم',
                'alert-type' => 'error',
                'error' => 'يوجد بالفعل صلاحية بهذا الاسم',
            );
            return back()->withInput($request->all())->with($notification);
        }
        $permission->syncRoles($request->roles);
        $permission->update(['name' => $request->name]);

        $notification = array(
            'message' => 'تم حفظ التعديلات بنجاح',
            'alert-type' => 'success',
            'success' => 'تم حفظ التعديلات بنجاح',
        );
        return redirect()->route('permissions.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($permission)
    {
        $permission = Permission::findOrFail($permission);
        $permission->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('permissions.index')->with($notification);
    }

    public function delete(PermissionRequest $request)
    {
        DB::beginTransaction();
        $permission_ids = $request->permissions;
        if($permission_ids){
            foreach($permission_ids as $permission_id){
                $permission = permission::find($permission_id);
                if($permission)
                    $permission->delete();
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

        return redirect()->route('permissions.index')->with($notification);
    }
}
