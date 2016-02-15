<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {
 
    public function rules(){
        return [
         'name' => 'required',
         'username' => 'required',
         'password' => 'required'
        ];
    }

    public function messages()
    {
      return [
        'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
        'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
        'password.required' => 'กรุณากรอกรหัสผ่าน'
      ];
    }

    public function authorize(){
       return true;
    }
}