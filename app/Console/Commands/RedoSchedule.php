<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
class RedoSchedule extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:redoschedule';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command Redo Schedule';

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
		$tools = new \App\Tools2000;
		$schedule = \App\models\ScheduleError::where('status',1)
												->get();
		$date = date('YmdHis');
        $tokenO = 'hhinfo:'.$date;
        $token = base64_encode($tokenO);
		foreach ($schedule as $key => $value) {
			$device = \App\models\Device::where('ip',$value->ip)
										->first();

			$new_path  =  preg_replace("/token(.*?)&/",'token='.$token.'&',$value->path);
			$rt = $tools->httpGet($value->ip,$new_path);
			if($rt['result']==false){
				$msg = DB::connection()->getPdo()->quote(utf8_encode($rt['msg']));
                $value->errorMsg = $msg;
				if($value->rty>=3){
					$value->status = 9;
					$msg='Error!!!定時排成失敗!!!'.PHP_EOL.'IP : '.$device->ip.' 裝置 : '.$device->family.'-'.$device->name;
					$tools= new \App\ToolsDiscord;
					$tools->push($msg);
				}
				else{
					$value->rty=$value->rty+1;
				}
				$value->save();
		
            }
            else{
            	$value->status= 2;
            	$value->save();
            }

			# code...
		}

	

	
	}
}
