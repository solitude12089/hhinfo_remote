<?php

namespace App;
use App\SysLog;
use Auth;
use Log;
use GuzzleHttp\Client;
use DateTime;
class Tools2000 
{

    function httpGet($ip,$path){
        try{
            Log::debug(__Function__.' send Data : '.$ip.$path);

            $result = "";
            $res="";
            $fp = fsockopen($ip, 4661, $errno, $errstr,1);
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
            Log::debug(__Function__.' httpGet fail : '.$e->getMessage());
            return [
                'result' => false,
                'msg' => $e->getMessage(),
                'ip' => $ip,
                'path' => $path
            ];

        }
        return $result;
    }

    function httpClient_v2($ip,$method,$path,$data = null) {
        try {

			\Log::debug(__FUNCTION__.' use v2 client ip:'.$ip.' method: '.$method.' path: '.$path);
            $url = $ip.$path;
            $token = env('CLIENT_TOKEN');
            $arr['headers']['token'] = $token;
            $arr['headers']['Content-Type'] = 'application/json';
			$arr['timeout']  = 5;
            //dd($url);

			$client = new Client();
            $rt_data = null;
			switch ($method) {
                
				case 'get':
                   
					$response = $client->get($url,$arr);
					$rt_data = json_decode($response->getBody()->getContents());
                  
					break;
				case 'post':
					$arr['body'] =  json_encode($data); 
					$response = $client->post($url,$arr);
					$rt_data = json_decode($response->getBody()->getContents());
                    break;
				case 'put':
					$arr['body'] = json_encode($postData);
					$response = $client->post($url,$arr);
					$rt_data = json_decode($response->getBody()->getContents());
                    break;
			}
            return [
                'result' => true,
                'msg' => '',
                'data' => $rt_data
            ];
         
            return $rt_data;
		}
		catch (\Exception $e) {
			return [
                'result' => false,
                'msg' => $e->getMessage(),
                'ip' => $ip,
                'path' => $path
            ];
		}
    }



    public function sync($device){
        
    }

    public function transfer_newformat($data){
       // dd($data);
        if(isset($data['r1status'])){
            $new_format = [
                'controlip' => $data['controlip'],
                'relay' => (object)[
                    '1' => $data['r1status'],
                    '2' => $data['r2status'],
                    '3' => $data['r3status'],
                    '4' => $data['r4status'],
                ],
                'sensor' => (object)[
                    '1' => $data['s1status'],
                    '2' => $data['s2status'],
                    '3' => $data['s3status'],
                    '4' => $data['s4status'],
                    '5' => $data['s5status'],
                    '6' => $data['s6status'],
    
                ]
            ];
        }   
        else{
            $new_format = [
                'controlip' => $data['controlip'],
                'relay' => (object)[
                    '1' => $data['r1ststus'],
                    '2' => $data['r2ststus'],
                    '3' => $data['r3ststus'],
                    '4' => $data['r4ststus'],
                ],
                'sensor' => (object)[
                    '1' => $data['s1status'],
                    '2' => $data['s2status'],
                    '3' => $data['s3status'],
                    '4' => $data['s4status'],
                    '5' => $data['s5status'],
                    '6' => $data['s6status'],
    
                ]
            ];
        }
       
        return $new_format;
    }


