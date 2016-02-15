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

            return view('routes/manage', compact('data', 'useronline', 'mem', 'hdd', 'first', 'uptime'));
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








}
