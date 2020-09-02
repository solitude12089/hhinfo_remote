<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
class responseJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $device_id; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($device_id)
    {
        $this->device_id = $device_id;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = date('Y-m-d H:i:s');
        $tools = new \App\Tools2000;
        $setD = [
            '2' => "1"
        ];
        $tools->setStatus($this->device_id,$setD);
        Log::info('responseJob : '.__FUNCTION__.'Device ID : '.$this->device_id);
        //
    }
}
