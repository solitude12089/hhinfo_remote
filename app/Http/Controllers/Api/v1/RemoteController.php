<?php 
namespace App\Http\Controllers\Api\v1;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Log;


use DatePeriod;
use DateInterval;
use \App\SysLog;
class RemoteController extends Controller {

	
	public function dcode(Request $request){
		try{
		
		
			$data= $request->all();
		
			$serverip = env('SERVER_IP');
			Log::debug(__Function__.' get Data :'.json_encode($data));
			if(!isset($data['txcode'])||!isset($data['controlip'])){
				return; 
			}
		
			$senser1 = isset($data['s1status'])?$data['s1status']:1;
			$device = \App\models\Device::where('ip',$data['controlip'])
										->orWhere('local_ip',$data['controlip'])
										->first();
			if($device===null){
				//return 'Device Miss';
				return response('serverip='.$serverip, 200)
					->header('Content-Type', 'text/plain');
			}


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

			if($data['txcode']=='9999099990'){
				return response('serverip='.$serverip, 200)
					->header('Content-Type', 'text/plain');
			}

			$e_swipe_event = SysLog::log('normal',$device->group_id,'swipe event',0,$device->id,$data['txcode']);
			
			$customer = null;
			if(strlen($data['txcode'])==12 &&  substr($data['txcode'], -4) == date('md')){
				//QRCODE
				$customer = \App\models\Customer::where('phone_uuid',substr($data['txcode'],0,strlen($data['txcode'])-4 ))
														->where('status',1)
														->first();
			}
			else{
				$cards = \App\models\Card::where('card_uuid',$data['txcode'])->first();
				if($cards===null){
					//return 'Customer Miss';
					return response('serverip='.$serverip, 200)
						->header('Content-Type', 'text/plain');
				}
				$customer = $cards->customer;
			}

			
			
			if($customer==null||$customer->status!=1){
				//return 'Customer Miss';
				return response('serverip='.$serverip, 200)
					->header('Content-Type', 'text/plain');
			}

			$e_swipe_card = SysLog::log('normal',$device->group_id,'swipe card',$customer->id,$device->id,$e_swipe_event->id);
			
			//Check White list
			$spcard = \App\models\Spcard::where('customer_id',$customer->id)->first();
			
			if($spcard!=null){
				if(in_array($device->family,$spcard->family)&&$device->group_id ==$spcard->group_id){
					return $this->opendoor($device,$senser1,$customer->id,$e_swipe_event->id,'全區卡',$spcard);
				}
			}



			//Check bookongs
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
			$toDay = date('Y-m-d');
			$booking = \App\models\BookingHistory::where('date',$toDay)
													->where('range_id',$nowRanges)
													->where('status',1)
													->where('customer_id',$customer->id)
													->whereIn('device_id',$searchDevice)
													->get()->count();
			if($booking>0){
				// SysLog::log('normal',$device->group_id,'swipe return',$customer->id,$device->id,$e_swipe_event->id,'租借時段開門');
				return $this->opendoor($device,$senser1,$customer->id,$e_swipe_event->id,'租借時段');
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
						SysLog::log('normal',$device->group_id,'swipe return',$customer->id,$device->id,$e_swipe_event->id,'超時關門');
						return $this->closedoor();
					}
				}
				return response('serverip='.$serverip, 200)
					->header('Content-Type', 'text/plain');
			}
		}
		catch(\Exception $e){
			dd($e);
		}
	}

	public function operdo(Request $request){
		$data= $request->all();
		Log::debug(__Function__.' get Data :'.json_encode($data));
	}

	public function opendoor($device,$senser,$customer_id,$e_swipe_event_id,$mode,$spcard = null){
	
		$serverip = env('SERVER_IP');
		if($device->type=='鐵捲門')
		{
			$opentime='1';
			if($senser==1){
				SysLog::log('normal',$device->group_id,'swipe return',$customer_id,$device->id,$e_swipe_event_id,$mode.'-開門');
				$gateno ='1';
			}else{
				SysLog::log('normal',$device->group_id,'swipe return',$customer_id,$device->id,$e_swipe_event_id,$mode.'-關門');
				$gateno ='2';
			}

		}
		else{
			SysLog::log('normal',$device->group_id,'swipe return',$customer_id,$device->id,$e_swipe_event_id,$mode.'-開門');
			$gateno ='1';
			$opentime='4';
		
		}
		$rt='serverip='.$serverip."&gateno=".$gateno."&opentime=".$opentime;
		
		if($spcard!=null && count($spcard->authority)!=0){
			foreach($spcard->authority as $ak => $av){
				$rt = $rt."&gateno=".$av."&opentime=255";
			}
		}

		// $rt='serverip='.$serverip."&gateno=".$gateno."&opentime=".$opentime;
		return response($rt, 200)
			->header('Content-Type', 'text/plain');
	}

	public function closedoor(){
		$serverip = env('SERVER_IP');
		$opentime='1';
		$gateno ='2';
		$rt='serverip='.$serverip."&gateno=".$gateno."&opentime=".$opentime;
		return response($rt, 200)
			->header('Content-Type', 'text/plain');
	}

	public function scode(Request $request){
		$data = $request->all();
		Log::debug(__Function__.' get Data :'.json_encode($data));
		$serverip = env('SERVER_IP');
		if(!isset($data['controlip'])){
			return; 
		}
		$device = \App\models\Device::where('ip',$data['controlip'])
									->orWhere('local_ip',$data['controlip'])
									->first();
		if($device===null){
			//return 'Device Miss';
			return response('serverip='.$serverip, 200)
				->header('Content-Type', 'text/plain');
		}
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
		
		return response('serverip='.$serverip, 200)
                  ->header('Content-Type', 'text/plain');
	}
	


}



?>
