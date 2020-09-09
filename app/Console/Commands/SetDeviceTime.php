<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use DB;
class SetDeviceTime extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:setdevicetime';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command Set Device Time';

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

        $devices = \App\models\Device::where('status',1)->get();
        foreach ($devices as $device){
            $rt = $tools->setTime($device->id);
        }
       
	}
}
