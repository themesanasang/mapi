<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {
 
    public function rules(){
        return [
         'username' => 'required',
         'password' => 'required'
        ];
    }

    public function messages()
    {
      return [
        'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
        'password.required' => 'กรุณากรอกรหัสผ่าน'
      ];
    }

    public function authorize(){
       return true;
    }
}