    public function getStatus($device_id){

        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        $serverip = env('SERVER_IP');
        if($device->kernel=='漢軍'){
            $date = date('YmdHis');
            $tokenO = 'hhinfo:'.$date;
            $token = base64_encode($tokenO);
            $path = '/api/v2/remote/get?token='.$token.'&getr=all&serverip='.$serverip;
            $rt = $this->httpGet($ip,$path);
            if($rt['result']){
                $new_format = $this->transfer_newformat($rt['data']);
                $rt['data'] = $new_format;
            }
        }
        else{
            $path = '/api/v3/remote/control?serverip='.$serverip;
            $rt = $this->httpClient_v2($ip,'get',$path);
            if($rt['result']){
                $rt['data']=(array)$rt['data'];
            }
        }

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
        $serverip = env('SERVER_IP');
        if($device->kernel=='漢軍'){
            $date = date('YmdHis');
            $tokenO = 'hhinfo:'.$date;
            $token = base64_encode($tokenO);
            $command = '';
            foreach ($setData as $key => $value) {
                $command = $command.'&rlno='.$key.'&action='.$value;
            }
            $path = '/api/v2/remote/rcode?token='.$token.$command.'&serverip='.$serverip;
            $rt = $this->httpGet($ip,$path);
          
            if($rt['result']){
                $new_format = $this->transfer_newformat($rt['data']);
                $rt['data'] = $new_format;
            }
        }
        else{
            $set_relay = [];
            $int_key = 1;
            foreach ($setData as $key => $value) {
                $set_relay[$int_key]=[
                    'gateno' => $key,
                    'opentime' => (int)$value,
                    'waittime' => 0
                ];
                $int_key ++;
            }
            $data = [
                'serverip' => $serverip,
                'relay'=> $set_relay
            ];
            $path = '/api/v3/remote/control';
            $rt = $this->httpClient_v2($ip,'post',$path,$data);
            if($rt['result']){
                $rt['data']=(array)$rt['data'];
            }
        }
     
      
        if($rt['result']==1 && isset($rt['data'])){

            $data = $rt['data'];
           
            $relays = (array)$rt['data']['relay'];
            $sensors = (array)$rt['data']['sensor'];
      
        
			$r1 = isset($relays["1"])?$relays["1"]:"";
			$r2 = isset($relays['2'])?$relays['2']:"";
			$r3 = isset($relays['3'])?$relays['3']:"";
			$r4 = isset($relays['4'])?$relays['4']:"";
			$s1 = isset($sensors['1'])?$sensors['1']:"";
			$s2 = isset($sensors['2'])?$sensors['2']:"";
			$s3 = isset($sensors['3'])?$sensors['3']:"";
			$s4 = isset($sensors['4'])?$sensors['4']:"";
			$s5 = isset($sensors['5'])?$sensors['5']:"";
            $s6 = isset($sensors['6'])?$sensors['6']:"";
            
         
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
        $serverip = env('SERVER_IP');
        if($device->kernel=='漢軍'){
            $date = date('YmdHis');
            $tokenO = 'hhinfo:'.$date;
            $token = base64_encode($tokenO);
           
            $path = '/api/v2/remote/get?gettime=0&serverip='.$serverip;
            $rt = $this->httpGet($ip,$path);
            if($rt['result']){
             
                $format_time = substr($rt['data']['nowtime'],0,8).substr($rt['data']['nowtime'],10,6);
                $format_time = new DateTime($format_time);
                $format_time = $format_time->format('Y-m-d H:i:s');
                $rt['data']=[
                    'controlip' =>  $rt['data']['controlip'],
                    'nowtime' => $format_time
                ];
               
            }
        }
        else{
            $path = '/api/v3/remote/control/time?serverip='.$serverip;
            $rt = $this->httpClient_v2($ip,'get',$path);
            if($rt['result']){
                $t = $rt['data']->Year.'-'.
                    $rt['data']->Month.'-'.
                    $rt['data']->Day.' '.
                    $rt['data']->Hour.':'.
                    $rt['data']->Minute.':'.
                    $rt['data']->Second;
                
             
                $rt['data']=[
                    'controlip' =>  $rt['data']->controlip,
                    'nowtime' => $t
                ];
            }
        }
        
        return $rt;
    }

     public function setTime($device_id){
        $now = date('YmdHis');
        //$now = '20210407145050';
        $serverip = env('SERVER_IP');
        $device = \App\models\Device::where('id',$device_id)->first();
        $ip = $device->ip;
        if($device->kernel=='漢軍'){
            $date = date('YmdHis',strtotime($now));
            $tokenO = 'hhinfo:'.$date;
            $token = base64_encode($tokenO);
            $dateW = date('Ymd',strtotime($now)).'0'.date('w',strtotime($now)).date('His',strtotime($now));
            $path = '/api/v2/remote/get?settime='.$dateW.'&serverip='.$serverip;
            $rt = $this->httpGet($ip,$path);
        }
        else{
            $data = [
                'serverip' => $serverip,
                'Year'=> substr($now,0,4),
                'Month'=> substr($now,4,2),
                'Day'=> substr($now,6,2),
                'Hour'=> substr($now,8,2),
                'Minute'=> substr($now,10,2),
                'Second'=> substr($now,12,2),

            ];
            $path = '/api/v3/remote/control/time';
           
           
            $rt = $this->httpClient_v2($ip,'post',$path,$data);
            if($rt['result']){
                $rt['data']=(array)$rt['data'];
            }

          
        }
        // dd($rt);


        // if($rt['result']==1 && isset($rt['data'])){
           
        //     $data = $rt['data'];
		// 	$r1 = isset($data['r1status'])?$data['r1status']:"";
		// 	$r2 = isset($data['r2status'])?$data['r2status']:"";
		// 	$r3 = isset($data['r3status'])?$data['r3status']:"";
		// 	$r4 = isset($data['r4status'])?$data['r4status']:"";
		// 	$s1 = isset($data['s1status'])?$data['s1status']:"";
		// 	$s2 = isset($data['s2status'])?$data['s2status']:"";
		// 	$s3 = isset($data['s3status'])?$data['s3status']:"";
		// 	$s4 = isset($data['s4status'])?$data['s4status']:"";
		// 	$s5 = isset($data['s5status'])?$data['s5status']:"";
        //     $s6 = isset($data['s6status'])?$data['s6status']:"";
            
        //     $device->update([
		// 		'r1' => $r1,
		// 		'r2' => $r2,
		// 		'r3'  => $r3,
		// 		'r4'  => $r4,
		// 		's1'  => $s1,
		// 		's2'  => $s2,
		// 		's3'  => $s3,
		// 		's4'  => $s4,
		// 		's5'  => $s5,
		// 		's6'   => $s6
		// 	]);
        // }
        return $rt;
    }
    


}
