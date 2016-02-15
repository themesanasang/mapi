<?php 
namespace App;

use DB;
use Routeros_api;
 
class Common{


    /**
    * check MT Online
    */
    public function checkOnlineMT($mtip, $mtusername, $mtpassword)
    {
        $API = new \App\routeros_api(); 
        $API->debug = false;              

        // login mt
        if ($API->connect($mtip, $mtusername, $mtpassword)) {    
            $API->disconnect();             
            return true; //online
        }else{
            return false; //no on
        }   
    }

    /**
    * uptime MT to show
    */
    public function UptimeInSeconds($uptime) {
        $mark1=strpos($uptime, "d"); 
        $days= (substr($uptime, 0, $mark1)); 
   
        if(strlen($days) > 2){
            $d=explode("w", $days);
            $days = ($d[0]*7)+$d[1];
        }else{
            $days = substr($days, 0, 1);
        }
       
        if ($mark1) $uptime=substr($uptime, $mark1 + 1); 
        
        $mark1=strpos($uptime, "h");
        $hours=substr($uptime, 0, $mark1); 
        if ($mark1) $uptime=substr($uptime, $mark1 + 1); 

        $mark1=strpos($uptime, "m");
        $mins=substr($uptime, 0, $mark1); 
        
        if( $days == '' ) $days = 0;
        if( $hours == '' ) $hours = 0;
        
        $result = "$days วัน. $hours ชั่วโมง.";

        return $result;
    } 


    /**
     * [conv_date description]
     * @param  [type] $amount [description]
     * @return [type]         [description]
     */
    public static function conv_date($amount)
    {    
        $month = date('F');
        $day = date('j');
        $year = date('Y');
        $time = '24:00:00';
        $date = mktime(0,0,0, date('m'), $day+$amount, $year);
        
        $out['radcheck'] = date("F j Y", $date)." ".$time;
        $out['radreply'] = date("Y-n-j", $date)."T".$time;
        
        return $out;    
    }





    /**
     * [datethai description]
     * @param  [type] $strDate [description]
     * @return [type]          [description]
     */
    public static function  datethai($strDate)
    {
        $strYear = date("Y",strtotime($strDate))+543;
        $strMonth= date("n",strtotime($strDate));
        $strDay= date("j",strtotime($strDate));
        $strHour= date("H",strtotime($strDate));
        $strMinute= date("i",strtotime($strDate));
        $strSeconds= date("s",strtotime($strDate));
        $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        $strMonthThai=$strMonthCut[$strMonth];
        return "$strDay $strMonthThai $strYear เวลา  $strHour:$strMinute:$strSeconds";
    }

    public static function  datethai22($strDate)
    {
        $strYear = date("Y",strtotime($strDate))+543;
        $strMonth= date("n",strtotime($strDate));      
        $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        $strMonthThai=$strMonthCut[$strMonth];
        return " เดือน $strMonthThai $strYear";
    }

    public static function  monththai22($m)
    {
        $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        $strMonthThai=$strMonthCut[$m];
        return $strMonthThai;
    }





    /**
     * [thaidate description]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function  thaidate($str) 
    {
        $str = preg_replace('/24:00:00/', '', $str);
        
        $m = substr($str, 0, 3);
        $d = substr($str, 4, 2);
        $y = substr($str, 7, 4) + 543;
        $month = array("January"=>"ม.ค.", "February"=>"ก.พ.", "March"=>"มี.ค", "April"=>"เม.ย.", "May"=>"พ.ค.", "June"=>"มิ.ย.", "July"=>"ก.ค.", "August"=>"ส.ค.", "September"=>"ก.ย.", "October"=>"ต.ค.", "November"=>"พ.ย.", "Dec"=>"ธ.ค.");
        
        return $d." ".$month[$m]." ".$y;
    }





    /**
     * [unix_to_human description]
     * @param  string  $time    [description]
     * @param  boolean $seconds [description]
     * @param  string  $fmt     [description]
     * @return [type]           [description]
     */
    public static function unix_to_human($time = '', $seconds = FALSE, $fmt = 'us')
    {
        $r  = date('Y', $time).'-'.date('m', $time).'-'.date('d', $time).' ';

        if ($fmt == 'us'){
            $r .= date('h', $time).':'.date('i', $time);
        }
        else{
            $r .= date('H', $time).':'.date('i', $time);
        }

        if ($seconds){
            $r .= ':'.date('s', $time);
        }

        if ($fmt == 'us'){
            $r .= ' '.date('A', $time);
        }

        return $r;
    }






    /**
     * [random_string description]
     * @param  string  $type [description]
     * @param  integer $len  [description]
     * @return [type]        [description]
     */
    public static function random_string($type = 'alnum', $len = 8)
    {
        switch($type){
            case 'basic'    : return mt_rand();
                break;
            case 'alnum'    :
            case 'numeric'  :
            case 'nozero'   :
            case 'alpha'    :

                    switch ($type){
                        case 'alpha'    :   $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            break;
                        case 'alnum'    :   $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            break;
                        case 'numeric'  :   $pool = '0123456789';
                            break;
                        case 'nozero'   :   $pool = '123456789';
                            break;
                    }

                    $str = '';
                    for ($i=0; $i < $len; $i++){
                        $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
                    }
                    return $str;
                break;
            case 'unique'   :
            case 'md5'      :

                        return md5(uniqid(mt_rand()));
                break;
            case 'encrypt'  :
            case 'sha1' :

                        $CI =& get_instance();
                        $CI->load->helper('security');

                        return do_hash(uniqid(mt_rand(), TRUE), 'sha1');
                break;
        }
    }





    /**
     * [byte_format description]
     * @param  [type] $num [description]
     * @return [type]      [description]
     */
    public static function byte_format($num)
    { 
        if ($num >= 1000000000000) {
            $num = round($num / 1099511627776, 1);
            $unit = 'TB';
        }
        elseif ($num >= 1000000000) {
            $num = round($num / 1073741824, 1);
            $unit = 'GB';
        }
        elseif ($num >= 1000000) {
            $num = round($num / 1048576, 1);
            $unit = 'MB';
        }
        elseif ($num >= 1000) {
            $num = round($num / 1024, 1);
            $unit = 'KB';
        }
        else{
            $unit = 'Bytes';
            return number_format($num).' '.$unit;
        }

        return number_format($num, 1).' '.$unit;
    }   





    /**
     * [time_data description]
     * @param  [type] $time [description]
     * @param  [type] $fnc  [description]
     * @return [type]       [description]
     */
    public static function time_data($time,$fnc)
    {            
        $hours = ($time - ($time % 3600)) / 3600 .':';
        $time = $time - ($hours * 3600); 
             
        $mins = ($time - ($time % 60)) / 60 .':'; 
      
        $secs = $time - ($mins * 60);
        
        ($hours<10 && $hours>=0) ? $hours = "0".$hours : "";
        ($mins<10  && $mins>=0)  ? $mins  = "0".$mins  : "";
        ($secs<10  && $secs>=0)  ? $secs  = "0".$secs  : "";
        
        return $hours.''.$mins.''.$secs;
    }




 
}