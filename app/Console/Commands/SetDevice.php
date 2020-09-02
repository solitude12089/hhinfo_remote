<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
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
		$nowRanges = date('H');
        $toDay = date('Y-m-d');
        $tools = new \App\Tools2000;
        $booking_histories = \App\models\BookingHistory::where('date',$toDay)
                                                        ->where('range_id',$nowRanges)
                                                        ->where('status',1)
                                                        ->get();
        $devices = \App\models\Device::where('status',1)->get();
        $history=[];
        foreach ($booking_histories as $key => $value) {
            $history[$value->device_id] = $value;
        }


        foreach ($devices as $key => $device) {
            if(array_key_exists($device->id, $history)){
                $h = $history[$device->id];
                if($h->aircontrol==1){
                    $setData = [
                        "3"=>"255",
                        "4"=>"255"
                    ];
                 
                }
                else{
                    $setData = [
                        "3"=>"255",
                        "4"=>"0"
                    ];
                   
                }
               
            }
            else{
                $setData = [
                    "3"=>"0",
                    "4"=>"0"
                ];
                
            }

            $rt = $tools->setStatus($device->id,$setData);
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
