<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
use Log;
class CheckLive extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:checklive';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command Check Live';

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

        $devices = \App\models\Device::where('status',1)
                                        ->whereRaw('TIMESTAMPDIFF(MINUTE,updated_at,now())>=6')
                                        ->get();
        $msg ='';
        foreach ($devices as $device){
            $msg=$msg.'IP : '.$device->ip.' 裝置 : '.$device->family.'-'.$device->name.' last updated : '.$device->updated_at.PHP_EOL;
        }
        if($msg!=''){
            $msg='Error!!!失去連線!!!'.PHP_EOL.$msg;
            $tools= new \App\ToolsDiscord;
		    $tools->push($msg);
	
        }

		Log::debug(__CLASS__.__Function__.' end ');


	

	
	}
}
