<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoutesRequest;
use Auth;
use App\User;
use App\Mt;
use App\Room;
use App\Common;
use App\Users_net;
use DB;
use Crypt;
use Request;
use Routeros_api;
use Validator;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use TCPDF;
use Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


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
            
            $hotspot = $API->comm ("/ip/hotspot/getall"); 

            $API->disconnect();

            return view('routes/manage', compact('data', 'useronline', 'mem', 'hdd', 'first', 'uptime', 'allroom', 'hotspot'));
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

                $data = Mt::where('mtid', $postData['mtid'])->first();
                $room = Room::where('id', $postData['id'])->first();
                
                $API = new \App\routeros_api(); 
                $API->debug = false;   

                if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                     
                    $user = $API->comm("/ip/hotspot/user/print", array(
                      "?comment" => $room->room,
                    ));
                    
                    $API->disconnect();  

                    if( isset($user['!trap']) ){
                      return view('routes/error_connect', compact('data'));
                    }  
                    
                }else{
                    return view('routes/error_connect', compact('data'));
                }

                if( count($user) == 0 ){
                    Room::where('id', $postData['id'])->update([
                        'room' => $postData['room'],
                        'roomdetail' => $postData['roomdetail']
                    ]);
                }else{
                    return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! มีผู้ใช้งานอยู่ในห้องนี้. \nกรุณาตรวจสอบอีกครั้ง.");
                }
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
            
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('id', $id)->first();
            
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                 
                $user = $API->comm("/ip/hotspot/user/print", array(
                  "?comment" => $room->room,
                ));
                
                $API->disconnect();  

                if( isset($user['!trap']) ){
                  return view('routes/error_connect', compact('data'));
                }  
                
            }else{
                return view('routes/error_connect', compact('data'));
            }

            if( count($user) == 0 ){
                Room::where('id', $id)->delete();
                return redirect('routes/pageroom/'.Crypt::encrypt($mtid));
            }else{
                return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! มีผู้ใช้งานอยู่ในห้องนี้. \nกรุณาตรวจสอบอีกครั้ง.");
            }
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
                
                $pro = $API->comm("/ip/hotspot/user/profile/print", array(
                  "?.id" => $id,
                ));

                $user = $API->comm("/ip/hotspot/user/print", array(
                  "?profile" => $pro[0]['name'],
                ));

                if( count($user) == 0 ){
                    $profile = $API->comm("/ip/hotspot/user/profile/remove", array(
                          ".id" => e($id),
                        )); 
                }else{
                    return redirect()->back()->with('message',"เกิดข้อผิดผลาด!! มีผู้ใช้งานอยู่ในรูปแบบการใช้งานนี้. \nกรุณาตรวจสอบอีกครั้ง.");
                }
           
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

                /* Paginator Array */
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                if (is_null($currentPage)) {
                    $currentPage = 1;
                }
                $collection = new Collection($user);
                $perPage = 50;
                $currentPageSearchResults = $collection->slice( ($currentPage - 1) * $perPage, $perPage)->all();
                $user= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
                $user->setPath(Crypt::encrypt($mtid));

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
            $roomkey = $roomsearch->room;
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
                      "?comment" => $roomkey ,
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

            if( $postData['comment'] == 'none' ){
                return Redirect()->back()->with('comment', 'กรุณาเลือกข้อมูล');
            }
            /*if( $postData['server'] == 'none' ){
                return Redirect()->back()->with('server', 'กรุณาเลือกข้อมูล');
            }*/
            if( $postData['profile'] == 'none' ){
                return Redirect()->back()->with('profile', 'กรุณาเลือกข้อมูล');
            }

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

                    Users_net::create([
                            'created_by' => Auth::user()->name,
                            'room' => $postData['comment'],
                            'username' => $postData['name'],
                            'password' => $postData['password'],
                            'profile' => $postData['profile'],
                            'created_type' => 'form'
                        ]);
                    
                    $ARRAY = $API->comm("/ip/hotspot/user/add", array(
                        //'server'    => e($postData['server']),
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

                return view('routes/hotspot/addfileusernet', compact('data', 'server_list', 'profile_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
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

        /*if( $postData['server'] == 'none' ){
            return Redirect()->back()->with('server', 'กรุณาเลือกข้อมูล');
        }*/
        if( $postData['profile'] == 'none' ){
            return Redirect()->back()->with('profile', 'กรุณาเลือกข้อมูล');
        }
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

                        if( $name != '' ){
                            Users_net::create([
                                'created_by' => Auth::user()->name,
                                'room' => $comment,
                                'username' => $name,
                                'password' => $password,
                                'profile' => $postData['profile'],
                                'created_type' => 'file'
                            ]);
                        }

                        $ARRAY = $API->comm("/ip/hotspot/user/add", array(
                            //'server'    => $postData['server'],
                            'name'      => $name, 
                            'password'  => $password,
                            'profile'   => $postData['profile'],
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
    * แสดงหน้า สร้างบัตร ผู้ใช้งาน internet
    */
    public function getAddCardUserNet($mtid)
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

                return view('routes/hotspot/addcardusernet', compact('data', 'server_list', 'profile_list', 'room_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        } 
    }

    public function postAddCardUserNet()
    {
        if(Auth::check()){
            $postData = Request::All();

            if( $postData['comment'] == 'none' ){
                return Redirect()->back()->with('comment', 'กรุณาเลือกข้อมูล');
            }
            /*if( $postData['server'] == 'none' ){
                return Redirect()->back()->with('server', 'กรุณาเลือกข้อมูล');
            }*/
            if( $postData['profile'] == 'none' ){
                return Redirect()->back()->with('profile', 'กรุณาเลือกข้อมูล');
            }

            $messages = [
                'cardvalue.required' => 'กรุณากรอก'            
            ];

            $rules = [
                'cardvalue' => 'required'
            ];

            $validator = Validator::make($postData, $rules, $messages);
            if ($validator->fails()) {               
                return Redirect()->back()->withInput()->withErrors($validator);
            }else{
                $data = Mt::where('mtid', $postData['mtid'])->first();

                $cardvalue = $postData['cardvalue'];

                $API = new \App\routeros_api(); 
                $API->debug = false;   

                if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                    
                    for( $i=0; $i < $cardvalue; $i++ )
                    {
                        $username  = Common::random_string('alnum', 8);
                        $password  = Common::random_string('alnum', 8);

                        Users_net::create([
                            'created_by' => Auth::user()->name,
                            'room' => $postData['comment'],
                            'username' => $username,
                            'password' => $password,
                            'profile' => $postData['profile'],
                            'created_type' => 'card'
                        ]);

                        $ARRAY = $API->comm("/ip/hotspot/user/add", array(
                            //'server'    => e($postData['server']),
                            'name'      => $username, 
                            'password'  => $password,
                            'profile'   => e($postData['profile']),
                            'comment'   => e($postData['comment'])
                        )); 

                        $stack[] = array(
                          'created_by'    =>  Auth::user()->name,
                          'username'      =>  $username,
                          'password'      =>  $password,
                          'profile'       =>  $postData['profile'],
                          'created_date'  =>  date('Y-m-d')
                        );

                    }//end for จำนวนบัตร

                    $API->disconnect();

                    //PDF
                    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                    $pdf->SetPrintHeader(false);
                    $pdf->SetPrintFooter(false);                                   
                     
                    // set header and footer fonts
                    $pdf->setHeaderFont(array('angsanaupc','',PDF_FONT_SIZE_MAIN));
                    $pdf->setFooterFont(array('angsanaupc','',PDF_FONT_SIZE_DATA));
                     
                    // set default monospaced font
                    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                     
                    // set margins
                    $pdf->SetMargins(0, 0, 0, 0);
                    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                    
                    $pdf->SetFont('angsanaupc','',14,'',true); 

                    $pdf->AddPage(); 

                    $row = 0;
                    $flag = 0;   
                    $x1 = 0;
                    $x2 = 0;
                    $y = 0;
                    $w = 0;

                    $h = 30;
                    
                    $h1 = $h;
                    $h2 = $h;
                    $top_v1 = 1;
                    $top_v2 = 1;
                    $top_h1 = 1;
                    $top_h2 = $h;

                    $xp = 0.5;
                    $yp = 0.5;  

                    $nasleft = 6;
                    $nastop  = 2;
                    $groupleft = 6;
                    $grouptop  = 7;
                    $userleft = 6;
                    $usertop  = 13;
                    $passleft = 6;
                    $passtop  = 18;              
                  
                    $style = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
                    foreach ($stack as $key) {                   

                        if( $row == 0 ){
                            $x1 = 0.5;
                            $x2 = 49.75;
                            $y = 0.5;
                            $w = 49.75;

                            $nasleft = $nasleft;
                            $nastop  = $nastop;
                            $groupleft = $groupleft;
                            $grouptop  = $grouptop;
                            $userleft = $userleft;
                            $usertop  = $usertop;
                            $passleft = $passleft;
                            $passtop  = $passtop; 
                        } 
                        if( $row == 1 ){
                            $x1 = 49.75;
                            $x2 = 99.75;
                            $y = 49.75;
                            $w = 99.75;

                            $nasleft = $nasleft+50;
                            $nastop  = $nastop;
                            $groupleft = $groupleft+50;
                            $grouptop  = $grouptop;
                            $userleft = $userleft+50;
                            $usertop  = $usertop;
                            $passleft = $passleft+50;
                            $passtop  = $passtop; 
                        } 
                        if( $row == 2 ){
                            $x1 = 99.75;
                            $x2 = 149;
                            $y = 99.75;
                            $w = 149;

                            $nasleft = $nasleft+50;
                            $nastop  = $nastop;
                            $groupleft = $groupleft+50;
                            $grouptop  = $grouptop;
                            $userleft = $userleft+50;
                            $usertop  = $usertop;
                            $passleft = $passleft+50;
                            $passtop  = $passtop;
                        }    
                         if( $row == 3 ){
                            $x1 = 149;
                            $x2 = 199.75;
                            $y = 149;
                            $w = 199.75;

                            $nasleft = $nasleft+50;
                            $nastop  = $nastop;
                            $groupleft = $groupleft+50;
                            $grouptop  = $grouptop;
                            $userleft = $userleft+50;
                            $usertop  = $usertop;
                            $passleft = $passleft+50;
                            $passtop  = $passtop; 
                        }    

                        
                        $pdf->Line($x1, $h1, $x1, $top_v1, $style);//left
                        $pdf->Line($y, $top_h1, $w, $top_h1, $style);//top
                        $pdf->Line($x2, $h2, $x2, $top_v2, $style);//right
                        $pdf->Line($y, $top_h2, $w, $top_h2, $style);//bottom
                       
                        $pdf->SetXY($nasleft, $nastop);
                        $pdf->Cell(0, 0, 'สร้างโดย: '.$key['created_by'], 0, 0, 'L', 0, '', 0);

                        $pdf->SetXY($groupleft, $grouptop);
                        $pdf->Cell(0, 0, 'กลุ่ม : '.$key['profile'], 0, 0, 'L', 0, '', 0);

                        $pdf->SetXY($userleft, $usertop);
                        $pdf->Cell(0, 0, 'ชื่อผู้ใช้ (User) : '.$key['username'], 0, 0, 'L', 0, '', 0);

                        $pdf->SetXY($passleft, $passtop);
                        $pdf->Cell(0, 0, 'รหัสผ่าน (Pass) : '.$key['password'], 0, 0, 'L', 0, '', 0);

                        $row++;

                        $xp += 70;
                        

                        if( $row == 4 ){
                            $flag++;
                            $row = 0;

                            $xp = 0.5;
                            $yp += 46;

                            $nastop += $h;
                            $grouptop += $h;
                            $usertop += $h;
                            $passtop += $h; 
                           
                            $nasleft = 6;
                            $groupleft = 6;
                            $userleft = 6;
                            $passleft = 6;

                            $h1 += $h;
                            $h2 += $h;
                            $top_v1 += $h;
                            $top_v2 += $h;
                            $top_h1 += $h;
                            $top_h2 += $h;
                        }

                        if( $flag == 9 ){
                            $pdf->AddPage(); 
                            $row = 0;
                            $flag = 0;   
                            $x1 = 0;
                            $x2 = 0;
                            $y = 0;
                            $w = 0;

                            $h = 30;
                            
                            $h1 = $h;
                            $h2 = $h;
                            $top_v1 = 1;
                            $top_v2 = 1;
                            $top_h1 = 1;
                            $top_h2 = $h; 

                            $xp = 0.5;
                            $yp = 0.5; 

                            $nasleft = 6;
                            $nastop  = 2;
                            $groupleft = 6;
                            $grouptop  = 7;
                            $userleft = 6;
                            $usertop  = 13;
                            $passleft = 6;
                            $passtop  = 18;  
                        }


                    }//foreach  
                    

                    $filename = storage_path() . '/report_card_mt.pdf';         
                    $contents = $pdf->output($filename, 'F');
                    $headers = array(
                        'Content-Type' => 'application/pdf',
                    );
                    return Response::download($filename, 'cardmt.pdf', $headers);

                    //return redirect('routes/hotspot/usernet/'.Crypt::encrypt($postData['mtid']));
                }else{
                    return view('routes/error_connect', compact('data'));
                }
            }
        }else{
            return view('auth/login');
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
                        //'server'    => e($postData['server']),
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

    /**
    * แสดงหน้า ย้ายห้องผู้ใช้งาน
    */
    public function getMoveRoomUserNet($mtid)
    {
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){

            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->get();

            $room_list=[];
            foreach ($room as $key => $value) {                    
               $room_list[$value->id] = $value->room;
            }

            $API = new \App\routeros_api(); 
            $API->debug = false;  
            
            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                  
                $ARRAY = $API->comm ("/ip/dhcp-server/getall");
                $ARRAY2 = $API->comm("/ip/hotspot/user/profile/getall");  

                $API->disconnect();

                $server_list=[];
                foreach ($ARRAY as $value) {                    
                 $server_list[$value['name']] = $value['name'];
                } 

                $profile_list=[];
                foreach ($ARRAY2 as $value) {                    
                 $profile_list[$value['.id']] = $value['name'];
                }    

                return view('routes/hotspot/moveusernetroom', compact('data' ,'server_list', 'profile_list', 'room_list'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้ารายชื่อคนในห้อง
    */
    public function getUserRoom($mtid, $roomid)
    {
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->where('id', $roomid)->first();
            
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                 
                $user = $API->comm("/ip/hotspot/user/print", array(
                  "?comment" => $room->room ,
                ));
                 
                $API->disconnect();  

                if( isset($user['!trap']) ){
                  return view('routes/error_connect', compact('data'));
                }  

                return view('routes/hotspot/olduserroom', compact('data', 'user'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * แสดงหน้ารายชื่อคนในห้อง
    */
    public function getUserRoomNew($mtid, $roomid)
    {
        $mtid = Crypt::decrypt($mtid);

        if(Auth::check()){
            $data = Mt::where('mtid', $mtid)->first();
            $room = Room::where('mtid', $mtid)->where('id', $roomid)->first();
            
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                 
                $user = $API->comm("/ip/hotspot/user/print", array(
                  "?comment" => $room->room ,
                ));
                 
                $API->disconnect();  

                if( isset($user['!trap']) ){
                  return view('routes/error_connect', compact('data'));
                }  

                return view('routes/hotspot/newuserroom', compact('data', 'user'));
            }else{
                return view('routes/error_connect', compact('data'));
            }
        }else{
            return view('auth/login');
        }
    }

    /**
    * ย้ายห้อง
    */
    public function postMoveRoomUserNet()
    {
        if(Auth::check()){

            $user = Request::all();
            $data = Mt::where('mtid', $user['mtid'])->first();
            $c = count($user['oldid']); 
            $room = Room::where('mtid', $user['mtid'])->where('id', $user['room'])->first();
                       
            $API = new \App\routeros_api(); 
            $API->debug = false;   

            if( $API->connect($data->mtip, $data->mtusername, Crypt::decrypt($data->mtpassword)) ){
                
                for ($i=0; $i < $c; $i++) { 
                    $ARRAY = $API->comm("/ip/hotspot/user/set", array(
                        '.id'       => $user['oldid'][$i],
                        //'server'    => $user['server'],
                        'profile'   => $user['profile'],
                        'comment'   => $room->room
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
