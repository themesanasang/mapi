<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoutesRequest;
use Auth;
use App\User;
use App\Mt;
use App\Room;
use App\Common;
use DB;
use Crypt;
use Request;
use Routeros_api;
use Validator;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

class MikrotikController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }






    /************************************/
    /*             Routes              */
    /************************************/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check()){
            if( Auth::user()->type == 'user' ){
                $data = DB::table('routes')
                            ->where('usermanage', Auth::user()->id)
                            ->paginate(30);
            }else{
                $data = DB::table('routes')
                            ->leftjoin('users','users.id','=','routes.usermanage')
                            ->select('routes.*', 'users.name')
                            ->paginate(30);
            }
            //return $data;
            $data->setPath('routes');
            return view('routes/index', compact('data'));
        }else{
            return view('auth/login');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::check()){
            if( Auth::user()->type == 'user' ){
                return view('routes/addroutes');
            }else{
                $user = User::all();
                $userall=[];
                foreach ($user as $key => $value) {                    
                   $userall[$value->id] = $value->name;
                }  
                return view('routes/addroutes', compact('userall'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoutesRequest $request)
    {
        if(Auth::check()){
            $mtname = $request->input('mtname');
            $mtip = $request->input('mtip');
            $mtport = $request->input('mtport');
            $mtusername = $request->input('mtusername');
            $mtpassword = $request->input('mtpassword');
            $mtdetail = $request->input('mtdetail');
            $usermanage = $request->input('usermanage');

            Mt::create([
                'mtname' => $mtname,
                'mtip' => $mtip,
                'mtport' => $mtport,
                'mtusername' => $mtusername,
                'mtpassword' => Crypt::encrypt($mtpassword),
                'mtdetail' => $mtdetail,
                'usermanage' => $usermanage
            ]); 

            return redirect('routes'); 
        }else{
            return view('auth/login');
        }       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);

        $common = new Common();
        $data = Mt::where('mtid', $id)->first();

        $allroom = Room::where('mtid', $id)->count();

        $API = new \App\routeros_api(); 
        $API->debug = false;   

        if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){

            $ARRAY = $API->comm("/system/resource/print");

            //ถ้าดึงข้อมูล mt ไม่ได้ หรือ error แสดง 404
            if( isset($ARRAY['!trap']) ){
              return view('routes/error_connect', compact('data'));
            }else{
              $first = $ARRAY['0'];
            }  

            $useronline = $API->comm ("/ip/hotspot/active/getall"); 
            $useronline = count($useronline); 

            $memperc = ($first['free-memory']/$first['total-memory']);
            $hddperc = ($first['free-hdd-space']/$first['total-hdd-space']);
            $mem = ($memperc*100);
            $hdd = ($hddperc*100); 
            $uptime = $common->UptimeInSeconds($first['uptime']);

            $API->disconnect();

            return view('routes/manage', compact('data', 'useronline', 'mem', 'hdd', 'first', 'uptime', 'allroom'));
        }else{
            return view('routes/error_connect', compact('data'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        if(Auth::check()){
            $data = Mt::where('mtid', $id)->first();

            if( Auth::user()->type == 'user' ){
                return view('routes/editroutes', compact('data'));
            }else{
                $user = User::all();
                $userall=[];
                foreach ($user as $key => $value) {                    
                   $userall[$value->id] = $value->name;
                }  
                return view('routes/editroutes', compact('userall', 'data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoutesRequest $request, $id)
    {
        if(Auth::check()){
            $mtname = $request->input('mtname');
            $mtip = $request->input('mtip');
            $mtport = $request->input('mtport');
            $mtusername = $request->input('mtusername');
            $mtpassword = $request->input('mtpassword');
            $mtdetail = $request->input('mtdetail');
            $usermanage = $request->input('usermanage');

            Mt::where('mtid', $id)
                ->update([
                    'mtname' => $mtname,
                    'mtip' => $mtip,
                    'mtport' => $mtport,
                    'mtusername' => $mtusername,
                    'mtpassword' => Crypt::encrypt($mtpassword),
                    'mtdetail' => $mtdetail,
                    'usermanage' => $usermanage,
                ]);

            return redirect('routes'); 
        }else{
            return view('auth/login');
        }  
    }

    /**
    * ลบ MT
    */
    public function postDeleteRoutes()
    {
        if(Auth::check()){
            $postData = Request::all();
            Mt::where('mtid', $postData['mtid'])->delete();
        }else{
            return view('auth/login');
        }
    }






    /************************************/
    /*               Room               */
    /************************************/

    /**
    * แสดงหน้าเพิ่มห้อง
    */
    public function getPageRoom($id)
    {
        $id = Crypt::decrypt($id);

        if(Auth::check()){
            $data = Mt::where('mtid', $id)->first();
            $room = Room::where('mtid', $id)->paginate(30);
            $room->setPath(Crypt::encrypt($id));
            return view('routes/addroom', compact('data', 'room'));
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า แก้ไขห้อง
    */
    public function getEditPageRoom($id, $mtid)
    {
        $id = Crypt::decrypt($id);
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->paginate(30);
            $room->setPath(Crypt::encrypt($id).'/'.Crypt::encrypt($mtid));
            $editroom = Room::find($id);
            return view('routes/addroom', compact('data', 'room', 'editroom'));
        }else{
            return view('auth/login');
        }
    }

    /**
    * เพิ่มห้อง
    */
    public function postAddRoom()
    {
        if(Auth::check()){
            $postData = Request::All();

            $messages = [
                'room.required' => 'กรุณากรอกชื่อห้อง',                 
            ];

            $rules = [
                'room' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{

                $chk = Room::where('mtid', $postData['mtid'])->where('room', $postData['room'])->count();
                if( $chk > 0 ){
                    return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! มีชื่อห้องนี้อยู่แล้ว. \nกรุณาลองใหม่อีกครั้ง.");
                }else{
                    Room::create([
                        'mtid' => $postData['mtid'],
                        'room' => $postData['room'],
                        'roomdetail' => $postData['roomdetail']
                    ]);
                }
            }
            
            return redirect('routes/pageroom/'.Crypt::encrypt($postData['mtid']));
        }else{
            return view('auth/login');
        }
    }

    /**
    * แก้ห้อง
    */
    public function postEditRoom()
    {
        if(Auth::check()){
            $postData = Request::All();

            $messages = [
                'room.required' => 'กรุณากรอกชื่อห้อง',                 
            ];

            $rules = [
                'room' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{
                Room::where('id', $postData['id'])
                    ->update([
                        'room' => $postData['room'],
                        'roomdetail' => $postData['roomdetail']
                    ]);
            }
            
            return redirect('routes/pageroom/'.Crypt::encrypt($postData['mtid']));
        }else{
            return view('auth/login');
        }
    }

    /**
    * ลบห้อง
    */
    public function getDeleteRoom($mtid,$id)
    {
        $id = Crypt::decrypt($id);
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            Room::where('id', $id)->delete();
            return redirect('routes/pageroom/'.Crypt::encrypt($mtid));
        }else{
            return view('auth/login');
        }
    }






    /************************************/
    /*        user profile hotspot      */
    /************************************/

    /**
    * แสดงหน้ารายการ userprofile
    */
    public function getUserProfile($id)
    {
        $id = Crypt::decrypt($id);

        if(Auth::check()){
            $data = Mt::where('mtid', $id)->first();
            
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                $profile = $API->comm("/ip/hotspot/user/profile/getall");          
                $API->disconnect();

                //ถ้าดึงข้อมูล mt ไม่ได้ หรือ error แสดง 404    
                if( isset($profile['!trap']) ){
                   return view('routes/error_connect', compact('data'));
                }

                return view('routes/hotspot/userprofile', compact('data', 'profile'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า adduserprofile
    */
    public function getAddUserProfile($id)
    {
        $id = Crypt::decrypt($id);

        if(Auth::check()){
            $data = Mt::where('mtid', $id)->first();

            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                $ARRAY = $API->comm("/ip/firewall/address-list/getall");           
                $API->disconnect();

                //ถ้าดึงข้อมูล mt ไม่ได้ หรือ error แสดง 404    
                if( isset($ARRAY['!trap']) ){
                   return view('routes/error_connect', compact('data'));
                }

                $address_list=[];
                foreach ($ARRAY as $value) {                    
                 $address_list[$value['list']] = $value['list'];
                }   

                return view('routes/hotspot/adduserprofile', compact('data', 'address_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * add userprofile
    */
    public function postAddUserProfile()
    {
        if(Auth::check()){
            $postData = Request::All();

            $messages = [
                'name.required' => 'กรุณากรอกชื่อรูปแบบ', 
                'session.required' => 'กรุณากรอกเวลาในการเชื่อมต่อใช้งานต่อครั้ง', 
                'use.required' => 'กรุณากรอกผู้ใช้งานสามารถใช้ได้กี่เครื่อง'                 
            ];

            $rules = [
                'name' => 'required',
                'session' => 'required',
                'use' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{
                $data = Mt::where('mtid', $postData['mtid'])->first();

                $API = new \App\routeros_api(); 
                $API->debug = false;   

                if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                    $ARRAY = $API->comm("/ip/firewall/address-list/getall");           
                   
                    //ถ้าดึงข้อมูล mt ไม่ได้ หรือ error แสดง 404    
                    if( isset($ARRAY['!trap']) ){
                       return view('routes/error_connect', compact('data'));
                    }

                    $ARRAY = $API->comm("/ip/hotspot/user/profile/add", array(
                        'name'                => e($postData['name']),
                        'session-timeout'     => e($postData['session']), 
                        'idle-timeout'        => 'none',
                        'keepalive-timeout'   => '00:02:00',
                        'status-autorefresh'  => '00:01:00',
                        'shared-users'        => e($postData['use']),
                        'rate-limit'          => e($postData['limit']),
                        'address-list'        => e($postData['address'])
                        //'on-login'            => $this->scriptUseday(e($postData['useday']))
                    ));   

                    $API->disconnect();

                    return redirect('routes/hotspot/userprofile/'.Crypt::encrypt($data->mtid));
                }else{
                    return view('routes/error_connect', compact('data'));
                }
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า แก้ไข userprofile
    */
    public function getEditUserProfile($name, $mtid)
    {
        $name = Crypt::decrypt($name);
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
           
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                $profile = $API->comm("/ip/hotspot/user/profile/print", array(
                              "?name" => $name,
                            )); 
                  
                $ARRAY = $API->comm("/ip/firewall/address-list/getall");           

                $API->disconnect();

                $address_list=[];
                foreach ($ARRAY as $value) {                    
                 $address_list[$value['list']] = $value['list'];
                }     

                return view('routes/hotspot/edituserprofile', compact('data', 'address_list', 'profile'));
            }else{
                return view('routes/error_connect', compact('data'));
            }

            //return view('routes/addroom', compact('data', 'room', 'editroom'));
        }else{
            return view('auth/login');
        }
    }

    /**
    * แก้ไข userprofile
    */
    public function postEditUserProfile()
    {
        if(Auth::check()){
            $postData = Request::All();

            $messages = [
                'name.required' => 'กรุณากรอกชื่อรูปแบบ', 
                'session.required' => 'กรุณากรอกเวลาในการเชื่อมต่อใช้งานต่อครั้ง', 
                'use.required' => 'กรุณากรอกผู้ใช้งานสามารถใช้ได้กี่เครื่อง'                 
            ];

            $rules = [
                'name' => 'required',
                'session' => 'required',
                'use' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{
                $data = Mt::where('mtid', $postData['mtid'])->first();
           
                $API = new \App\routeros_api(); 
                $API->debug = false;   

                if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                    $ARRAY = $API->comm("/ip/hotspot/user/profile/set", array(
                          '.id'                 => e($postData['id']),
                          'name'                 => e($postData['name']),
                          'session-timeout'     => e($postData['session']), 
                          'shared-users'        => e($postData['use']),
                          'rate-limit'          => e($postData['limit']),
                          'address-list'        => e($postData['address']),
                    ));          
                   
                    $API->disconnect(); 

                    return redirect('routes/hotspot/userprofile/'.Crypt::encrypt($postData['mtid']));
                }else{
                    return view('routes/error_connect', compact('data'));
                }
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * ลบ userprofile
    */
    public function getDeleteUserProfile($id, $mtid)
    {
        $id = Crypt::decrypt($id);
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            
            $data = Mt::where('mtid', $mtid)->first();
           
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                
                $profile = $API->comm("/ip/hotspot/user/profile/remove", array(
                          ".id" => e($id),
                        ));    
           
                $API->disconnect();

                return redirect('routes/hotspot/userprofile/'.Crypt::encrypt($mtid));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }







    /************************************/
    /*        user hotspot               */
    /************************************/

    /**
    * แสดงหน้า รายการผู้ใช้งาน internet
    */
    public function getUserNet($mtid)
    {
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->get();

            $room_list=[];
            foreach ($room as $key => $value) {                    
               $room_list[Crypt::encrypt($value->id)] = $value->room;
            } 
            
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                $user = $API->comm("/ip/hotspot/user/getall");    

                $API->disconnect();  

                if( isset($user['!trap']) ){
                  return view('routes/error_connect', compact('data'));
                }  

                return view('routes/hotspot/usernet', compact('data', 'user', 'room_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า รายการผู้ใช้งาน internet Search
    */
    public function getUserNetSearch($roomsearch, $mtid)
    {
        if( $roomsearch == 'All' ){
            $roomsearch = 'All';
        }else{
            $roomsearch = Crypt::decrypt($roomsearch);
            $roomsearch = Room::find($roomsearch);
        }

        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->get();

            $room_list=[];
            foreach ($room as $key => $value) {                    
               $room_list[Crypt::encrypt($value->id)] = $value->room;
            } 


            
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                 
                if( $roomsearch == 'All' ){
                   $user = $API->comm("/ip/hotspot/user/getall"); 
                }else{
                    $user = $API->comm("/ip/hotspot/user/print", array(
                      "?comment" => $roomsearch->room,
                    ));
                }  
                
                $API->disconnect();  

                if( isset($user['!trap']) ){
                  return view('routes/error_connect', compact('data'));
                }  

                return view('routes/hotspot/usernetsearch', compact('data', 'user', 'room_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า เพิ่มผู้ใช้งาน internet
    */
    public function getAddUserNet($mtid)
    {
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->get();

            $room_list=[];
            foreach ($room as $key => $value) {                    
               $room_list[$value->room] = $value->room;
            }  

            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                $ARRAY = $API->comm ("/ip/hotspot/getall");
                //ดึงค่าใน mt ไม่ได้หรือ error แสดง 404
                if( isset($ARRAY['!trap']) ){
                  return view('routes/error_connect', compact('data'));
                } 
                $ARRAY2 = $API->comm("/ip/hotspot/user/profile/getall");  

                $API->disconnect();

                $server_list=[];
                foreach ($ARRAY as $value) {                    
                 $server_list[$value['name']] = $value['name'];
                } 

                $profile_list=[];
                foreach ($ARRAY2 as $value) {                    
                 $profile_list[$value['name']] = $value['name'];
                } 

                return view('routes/hotspot/addusernet', compact('data', 'server_list', 'profile_list', 'room_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * เพิ่ม ผู้ใช้งาน internet
    */
    public function postAddUserNet()
    {
        if(Auth::check()){
            $postData = Request::All();

            $messages = [
                'name.required' => 'กรุณากรอกชื่อผู้ใช้งาน', 
                'password.required' => 'กรุณากรอกรหัสผ่าน'                 
            ];

            $rules = [
                'name' => 'required',
                'password' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{
                $data = Mt::where('mtid', $postData['mtid'])->first();

                $API = new \App\routeros_api(); 
                $API->debug = false;   

                if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                    
                    $ARRAY = $API->comm("/ip/hotspot/user/add", array(
                        'server'    => e($postData['server']),
                        'name'      => e($postData['name']), 
                        'password'  => e($postData['password']),
                        'profile'   => e($postData['profile']),
                        'email'     => e($postData['email']),
                        'comment'   => e($postData['comment'])
                    ));  

                    $API->disconnect();

                    return redirect('routes/hotspot/usernet/'.Crypt::encrypt($data->mtid));
                }else{
                    return view('routes/error_connect', compact('data'));
                }
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า อัพโหลดไฟล์ Excel
    */
    public function getAddFileUserNet($mtid)
    {
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();

            return view('routes/hotspot/addfileusernet', compact('data'));
        }else{
            return view('auth/login');
        } 
    }

    /**
    * อ่านไฟล์ Excel
    */
    public function postAddFileUserNet()
    {
        $postData = Request::All();
        $file = Request::file('fileexcel');
        //return $file->getMimeType();

        if( $file == '' ){
            return Redirect()->back()->with('fileexcel', 'กรุณาลองใหม่อีกครั้ง');
        }

        $filename = $file->getClientOriginalName();

        $acceptedFormats = array('xls', 'xlsx');
        if(!in_array(pathinfo($filename, PATHINFO_EXTENSION), $acceptedFormats)) {
            return Redirect()->back()->with('fileexcel', 'กรุณาเลือกประเภทไฟล์ให้ถูกต้อง');
        }else{
            $file->move('storage/tempexcel',$file->getClientOriginalName());

            $objPHPExcel = PHPExcel_IOFactory::load( "storage/tempexcel/".$filename );
            
            $data = Mt::where('mtid', $postData['mtid'])->first();

            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) 
                {
                    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $nrColumns = ord($highestColumn) - 3; 

                    for ($row = 2; $row <= $highestRow; ++ $row) 
                    {
                       $val = array();
                        for ($col = 0; $col < $highestColumnIndex; ++ $col) 
                        {
                            $cell  = $worksheet->getCellByColumnAndRow($col, $row);
                            $val[] = $cell->getValue();                                                   
                        } 

                        $name       = $val[0];
                        $password   = $val[1];
                        $comment    = $val[2];

                        $ARRAY = $API->comm("/ip/hotspot/user/add", array(
                            'server'    => 'all',
                            'name'      => $name, 
                            'password'  => $password,
                            'profile'   => 'default',
                            'comment'   => $comment
                        )); 
                        
                    } //end for 1
                }//end foreach 1

                $API->disconnect();
            }else{
                return view('routes/error_connect', compact('data'));
            }

            return Redirect()->back()->with('fileexcelok', 'อัพโหลดไฟล์เรียบร้อยแล้ว');
        }
    }

    /**
    * แสดงหน้า แก้ไข ผู้ใช้งาน internet
    */
    public function getEditUserNet($name, $mtid)
    {
        $name = Crypt::decrypt($name);
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->get();

            $room_list=[];
            foreach ($room as $key => $value) {                    
               $room_list[$value->room] = $value->room;
            }
           
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                $user = $API->comm("/ip/hotspot/user/print", array(
                          "?name" => e($name),
                        )); 
                  
                $ARRAY = $API->comm ("/ip/dhcp-server/getall");
                $ARRAY2 = $API->comm("/ip/hotspot/user/profile/getall");  

                $API->disconnect();

                $server_list=[];
                foreach ($ARRAY as $value) {                    
                 $server_list[$value['name']] = $value['name'];
                } 

                $profile_list=[];
                foreach ($ARRAY2 as $value) {                    
                 $profile_list[$value['name']] = $value['name'];
                }    

                return view('routes/hotspot/editusernet', compact('data', 'user' ,'server_list', 'profile_list', 'room_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แแก้ไข ผู้ใช้งาน internet
    */
    public function postEditUserNet()
    {
        if(Auth::check()){
            $postData = Request::All();

            $messages = [
                'name.required' => 'กรุณากรอกชื่อผู้ใช้งาน', 
                'password.required' => 'กรุณากรอกรหัสผ่าน'                 
            ];

            $rules = [
                'name' => 'required',
                'password' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{
                $data = Mt::where('mtid', $postData['mtid'])->first();
           
                $API = new \App\routeros_api(); 
                $API->debug = false;   

                if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                    $ARRAY = $API->comm("/ip/hotspot/user/set", array(
                        '.id'       => e($postData['id']),
                        'server'    => e($postData['server']),
                        'name'      => e($postData['name']), 
                        'password'  => e($postData['password']),
                        'profile'   => e($postData['profile']),
                        'email'     => e($postData['email']),
                        'comment'   => e($postData['comment'])
                    ));      
                   
                    $API->disconnect(); 

                    return redirect('routes/hotspot/usernet/'.Crypt::encrypt($postData['mtid']));
                }else{
                    return view('routes/error_connect', compact('data'));
                }
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้า ลบ ผู้ใช้งาน internet
    */
    public function getDeleteUserNet($id, $mtid)
    {
        $id = Crypt::decrypt($id);
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            
            $data = Mt::where('mtid', $mtid)->first();
           
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                
                $API->comm("/ip/hotspot/user/remove", array(
                          ".id" => e($id),
                        ));   
           
                $API->disconnect();

                return redirect('routes/hotspot/usernet/'.Crypt::encrypt($mtid));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * ลบ ผู้ใช้งาน internet แบบ select all
    */
    public function postDeleteUserNet()
    {
        if(Auth::check()){

            $user = Request::all();
            $data = Mt::where('mtid', $user['mtid'])->first();
            $c = count($user['id']); 
                       
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                
                for ($i=0; $i < $c; $i++) { 
                
                    $API->comm("/ip/hotspot/user/remove", array(
                          ".id" => $user['id'][$i],
                        )); 

                }
           
                $API->disconnect();
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }







}
