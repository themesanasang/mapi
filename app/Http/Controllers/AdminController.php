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
use App\Users_net;
use DB;
use Routeros_api;

class AdminController extends Controller
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

            $user = count( User::all() );
            $mt = count( Mt::all() );
            $usernet = count( Users_net::all() );

            $common = new Common();

            //Hdd info
            $perc =  round((disk_free_space("/")*100) / disk_total_space("/"),2);
            $hddperc = 100 - $perc;
            //server linux                           
            $server = array(                         
                    'cpuload'            => $common->get_cpu_game(),
                    'uptime'             => $common->uptime(),
                    'memory_usage'       => $common->get_memory_game(),                      
                    'hdd_total_space'    => $common->byte_format(disk_total_space("/")),
                    'hdd_free_space'     => $common->byte_format(disk_total_space("/")-disk_free_space("/")),
                    'hdd_perc'           => $hddperc
                );

            return view('admin/index', compact('user', 'mt', 'usernet', 'server'));
        }else{
            return view('auth/login');
        }
    }




    /**
    * แสดง กราฟ หน้าแรก
    */
    public function getchart01()
    {
        if(Auth::check()){

            $data = DB::table( 'users' )
                    ->select( DB::raw('(select count(*) from routes where usermanage=users.id) as numroutes'), 'users.name' )
                    ->get(); 

            return $data;
            
        }else{
            return view('auth/login');
        }
    }
    




}
