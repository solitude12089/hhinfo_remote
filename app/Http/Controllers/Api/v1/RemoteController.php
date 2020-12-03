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
		$data= $request->all();
		$serverip = env('SERVER_IP');
		Log::debug(__Function__.' get Data :'.json_encode($data));
		if(!isset($data['txcode'])||!isset($data['controlip'])){
			//return;
			return response('serverip='.$serverip, 200)
                  ->header('Content-Type', 'text/plain');
		}
		$device = \App\models\Device::where('ip',$data['controlip'])
									->orWhere('local_ip',$data['controlip'])
									->first();
		if($device===null){
			//return 'Device Miss';
			return response('serverip='.$serverip, 200)
                  ->header('Content-Type', 'text/plain');
		}
		$device->touch();
		if($data['txcode']=='9999099990'){
			return response('serverip='.$serverip, 200)
                  ->header('Content-Type', 'text/plain');
		}

		$e_swipe_event = SysLog::log('normal',$device->group_id,'swipe event',0,$device->id,$data['txcode']);
		$customer = \App\models\Customer::where('card_uuid',$data['txcode'])
										->where('status',1)
										->first();
		if($customer===null){
			//return 'Customer Miss';
			return response('serverip='.$serverip, 200)
                  ->header('Content-Type', 'text/plain');
		}

		$e_swipe_card = SysLog::log('normal',$device->group_id,'swipe card',$customer->id,$device->id,$e_swipe_event->id);
		
		//Check White list
		$spcard = \App\models\Spcard::where('customer_id',$customer->id)->first();
		if($spcard!=null){
			if(in_array($device->family,$spcard->family)&&$device->group_id ==$spcard->group_id){
				SysLog::log('normal',$device->group_id,'swipe return',$customer->id,$device->id,$e_swipe_event->id,'全區卡開門');
				return $this->opendoor($device);
			}
		}


		//Check bookongs
		$searchDevice = [];
		if($device->type=='公用鐵捲門'){
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
			SysLog::log('normal',$device->group_id,'swipe return',$customer->id,$device->id,$e_swipe_event->id,'租借時段開門');
			return $this->opendoor($device);
		}
		else{
			//過時間關鐵捲門
			if($device->type=='公用鐵捲門'||$device->type=='鐵捲門'){
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

	public function operdo(Request $request){
		$data= $request->all();
		Log::debug(__Function__.' get Data :'.json_encode($data));
	}

	public function opendoor($device){
		$serverip = env('SERVER_IP');
		if($device->type=='鐵捲門'||$device->type=='公用鐵捲門')
		{
			$opentime='1';
			$job = (new \App\Jobs\responseJob($device->id))->delay(2);
			dispatch($job);
		}
		else{
			$opentime='4';
		}
			
		$gateno ='1';
		$rt='serverip='.$serverip."&gateno=".$gateno."&opentime=".$opentime;
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
	


}



?>
