<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\RequestUpdatePass;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Store;
use App\Models\UserToken;
use DB;
use PdfReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class manageusersController extends Controller
{

    public function displayReport()
    {


        $fromDate = '15/09/2020';
        $toDate = '15/09/2022';
        $sortBy = 'name';

        $title = 'Registered User Report'; // Report title

        $meta = [ // For displaying filters description on header
            'Registered on' => $fromDate . ' To ' . $toDate,
            'Sort By' => $sortBy
        ];

        $queryBuilder = User::select(['name', 'fName', 'is_active', 'created_at']) // Do some querying..
                            ->whereBetween('created_at', [$fromDate, $toDate])
                            ->orderBy($sortBy);

        $columns = [ // Set Column to be displayed
            'Name' => 'name',
            'created_at', // if no column_name specified, this will automatically seach for snake_case of column name (will be registered_at) column from query result
            'First Name' => 'fName',
            'Status' => function($result) { // You can do if statement or any action do you want inside this closure
                return ($result->is_active > 100000) ? 'yes' : 'No';
            }
        ];

        // Generate Report with flexibility to manipulate column class even manipulate column value (using Carbon, etc).
        return PdfReport::of($title, $meta, $queryBuilder, $columns)

                        ->stream(); // other available method: download('filename') to download pdf / make() that
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::all();
        $users = User::where('id', '<>', 1)->orderBy('id', 'DESC')->with('session')->get();
        $roles = Role::all();
        return view('dashboard.users.manageusers.index', compact(['users', 'roles', 'stores']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::where('is_active', '1')->where('id', '<>', 1)->orderBy('Store_Name')->get();
        $roles = Role::all();
        return view('dashboard.users.manageusers.create', compact(['roles', 'stores']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {

        $inputs = $request->except('_token');

        $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;
        $inputs['password'] = bcrypt($request->password);

        $user = User::create($inputs);
        $user->syncRoles([$inputs['role_id']]);
        if($request->stores)
            $user->stores()->sync($request->stores);

        $notification = array(
            'message' => 'تم إضافة العضو بنجاح',
            'alert-type' => 'success',
            'success' => 'تم إضافة العضو بنجاح',
        );
        return redirect()->route('manageusers.index')->with($notification);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        $stores = Store::where('is_active', '1')->where('id', '<>', 1)->orderBy('Store_Name')->get();
        $user = User::findOrFail($user_id);
        $roles = Role::all();
        $select_id = $user->getRole('id');
        return view('dashboard.users.manageusers.edit', compact(['user', 'roles', 'select_id', 'stores']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($user_id)
    {
        $stores = Store::where('is_active', '1')->where('id', '<>', 1)->orderBy('Store_Name')->get();
        $user = User::findOrFail($user_id);
        $roles = Role::all();
        $select_id = $user->getRole('id');
        return view('dashboard.users.manageusers.edit', compact(['user', 'roles', 'select_id', 'stores']));
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
        return redirect()->route('manageusers.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        $notification = array(
            'message' => 'تم الحذف بنجاح',
            'alert-type' => 'success',
            'success' => 'تم الحذف بنجاح',
        );
        return redirect()->route('manageusers.index')->with($notification);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        $user_ids = $request->manageusers;
        if($user_ids){
            foreach($user_ids as $user_id){
                $user = User::find($user_id);
                if($user)
                    $user->delete();
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
                'message' => 'من فضلك إختر عنصر لحذفة، أو حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
                'alert-type' => 'error',
                'error' => 'من فضلك إختر عنصر لحذفة، أو حدث خطأ حاول مرة أخري، إذا تكررت المشكلة تواصل مع الدعم الفني.',
            );
        }

        return redirect()->route('manageusers.index')->with($notification);
    }

    public function updateIsActive(Request $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            DB::beginTransaction();
            if($user->is_active){
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }
            if($user){
                $is_active = ($user->is_active)? 0 : 1;
            }

            $user->update(['is_active'=>$is_active]);
            DB::commit();

            $notification = array(
                'message' => 'تم حفظ التعديلات بنجاح',
                'alert-type' => 'success',
                'success' => 'تم حفظ التعديلات بنجاح',
            );

            return redirect()->route('manageusers.index')->with($notification);

        } catch (\Exception $ex) {
            return redirect()->route('manageusers.index')->with(['error' => "Code Error: updateIsActive, manageusersController at delete Session"]);
        }
    }

    public function changePass($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('dashboard.users.manageusers.changePass', compact(['user']));
    }

    public function updatePass(RequestUpdatePass $request)
    {
        DB::beginTransaction();
        $user = User::findOrFail($request->user_id);


        if ($user){
            $newpassword = bcrypt($request->password);
            $user->update( array( 'password' =>  $newpassword));

            $notification = array(
                'message' => 'تم حفظ التعديلات بنجاح',
                'alert-type' => 'success',
                'success' => 'تم حفظ التعديلات بنجاح',
            );
            DB::commit();

            return redirect()->route('manageusers.edit', $user->id)->with($notification);
        }else{
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'success' => trans('main.oldpasserror')
            );

            return back()->withInput()->withErrors($notification);
        }
    }

    public function loginById(User $user)
    {
        if(Auth::check() && Auth::user()->can(['loginUser'])){
            if($user){
                request()->session()->regenerate(true);
                request()->session()->flush();
                Auth::guard('web')->loginUsingId($user->id, true);
                $notification = notification('تم تسجيل الدخول بالعضو: '. $user->fullName, true);
                return Redirect(route('site.home'))->with($notification);
            }else{
                return 'No User id';
            }
        }else return "not permission";
    }

    public function adminloginget()
    {
        return view('dashboard.users.manageusers.adminlogin');
    }

    public function adminlogin(Request $request)
    {
        $user = User::where(['email'=>$request->UserEmail])->first();
        if($user){
            $admin = User::where(['email'=>$request->email])->first();
            if($admin && $admin->can(['loginUser'])){
                if(Hash::check($request->password, $admin->password)){
                    Auth::guard('web')->loginUsingId($user->id, true);
                    $notification = notification('تم تسجيل الدخول بالعضو: '. $user->fullName, true);
                    return Redirect(route('site.home'))->with($notification);
                }
            }
            $notification = notification('عذرا، خطأ في البريد الألكتروني أو كلمة المرور', false);
            return back()->withInput()->with($notification);
        }else{
            $notification = notification('عذرا، لا يوجد عضو بهذا البريد', false);
            return back()->withInput()->with($notification);
        }
        // return view('dashboard.users.manageusers.adminlogin');
    }

    public function usertokens(Request $request)
    {
        $UserTokens = UserToken::where('user_id', Auth::id())->where('token', '=', $request->token)->get();
        if($UserTokens->count()<1){
            UserToken::create([
                'user_id'=>Auth::id(),
                'token'=>$request->token,
            ]);

            return response()->json(['status'=>"true"]);
        }else{
            return response()->json(['status'=>"false"]);
        }
    }
}
