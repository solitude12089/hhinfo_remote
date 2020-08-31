<?php 
namespace App\Http\Controllers\Api\v1;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Log;


use DatePeriod;
use DateInterval;
class CustomerController extends Controller {
	public function registered(Request $request){
		$data = $request->all();

		if(!isset($data['name'])||$data['name']==''){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '名子不可為空值..'
			), 200);
		}

		if(!isset($data['phone'])||$data['phone']==''){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '電話不可為空值.'
			), 200);
		}

		if(preg_match("/^09\d{8}$/",$data['phone'])!=1){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '手機號碼格式錯誤,請輸入正確格式(例如 0912345678)'
			), 200);
		}


		$loct_ts =  \App\models\TempCustomer::where('name','=',$data['name'])
										->where('phone','=',$data['phone'])
										->where('status',9)
										->whereRaw('TIMESTAMPDIFF(MINUTE,updated_at,NOW()) <= 10')
										->first();

		if($loct_ts!=null){
			return response()->json(array(
										'status' =>0, 
										'msg' => '註冊太頻繁,請10分鐘後在試.'
									), 200);
		}


		$_tc = \App\models\TempCustomer::firstOrNew(['name' =>$data['name'],
													'phone' =>$data['phone']
													]);

		if($_tc->updated_at!=null){

			$a_date = new DateTime($_tc->updated_at);
			$b_date = new DateTime();
			$diff = $b_date->diff($a_date);
			if($diff->days>0){
				$_tc->qty = 0;
			}
			if($diff->i>10){
				$_tc->qty = 0;
			}
			
		}
				
										// ->whereRaw('TIMESTAMPDIFF(MINUTE,updated_at,NOW()) <= 10')
										// ->first();
		if($_tc->qty>=3){
			$_tc->qty=0;
			$_tc->status = 9;
			$_tc->save();
			return response()->json(array(
									'status' =>0, 
									'msg' => '註冊太頻繁,請稍後在試.'
								), 200);
		}

		$_tc->qty= $_tc->qty+1;
		$_tc->status=1;
		$_tc->vcode =$this->random_string();
		$_tc->save();
		$this->sendSMS($_tc->phone,$_tc->vcode);
		return response()->json(
					array(
						'status' =>1, 
						'msg' => ''
					), 200);
		
		


		





	}

	public function verify(Request $request){
		$data = $request->all();

		if(!isset($data['name'])||$data['name']==''){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '姓名不可為空值.'
			), 200);
		}

		if(!isset($data['phone'])||$data['phone']==''){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '電話不可為空值.'
			), 200);
		}

		if(!isset($data['vcode'])||$data['phone']==''){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '驗證碼不可為空值.'
			), 200);
		}

		if(preg_match("/^09\d{8}$/",$data['phone'])!=1){
			return response()->json(
			array(
				'status' =>0, 
				'msg' => '手機號碼格式錯誤,請輸入正確格式(例如 0912345678)'
			), 200);
		}

		$_tc = \App\models\TempCustomer::where('name',$data['name'])
										->where('phone',$data['phone'])
										->where('vcode',$data['vcode'])
										->whereRaw('TIMESTAMPDIFF(MINUTE,updated_at,NOW()) <= 10')
										->first();
		if($_tc!=null){
			return response()->json(
			array(
				'status' =>1, 
				'msg' => ''
			), 200);
		}
		else{
			$customer = \App\models\Customer::firstOrNew(['phone'=>$data['phone']]);
			$customer->name = $data['name'];
			$customer->save();

			return response()->json(
			array(
				'status' =>0, 
				'msg' => '驗證失敗,請重新認證.'
			), 200);
		}
													



		





	}

	public function sendSMS($phone,$vcode){
		$sms = new \App\SMSHttp();
		$userID= env('SMS_USER');
		$password= env('SMS_PWD');
		$subject = "HHINFO registered";	//簡訊主旨，主旨不會隨著簡訊內容發送出去。用以註記本次發送之用途。可傳入空字串。
    	$content = "歡迎註冊，您的認證碼為『".$vcode."』。(密碼10分鐘有效)";	
    	$mobile = $phone;	
    	$sendTime= "";

    	$rt = $sms->sendSMS($userID,$password,$subject,$content,$mobile,$sendTime);

    	if($rt){
    		 Log::info("傳送簡訊成功. To:".$phone);
    		 return true;
    	}else{
    		Log::info("傳送簡訊失敗. To:".$phone." Error:". $sms->processMsg);
    		return false;
    	}
	

		
	}

	public function random_string($length = 4, $characters = '0123456789') {
		if (!is_int($length) || $length < 0) {
			return false;
		}
		$characters_length = strlen($characters) - 1;
		$string = '';

		for ($i = 0; $i < $length; $i++) {
			$string .= $characters[mt_rand(0, $characters_length)];
		}
		return $string;
	}




// serverip=192.168.x.x&gateno=1&opentime=1000&serialno=2020011600001

// serverip = 貴司伺服器IP

// gateno = Relay ID

// opentime = Relay 動作時間

// serialno = 控制器會依此號碼回覆伺服器



}




?>
