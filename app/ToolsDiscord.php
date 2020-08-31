<?php 
namespace App;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;

class ToolsDiscord {
	public function push($msg){
		try {
            	$url = env('DISCORD_HOOK');
		        $data = [
		            'username' => 'Server',
		            'content' => $msg,
		        ];

				$ch = curl_init($url);
				
				curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
				curl_setopt($ch, CURLOPT_HTTPHEADER,array(
		                        'Content-type: application/json'
		                        )); 
				$rtn = curl_exec($ch);
				curl_close($ch);
		} catch (\Exception $e) {
			\Log::debug(__FUNCTION__.' Exception : '.$e->getMessage());
		
		}
	}
}



?>