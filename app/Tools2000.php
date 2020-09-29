<?php

namespace App;
use App\SysLog;
use Auth;
class Tools2000 
{

    function httpGet($ip,$path){
        try{


            $result = "";
            $res="";
            $fp = fsockopen($ip, 4661, $errno, $errstr,5);
            $header = "Get " . $path . " HTTP/1.0\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n"; 
            
            
            fputs($fp, $header, strlen($header));
            while (!feof($fp)) {
                $res .= fgets($fp, 1024);
            }
            fclose($fp);
            $strArray = explode("\r\n\r\n", $res);
            $result = $strArray[1];
            preg_match('/<body>(.*?)<\/body>/si',$result,$match);

            if(count($match)!=2){
               throw new Exception('Response format error.'.$result);
            }

            $rt_data = [];
            $sp = explode("&",$match[1]);
            foreach ($sp as $key => $value) {
                list($k, $v) = explode('=', $value);
                $arr= array($k => $v);
                $rt_data = array_merge($rt_data,$arr);
            }



            return [
                'result' => true,
                'msg' => '',
                'data' => $rt_data
            ];
           
        }
        catch (\Exception $e) {

            return [
                'result' => false,
                'msg' => $e->getMessage(),
                'ip' => $ip,
                'path' => $path
            ];

        }
        return $result;
    }

    public function sync($device){
        
    }


    public function getStatus($device_id){
        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        $date = date('YmdHis');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);
        $serverip = env('SERVER_IP');
        $path = '/api/v2/remote/get?token='.$token.'&getr=all&serverip='.$serverip;
        $rt = $this->httpGet($ip,$path);
        return $rt;


    }

    /**
	* 設定狀態
    *
    * @param  String  $device_id  DeviceID
	* @param  Array  $setData  修改資料 ex:['1'=>'255','Relay'=>'Action']
	* @return Array 
	* 
	*/
    public function setStatus($device_id,$setData){

        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        $date = date('YmdHis');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);
        $serverip = env('SERVER_IP');


        $command = '';
        foreach ($setData as $key => $value) {
            $command = $command.'&rlno='.$key.'&action='.$value;
        }

        $path = '/api/v2/remote/rcode?token='.$token.$command.'&serverip'.$serverip;
       
        $rt = $this->httpGet($ip,$path);
        $user = Auth::user();
        if($user!=null)
            SysLog::log('normal',$device->group_id,'device control',$user->id,$device->id,json_encode($setData));
        return $rt;
    }

    public function getTime($device_id){
        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        $date = date('YmdHis');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);
        $serverip = env('SERVER_IP');
        $path = '/api/v2/remote/get?gettime=0&serverip='.$serverip;
        $rt = $this->httpGet($ip,$path);
        return $rt;
    }

     public function setTime($device_id){
        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        $date = date('YmdHis');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);
        $serverip = env('SERVER_IP');
        $dateW = date('Ymd').'0'.date('w').date('His');
        $path = '/api/v2/remote/get?settime='.$dateW.'&serverip='.$serverip;
        $rt = $this->httpGet($ip,$path);
        return $rt;
    }
    


}
