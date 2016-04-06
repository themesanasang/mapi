<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Mt;
use App\Room;
use App\Common;
use App\Card;
use DB;
use Routeros_api;
use Crypt;

class ContentController extends Controller
{
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

            $userid = Auth::user()->id; 

            $common = new Common();
            $data = Mt::where('usermanage', $userid)->first();

            if(count($data) > 0){

                $allroom = Room::where('mtid', $data->mtid)->count();

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

                    return view('content/index', compact('data', 'useronline', 'mem', 'hdd', 'first', 'uptime', 'allroom'));
                }else{
                    return view('routes/error_connect', compact('data'));
                }
            }else{
                return view('content/index');
            }
        }else{
            return view('auth/login');
        }
    }

    
}
