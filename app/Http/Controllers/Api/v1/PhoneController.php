<?php 
namespace App\Http\Controllers\Api\v1;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Log;
use Exception;
use \App\SysLog;
use DatePeriod;
use DateInterval;
class PhoneController extends Controller {

	public function __construct()
    {
        $this->normal_btnMap = [
			'btn_r1_on' => '鐵捲門-開',
			'btn_r2_on'=> '鐵捲門-關',
			'btn_r3_on'=> '一般用電-開',
			'btn_r3_off' =>'一般用電-關',
			'btn_r4_on'=> '冷氣用電-開',
			'btn_r4_off' =>'冷氣用電-關',
		];

		$this->iron_btnMap = [
			'btn_r1_on' => '一般門-開',
			'btn_r2_on'=> '一般門-關',
			'btn_r3_on'=> '一般用電-開',
			'btn_r3_off' =>'一般用電-關',
			'btn_r4_on'=> '冷氣用電-開',
			'btn_r4_off' =>'冷氣用電-關',
		];
	}
	
	public function test(){
		return view('test.index');
	}

	public function registered(Request $request){
		try{
			$data = $request->all();
			if(!isset($data['name'])||$data['name']==''){
				throw new Exception('姓名不可為空值.');
			}
	
			if(!isset($data['phone'])||$data['phone']==''){
				throw new Exception('電話不可為空值.');
			}
	
			if(preg_match("/^09\d{8}$/",$data['phone'])!=1){
				throw new Exception('手機號碼格式錯誤,請輸入正確格式(例如 0912345678).');
			}
	
	
			$loct_ts =  \App\models\TempCustomer::where('name','=',$data['name'])
											->where('phone','=',$data['phone'])
											->where('status',9)
											->whereRaw('TIMESTAMPDIFF(MINUTE,updated_at,NOW()) <= 10')
											->first();
	
			if($loct_ts!=null){
				throw new Exception('註冊太頻繁,請10分鐘後在試.');
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
			if($_tc->qty>=3){
				$_tc->qty=0;
				$_tc->status = 9;
				$_tc->save();
				throw new Exception('註冊太頻繁,請10分鐘後在試.');
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
		catch(Exception $e){
			return response()->json(array(
				'status' =>0, 
				'msg' => $e->getMessage()
			), 200);
		}
	}

	public function verify(Request $request){
		try{
			$data = $request->all();

			if(!isset($data['name'])||$data['name']==''){
				throw new Exception('姓名不可為空值.');
			}

			if(!isset($data['phone'])||$data['phone']==''){
				throw new Exception('電話不可為空值.');
			}

			if(!isset($data['vcode'])||$data['vcode']==''){
				throw new Exception('驗證碼不可為空值.');
			}

			if(preg_match("/^09\d{8}$/",$data['phone'])!=1){
				throw new Exception('手機號碼格式錯誤,請輸入正確格式(例如 0912345678).');
			}

			$_tc = \App\models\TempCustomer::where('name',$data['name'])
											->where('phone',$data['phone'])
											->where('vcode',$data['vcode'])
											->whereRaw('TIMESTAMPDIFF(MINUTE,updated_at,NOW()) <= 10')
											->first();
			if($_tc!=null){
				$uuid =str_pad(rand(1,99999999),8,"0",STR_PAD_LEFT);
				// $s = str_pad(rand(1,99999999),8,"0",STR_PAD_LEFT);
				// $uuid = md5(uniqid(rand()));
				$customer = \App\models\Customer::firstOrNew(['phone'=>$data['phone']]);
				$customer->name = $data['name'];
				$customer->phone_uuid = $uuid;
				$customer->save();

				return response()->json(
				array(
					'status' =>1,
					'uuid' => $uuid,
					'msg' => ''
				), 200);
			}
			else{
				throw new Exception('驗證失敗,請重新認證.');
			}
						
		}
		catch(Exception $e){
			return response()->json(array(
					'status' =>0, 
					'msg' => $e->getMessage()
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
		Log::debug(__Function__.' 帳號:'.$userID.' ,密碼:'.$password);
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


	public function menu(Request $request){
		try{
			$data = $request->all();
			\Log::debug(__CLASS__.' '.json_encode($data));
			if(!isset($data['uuid'])||$data['uuid']==''){
				throw new Exception('請先註冊登入.');
			}
			$customer = \App\models\Customer::where('phone_uuid',$data['uuid'])->first();
			if($customer===null){
				throw new Exception('找不到該使用者.');
			}
			$rt_menu = [];
			$check_repeat = [];
			$spcard = \App\models\Spcard::where('customer_id',$customer->id)->first();
			if($spcard!=null){
				if(count($spcard->family)!=0){
					foreach($spcard->family as $fk => $family){
						$devices = \App\models\Device::where('status',1)
													->where('family',$family)
													->get();
						if(count($devices)!==0){
							foreach($devices as $dk => $device){
								if($device->type=='一般'){
									if(in_array($device->id,$check_repeat)){
										continue;
									}
									$rt_menu[] = [
										'device_id' => $device->id,
										'display_name' => $device->family.'-'.$device->name,
										'menu' =>[
											(object)[
												'id' => 'btn_r1_on',
												'name' => $this->normal_btnMap['btn_r1_on']
											],
											(object)[
												'id' => 'btn_r2_on',
												'name' => $this->normal_btnMap['btn_r2_on']
											],
											(object)[
												'id' => 'btn_r3_on',
												'name' => $this->normal_btnMap['btn_r3_on']
											],
											(object)[
												'id' => 'btn_r3_off',
												'name' => $this->normal_btnMap['btn_r3_off']
											],
											(object)[
												'id' => 'btn_r4_on',
												'name' => $this->normal_btnMap['btn_r4_on']
											],
											(object)[
												'id' => 'btn_r4_off',
												'name' => $this->normal_btnMap['btn_r4_off']
											]
										]
									];
									$check_repeat[]=$device->id;
								}
								else{
									if(in_array($device->id,$check_repeat)){
										continue;
									}
									$rt_menu[] = [
										'device_id' => $device->id,
										'display_name' => $device->family.'-'.$device->name,
										'menu' =>[
											(object)[
												'id' => 'btn_r1_on',
												'name' => $this->iron_btnMap['btn_r1_on']
											],
											(object)[
												'id' => 'btn_r2_on',
												'name' => $this->iron_btnMap['btn_r2_on']
											],
											(object)[
												'id' => 'btn_r3_on',
												'name' => $this->iron_btnMap['btn_r3_on']
											],
											(object)[
												'id' => 'btn_r3_off',
												'name' => $this->iron_btnMap['btn_r3_off']
											],
											(object)[
												'id' => 'btn_r4_on',
												'name' => $this->iron_btnMap['btn_r4_on']
											],
											(object)[
												'id' => 'btn_r4_off',
												'name' => $this->iron_btnMap['btn_r4_off']
											]
										]
									];
									$check_repeat[]=$device->id;
								}
								
							}
						}
					}
				}
			}
			$nowRanges = date('H');
			$toDay = date('Y-m-d');
			$bookings = \App\models\BookingHistory::with('device')
												->where('date',$toDay)
												->where('range_id',$nowRanges)
												->where('status',1)
												->where('customer_id',$customer->id)
												->get();
			if(count($bookings)>0){
				foreach ($bookings as $bk => $booking){
					$device = $booking->device;
					if($device->type=='一般'){
						if(in_array($device->id,$check_repeat)){
							continue;
						}
						$tmp_menu = [
							(object)[
								'id' => 'btn_r1_on',
								'name' => $this->normal_btnMap['btn_r1_on']
							],
							(object)[
								'id' => 'btn_r2_on',
								'name' => $this->normal_btnMap['btn_r2_on']
							],
							(object)[
								'id' => 'btn_r3_on',
								'name' => $this->normal_btnMap['btn_r3_on']
							],
							(object)[
								'id' => 'btn_r3_off',
								'name' => $this->normal_btnMap['btn_r3_off']
							],
							
						];
						if($booking->aircontrol==1){
							$tmp_menu[]=(object)[
								'id' => 'btn_r4_on',
								'name' => $this->normal_btnMap['btn_r4_on']
							];
							$tmp_menu[]=(object)[
								'id' => 'btn_r4_off',
								'name' => $this->normal_btnMap['btn_r4_off']
							];
						}

						$rt_menu[] = [
							'device_id' => $device->id,
							'display_name' => $device->family.'-'.$device->name,
							'menu' => $tmp_menu
						];
						$check_repeat[]=$device->id;
					}
					else{
						if(in_array($device->id,$check_repeat)){
							continue;
						}
						$tmp_menu = [
							(object)[
								'id' => 'btn_r1_on',
								'name' => $this->iron_btnMap['btn_r1_on']
							],
							(object)[
								'id' => 'btn_r2_on',
								'name' => $this->iron_btnMap['btn_r2_on']
							],
							(object)[
								'id' => 'btn_r3_on',
								'name' => $this->iron_btnMap['btn_r3_on']
							],
							(object)[
								'id' => 'btn_r3_off',
								'name' => $this->iron_btnMap['btn_r3_off']
							],
							
						];
						if($booking->aircontrol==1){
							$tmp_menu[]=(object)[
								'id' => 'btn_r4_on',
								'name' => $this->iron_btnMap['btn_r4_on']
							];
							$tmp_menu[]=(object)[
								'id' => 'btn_r4_off',
								'name' => $this->iron_btnMap['btn_r4_off']
							];
						}
						$rt_menu[] = [
							'device_id' => $device->id,
							'display_name' => $device->family.'-'.$device->name,
							'menu' =>$tmp_menu
						];
						$check_repeat[]=$device->id;
					}
				}
			}

			//過時間關鐵捲門
			
			$over_bookings = \App\models\BookingHistory::with('device')
											->where('date',$toDay)
											->where('range_id','<',$nowRanges)
											->where('status',1)
											->where('customer_id',$customer->id)
											->get();
			if(count($over_bookings)>0){
				foreach ($over_bookings as $obk => $obooking){
					if($obooking->device->type=='鐵捲門'){
						if(in_array($device->id,$check_repeat)){
							continue;
						}
						$rt_menu[] = [
							'device_id' => $booking->device->id,
							'display_name' => $booking->device->family.'-'.$booking->device->name,
							'menu' =>[
								(object)[
									'id' => 'btn_r2_on',
									'name' => $this->iron_btnMap['btn_r2_on']
								],
							]
						];
						$check_repeat[]=$device->id;
					}
				}
			}
			
		
			
			return response()->json(
				array(
					'status' =>1,
					'msg' => '',
					'data' => $rt_menu
				), 200);


		}
		catch(Exception $e){
			return response()->json(array(
				'status' =>0, 
				'msg' => $e->getMessage()
			), 200);
		}
		
	}


	public function btnclick(Request $request){
		try{
			$data = $request->all();
			Log::debug(__Function__.' get Data :'.json_encode($data));
			if(!isset($data['uuid'])||$data['uuid']==''){
				throw new Exception('請先註冊登入.');
			}
			$customer = \App\models\Customer::where('phone_uuid',$data['uuid'])->first();
			if($customer===null){
				throw new Exception('找不到該使用者.');
			}
			
			if(!isset($data['device_id'])||$data['device_id']==''){
				throw new Exception('Miss device ID.');
			}
			$device = \App\models\Device::where('id',$data['device_id'])->first();
			if($device ===null){
				throw new Exception('找不到裝置,請確認裝置ID.');
			}

			if(!isset($data['btn_name'])||$data['btn_name']==''){
				throw new Exception('Miss btn name.');
			}


			//check_authority
			$allow = false;
			$spcard = \App\models\Spcard::where('customer_id',$customer->id)->first();
			if($spcard!=null){
				if(in_array($device->family,$spcard->family)&&$device->group_id ==$spcard->group_id){
					$allow = true;
				}
			}

			
			$searchDevice = [];
			if($device->style=='公用'){
				$searchDevice = \App\models\Device::where('group_id',$device->group_id)
													->where('family',$device->family)
													->where('status',1)
													->get()
													->pluck('id')
													->toArray();
				
			}else{
				$searchDevice=[$device->id];
			}



			$nowRanges = date('H');
			$nowmin = date('i');
			if($nowmin>=30){
					$nowRanges = $nowRanges.':30';
			}else{
					$nowRanges = $nowRanges.':00';
			}

		
			$toDay = date('Y-m-d');
			$booking = \App\models\BookingHistory::where('date',$toDay)
													->where('range_id',$nowRanges)
													->where('status',1)
													->where('customer_id',$customer->id)
													->whereIn('device_id',$searchDevice)
													->get()->count();
			if($booking>0){
				$allow = true;
			}
			else{
				//過時間關鐵捲門
				if($device->type=='鐵捲門'){
					$over_booking = \App\models\BookingHistory::where('date',$toDay)
													->where('range_id','<=',$nowRanges)
													->where('status',1)
													->where('customer_id',$customer->id)
													->whereIn('device_id',$searchDevice)
													->get()->count();
					if($over_booking>0){
						$allow= true;
					}
				}
			}

			if($allow){
				$setData =[];
				switch($data['btn_name']){
					case 'btn_r1_on':
						$setData[1]="1";
					break;
					case 'btn_r1_off':
						$setData[1]="0";
					break;
					case 'btn_r2_on':
						$setData[2]="1";
					break;
					case 'btn_r2_off':
						$setData[2]="0";
					break;
					case 'btn_r3_on':
						$setData[3]="255";
					break;
					case 'btn_r3_off':
						$setData[3]="0";
					break;
					case 'btn_r4_on':
						$setData[4]="255";
					break;
					case 'btn_r4_off':
						$setData[4]="0";
					break;
				}
				$tools = new \App\Tools2000;
				if($device->type=='一般'){
					$actionName = $this->normal_btnMap[$data['btn_name']];
				}else{
					$actionName = $this->iron_btnMap[$data['btn_name']];
				}
				SysLog::log('normal',$device->group_id,'phone control',$customer->id,$device->id,0,$actionName);
				$rt = $tools->setStatus($device->id,$setData);
				return response()->json(
					array(
						'status' =>1
					), 200);
			}
			else{
				return response()->json(array(
					'status' =>0, 
					'msg' => '無操作權限.'
				), 200);
			}

		}
		catch(Exception $e){
			return response()->json(array(
				'status' =>0, 
				'msg' => $e->getMessage()
			), 200);
		}
		
	}


// serverip=192.168.x.x&gateno=1&opentime=1000&serialno=2020011600001

// serverip = 貴司伺服器IP

// gateno = Relay ID

// opentime = Relay 動作時間

// serialno = 控制器會依此號碼回覆伺服器



}




?>
