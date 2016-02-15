<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;
use App\Http\Requests;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Request;
use Password;
use Hash;
use Crypt;


class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers;

    /**
    * แสดงหน้าเข้าสู่ระบบ
    */
    public function getLogin()
    {
        if(Auth::check()){
            return view('admin/index');
        }else{
            return view('auth/login');
        }
    }

    /**
    * เข้าสู่ระบบ
    */
    public function postLogin(LoginRequest $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if(Auth::attempt(['username' => $username,'password'=>$password])){
            $user = Auth::User();

            if( $user->type == 'admin' ){
                return redirect()->intended('/systems');
            }else{
                return redirect()->intended('/home');  
            }
        }else{
           return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! ชื่อผู้ใช้งานหรือรหัสผ่านผิด. \nกรุณาลองใหม่อีกครั้ง.");
        }
    }

    /**
    * แสดงหน้าสมัคร
    */
    public function getRegister()
    {
        if(Auth::check()){
            return view('auth/register');
        }else{
            return view('auth/login');
        }
    }

    /**
    * รายการผู้ใช้งาน
    */
    public function getListUser()
    {
        if(Auth::check()){
            $data = User::paginate(30);
            $data->setPath('listuser');
            return view('auth/listuser', compact('data'));
        }else{
            return view('auth/login');
        }
    }

    /**
    * สมัครผู้ใช้งาน
    */
    public function postRegister(RegisterRequest $request)
    {
        $name = $request->input('name');
        $username = $request->input('username');
        $password = $request->input('password');
        $type = $request->input('type');

        $check = User::where('username', $username)->count();

        if( $check > 0 ){
            return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! ชื่อผู้ใช้งานถูกใช้งานแล้ว. \nกรุณาลองใหม่อีกครั้ง.");
        }else{
            User::create([
                'name' => $name,
                'username' => $username,
                'password' => bcrypt($password),
                'type' => $type
            ]); 
        }

        return redirect('auth/listuser'); 
    }

    /**
    * แสดงหน้าแก้ไขผู้ใช้งาน
    */
    public function getEditUser($id)
    {
        $id = Crypt::decrypt($id);
        
        if(Auth::check()){
            $data = User::find($id);
            return view('auth/edituser', compact('data'));
        }else{
            return view('auth/login');
        }
    }

    /**
    * แก้ไขผู้ใช้งาน
    */
    public function postUpdateUser()
    {
        $postData = Request::all();

        $messages = [
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล'                        
        ];

         $rules = [
            'name' => 'required'                    
         ];

        $validator = Validator::make($postData, $rules, $messages);
        if ($validator->fails()) {               
            return Redirect()->back()->withInput()->withErrors($validator);
        }else{
            $userid = $postData['userid'];
            $name = $postData['name'];
            $type = $postData['type'];                 
            
            
            $password = $postData['password'];
            $password_confirmation = $postData['password_confirmation'];

            if( $password == '' && $password_confirmation == '' ){
                $user = User::find($userid);
                $user->name = $name;
                $user->type = $type;
                $user->save();
            }else{
                if( $password != '' && $password_confirmation != '' ){
                    $user = User::find($userid);   

                    //if (Hash::needsRehash($oldpassword)) {
                    //    $oldpassword = $oldpassword;
                    //}

                    if( $password === $password_confirmation ){
                        $user = User::find($userid);
                        $user->name = $name;
                        $user->password = bcrypt($password_confirmation);
                        $user->type = $type;
                        $user->save();
                    }else{
                        return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! รหัสผ่าน. \nกรุณาลองใหม่อีกครั้ง.");
                    }
                }else{
                    return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! รหัสผ่าน. \nกรุณาลองใหม่อีกครั้ง.");
                }
            }   
        }       

        return redirect('auth/listuser'); 
    }

    /**
    * ลบผู้ใช้งาน
    */
    public function postDeleteUser()
    {
        if(Auth::check()){
            $postData = Request::all();
            $user = User::find($postData['userid']);
            $user->delete();
        }else{
            return view('auth/login');
        }
    }

    /**
    * ออกจากระบบ
    */
    public function getLogout()
    {
        Auth::logout();
        return redirect('/');
    }






}
