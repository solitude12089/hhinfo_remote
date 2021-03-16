<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
use Log;
class SetDevice extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:setdevice';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command Set Device';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        Log::debug(__CLASS__.__Function__.' start ');

		
        $nowRanges = date('H');
        $nowmin = date('i');
        if($nowmin>=30){
                $nowRanges = $nowRanges.':30';
        }else{
                $nowRanges = $nowRanges.':00';
        }
       

        $toDay = date('Y-m-d');
        $tools = new \App\Tools2000;
        $booking_histories = \App\models\BookingHistory::where('date',$toDay)
                                                        ->where('range_id',$nowRanges)
                                                        ->where('status',1)
                                                        ->get();
        $device = [];
        $_devices = \App\models\Device::where('status',1)
                                        ->where('mode','=','預約')
                                        ->get();
        foreach($_devices as $dkey => $dvalue){
            $device[$dvalue->id] = $dvalue;
        }
       
        $send_history=[];
        foreach ($booking_histories as $key => $value) {
            $current_device = isset($device[$value->device_id])?$device[$value->device_id]:null;
            if($current_device!=null){
                if($value->aircontrol==1){
                    $setData = [
                        "3"=>"255",
                        "4"=>"255"
                    ];
                }else{
                    $setData = [
                        "3"=>"255",
                        "4"=>"0"
                    ];
                }
                $rt = $tools->setStatus($current_device->id,$setData);
                if($rt['result']==false){
                    $error = new \App\models\ScheduleError;
                    $error->ip = $rt['ip'];
                    $error->path = $rt['path'];
                    $msg = DB::connection()->getPdo()->quote(utf8_encode($rt['msg']));
                    $error->errorMsg = $msg;
                    $error->save();
                } # code...
            }
            else{
                Log::debug(__CLASS__.__Function__.' History not found device.h_id : '.$value->id);
            }
            $send_history[] = $value->device_id;
        }

        foreach($device as $key => $value){
            if(!in_array($key,$send_history)){
                $setData = [
                    "3"=>"0",
                    "4"=>"0"
                ];
                $rt = $tools->setStatus($value->id,$setData);
                if($rt['result']==false){
                    $error = new \App\models\ScheduleError;
                    $error->ip = $rt['ip'];
                    $error->path = $rt['path'];
                    $msg = DB::connection()->getPdo()->quote(utf8_encode($rt['msg']));
                    $error->errorMsg = $msg;
                    $error->save();
                } # code...
            }
        }
	
	}
}
