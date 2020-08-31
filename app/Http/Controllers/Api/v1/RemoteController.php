<?php 
namespace App\Http\Controllers\Api\v1;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Log;


use DatePeriod;
use DateInterval;
class RemoteController extends Controller {

	
	public function dcode(Request $request){
		$data= $request->all();
		Log::debug(__Function__.' get Data :'.json_encode($data));

		if(!isset($data['txcode'])||!isset($data['controlip'])){
			return;
		}
		// https://discordapp.com/api/webhooks/749858296663375923/f_3Ncxe9ZHkv4CJzUpiQQN3QA8dfywW-S4CAOXOEPk9I3a8oVNla7EvWcvrHUcvFxILc
		$device = \App\models\Device::where('ip',$data['controlip'])->first();
		if($device===null){
			return;
		}
		// $driver->synced_at = date('Y-m-d H:i:s');
		$device->touch();



		if($data['txcode']!='9999099990'){
			$serverip = env('SERVER_IP');
			$gateno ='1';
			$opentime='4';

			$rt='serverip='.$serverip."&gateno=".$gateno."&opentime=".$opentime;
			return response($rt, 200)
                  ->header('Content-Type', 'text/plain');

		}
		return response('', 200)
                  ->header('Content-Type', 'text/plain');
		return;

		dd($data);
	}

	public function operdo(Request $request){
		$data= $request->all();
		Log::debug(__Function__.' get Data :'.json_encode($data));
	}




	function httpGet($url){
        $result = "";
        $res="";
		$fp = fsockopen('220.128.141.136', 4661, $errno, $errstr);
		$header = "Get " . $url . " HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n"; 
		
		
		fputs($fp, $header, strlen($header));
		while (!feof($fp)) {
			$res .= fgets($fp, 1024);
		}
		fclose($fp);
		$strArray = explode("\r\n\r\n", $res);
		$result = $strArray[1];
        return $result;
    }

	public function api1(){
		$server = 'http://220.128.141.136:4661';
        $date = date('YmdHis');
        $dateW = date('Ymd').'0'.date('w').date('His');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);

       	$api1 = '/api/v2/remote/get?token='.$token.'&getr=all&serverip=114.35.246.11';
        $api2 = $server.'/api/v2/remote/rcode?token='.$token.'&rlno=3&action=4&rlno=2&action=255&serverip=114.35.246.11';
        $api3 = $server.'/api/v2/remote/get?gettime=0&serverip=114.35.246.115';
        $api4 = $server.'/api/v2/remote/get?settime='.$dateW.'&serverip=114.35.246.115';
    	
    	$post_url = $api1;
     	echo "Request URL : ".$post_url.'<br>';
        echo "Token : ".$tokenO.'<br>';
        $rt = $this->httpGet($post_url);
        echo "Response : <br>";
        echo $rt;
	}

	public function api2(){
		$server = 'http://220.128.141.136:4661';
        $date = date('YmdHis');
        $dateW = date('Ymd').'0'.date('w').date('His');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);

       	$api1 = $server.'/api/v2/remote/get?token='.$token.'&getr=all&serverip=114.35.246.11';
        $api2 = $server.'/api/v2/remote/rcode?token='.$token.'&rlno=1&action=4&rlno=2&action=0&rlno=3&action=255&rlno=4&action=255&serverip=114.35.246.11';
        $api3 = $server.'/api/v2/remote/get?gettime=0&serverip=114.35.246.115';
        $api4 = $server.'/api/v2/remote/get?settime='.$dateW.'&serverip=114.35.246.115';
    	
    	$post_url = $api2;
     	echo "Request URL : ".$post_url.'<br>';
        echo "Token : ".$tokenO.'<br>';
        $rt = $this->httpGet($post_url);
        echo "Response : <br>";
        echo $rt;
		
	}

	public function api3Get(){
		
		$tools= new \App\ToolsDiscord;
		$tools->push('ZZZ');
		dd('Z');

		$server = 'http://220.128.141.136:4661';
        $date = date('YmdHis');
        $dateW = date('Ymd').'0'.date('w').date('His');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);

       	$api1 = $server.'/api/v2/remote/get?token='.$token.'&getr=all&serverip=114.35.246.11';
        $api2 = $server.'/api/v2/remote/rcode?token='.$token.'&rlno=3&action=4&rlno=2&action=255&serverip=114.35.246.11';
        $api3 = $server.'/api/v2/remote/get?gettime=0&serverip=114.35.246.115';
        $api4 = $server.'/api/v2/remote/get?settime='.$dateW.'&serverip=114.35.246.115';
    	
    	$post_url = $api3;
     	echo "Request URL : ".$post_url.'<br>';
        echo "Token : ".$tokenO.'<br>';
        $rt = $this->httpGet($post_url);
        echo "Response : <br>";
        echo $rt;
      
	}

	public function api3Set(){
		$server = 'http://220.128.141.136:4661';
        $date = date('YmdHis');
        $dateW = date('Ymd').'0'.date('w').date('His');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);

       	$api1 = $server.'/api/v2/remote/get?token='.$token.'&getr=all&serverip=114.35.246.11';
        $api2 = $server.'/api/v2/remote/rcode?token='.$token.'&rlno=3&action=4&rlno=2&action=255&serverip=114.35.246.11';
        $api3 = $server.'/api/v2/remote/get?gettime=0&serverip=114.35.246.115';
        $api4 = $server.'/api/v2/remote/get?settime='.$dateW.'&serverip=114.35.246.115';
    	
    	$post_url = $api4;
     	echo "Request URL : ".$post_url.'<br>';
        echo "Token : ".$tokenO.'<br>';
        $rt = $this->httpGet($post_url);
        echo "Response : <br>";
        echo $rt;
	}

}



?>
