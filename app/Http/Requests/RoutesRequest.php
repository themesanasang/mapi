<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoutesRequest extends FormRequest {
 
    public function rules(){
        return [
         'mtname' => 'required',
         'mtip' => 'required',
         'mtport' => 'required',
         'mtusername' => 'required',
         'mtpassword' => 'required'
        ];
    }

    public function messages()
    {
      return [
        'mtname.required' => 'กรุณากรอกชื่ออุปกรณ์',
        'mtip.required' => 'กรุณากรอกไอพี',
        'mtport.required' => 'กรุณากรอกพอร์ต',
        'mtusername.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
        'mtpassword.required' => 'กรุณากรอกรหัสผ่าน'
      ];
    }

    public function authorize(){
       return true;
    }
}