<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestUpdatePass;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use Image;
use PhpParser\Node\Stmt\Return_;


class MainUserController extends Controller
{
    public function generalErrorMsg(){
        return trans('main.ThereIsAnErrorPlsTryAgainlater');
    }

    protected function getFileNameError($funcName, $msg=""){
        $msg = ($msg)? $msg : $this->generalErrorMsg();
        $fn =  basename(__FILE__, '.php');
        return  $msg . ": " . $fn . '@' .$funcName;
    }

    public function logout()
    {
        Auth::logout();
        return Redirect()->route('login');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.users.view_profile', compact(['user']));

    }

    public function update(UserRequest $request)
    {
        // try{
            DB::beginTransaction();

            $inputs = $request->except(['_token', 'image']);
            $user = Auth::user();

            $inputs['is_active'] = (!$request->has('is_active')) ? 0 : 1;



            if($image  = $request->file('image')){
                $inputs['image'] = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(300,200)->save(User::files_path().$inputs['image']);
                $user->image_delete();
            }
            $user->update($inputs);

            $user->fName = $request->fName;
            $user->lName = $request->lName;

            $user->save();

            $notification = array(
                'message' => trans('main.SavedSuccessfully'),
                'alert-type' => 'success',
                'success' => trans('main.SavedSuccessfully')
            );

            DB::commit();
            return redirect()->route('mainUser.profile')->with($notification);

        // } catch (\Exception $ex) {
        //     DB::rollback();
        //     return back()->withInput($request->all())->with(['error' => $this->getFileNameError('store')]);
        // }
    }

    public function changePass()
    {
        $user = Auth::user();
        return view('dashboard.users.changePass', compact(['user']));
    }

    public function updatePass(RequestUpdatePass $request)
    {
        DB::beginTransaction();
        $user = Auth::user();


        $hashedPassword = Auth::user()->password;

        if (\Hash::check($request->oldpassword , $hashedPassword )){
            $newpassword = bcrypt($request->password);
            $user->update( array( 'password' =>  $newpassword));

            $notification = array(
                'message' => trans('main.UpdateSuccess'),
                'alert-type' => 'success',
                'success' => trans('main.UpdateSuccess')
            );
            DB::commit();

            return redirect()->route('mainUser.profile')->with($notification);
        }else{
            DB::rollback();
            $notification = array(
                'alert-type' => 'error',
                'success' => trans('main.oldpasserror')
            );
            return back()->withInput()->withErrors($notification);
        }
    }

    public function logintoken(Request $request)
    {
        $token = UserToken::where('token', $request->device_token)->where('user_id', $request->uid)->first();

        if($token){
            $uid = $token->user_id;
            Auth::loginUsingId($uid);
            return 1;
        }else{
            return 0;
        }
    }

    public function userlogin()
    {
        
        return view('auth.userlogin');
    }
    
    public function userloged(Request $request)
    {
        $notification = array(
            'message' => 'خطأ في البيات',
            'alert-type' => 'success',
            'success' => 'خطأ في البيات'
        );
        if($request->email && $request->password){
            $user = User::where('phone', $request->email)->where('is_active', 1)->first();
            if (!$user)  $user = User::where('email', $request->email)->where('is_active', 1)->first();
            
            if (
                $user &&
                Hash::check($request->password, $user->password)
                ){
                // return $request->all();

                Auth::loginUsingId($user->id);
                return redirect()->route('dashboard');
            }
        }
        return redirect()->route('mainUser.userlogin')->with($notification);
        
    }

}
