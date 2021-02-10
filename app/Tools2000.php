<?php

namespace App;
use App\SysLog;
use Auth;
use Log;
class Tools2000 
{

    function httpGet($ip,$path){
        try{
            Log::debug(__Function__.' send Data : '.$ip.$path);

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

            Log::debug(__Function__.' return Data : '.json_encode($rt_data));

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
        // dd($command);
        //$command = '&rlno=3&action=5';
        // $command = '&rlno=1&action=2&wait=0&rlno=2&action=2&wait=2&rlno=3&action=2&wait=4&rlno=4&action=2&wait=6&rlno=1&action=2&wait=8&rlno=2&action=2&wait=10&rlno=3&action=2&wait=12&rlno=4&action=2&wait=14';
        $path = '/api/v2/remote/rcode?token='.$token.$command.'&serverip='.$serverip;
        //dd($path);
        $rt = $this->httpGet($ip,$path);
        if($rt['result']==1 && isset($rt['data'])){
           
            $data = $rt['data'];
			$r1 = isset($data['r1status'])?$data['r1status']:"";
			$r2 = isset($data['r2status'])?$data['r2status']:"";
			$r3 = isset($data['r3status'])?$data['r3status']:"";
			$r4 = isset($data['r4status'])?$data['r4status']:"";
			$s1 = isset($data['s1status'])?$data['s1status']:"";
			$s2 = isset($data['s2status'])?$data['s2status']:"";
			$s3 = isset($data['s3status'])?$data['s3status']:"";
			$s4 = isset($data['s4status'])?$data['s4status']:"";
			$s5 = isset($data['s5status'])?$data['s5status']:"";
            $s6 = isset($data['s6status'])?$data['s6status']:"";
            
            $device->update([
				'r1' => $r1,
				'r2' => $r2,
				'r3'  => $r3,
				'r4'  => $r4,
				's1'  => $s1,
				's2'  => $s2,
				's3'  => $s3,
				's4'  => $s4,
				's5'  => $s5,
				's6'  => $s6
			]);
        }
       


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
        $now = date('YmdHis');
        //$now = date('YmdHis', strtotime(date('YmdHis') . ' -1 day'));
        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        $date = date('YmdHis',strtotime($now));
      
        
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);
        $serverip = env('SERVER_IP');
        $dateW = date('Ymd',strtotime($now)).'0'.date('w',strtotime($now)).date('His',strtotime($now));
       
        $path = '/api/v2/remote/get?settime='.$dateW.'&serverip='.$serverip;
        //dd($path);
        $rt = $this->httpGet($ip,$path);
        if($rt['result']==1 && isset($rt['data'])){
           
            $data = $rt['data'];
			$r1 = isset($data['r1status'])?$data['r1status']:"";
			$r2 = isset($data['r2status'])?$data['r2status']:"";
			$r3 = isset($data['r3status'])?$data['r3status']:"";
			$r4 = isset($data['r4status'])?$data['r4status']:"";
			$s1 = isset($data['s1status'])?$data['s1status']:"";
			$s2 = isset($data['s2status'])?$data['s2status']:"";
			$s3 = isset($data['s3status'])?$data['s3status']:"";
			$s4 = isset($data['s4status'])?$data['s4status']:"";
			$s5 = isset($data['s5status'])?$data['s5status']:"";
            $s6 = isset($data['s6status'])?$data['s6status']:"";
            
            $device->update([
				'r1' => $r1,
				'r2' => $r2,
				'r3'  => $r3,
				'r4'  => $r4,
				's1'  => $s1,
				's2'  => $s2,
				's3'  => $s3,
				's4'  => $s4,
				's5'  => $s5,
				's6'   => $s6
			]);
        }
        return $rt;
    }
    


}
