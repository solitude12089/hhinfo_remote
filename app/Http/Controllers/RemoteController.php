<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
class RemoteController extends Controller
{


    public function test(){
        $nowRanges = date('H');
        $toDay = date('Y-m-d');
        $tools = new \App\Tools2000;
    


        $booking_histories = \App\models\BookingHistory::where('date',$toDay)
                                                        ->where('range_id',$nowRanges)
                                                        ->where('status',1)
                                                        ->get();
        $devices = \App\models\Device::all();


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
                     echo "OPEN".$device->ip.'OPEN AIR' .'<br>';
                }
                else{
                    $setData = [
                        "3"=>"255",
                        "4"=>"0"
                    ];
                     echo "OPEN".$device->ip. 'CLOSE AIR'.'<br>';
                }
               
            }
            else{
                $setData = [
                    "3"=>"0",
                    "4"=>"0"
                ];
                  echo "CLOSE".$device->ip.'<br>';
            }

            $rt = $tools->setStatus($device->id,$setData);
            if($rt['result']==false){
                $msg = $rt['msg'];
                $msg2 = DB::connection()->getPdo()->quote(utf8_encode($rt['msg']));
                $error = new \App\models\ScheduleError;
                $error->ip = $rt['ip'];
                $error->path = $rt['path'];
                $error->errorMsg = $rt['msg'];
                $error->save();
            }
            var_dump($rt);
            # code...
        }

        dd("End");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user==null){
            return redirect('/');
        }

        $role = $user->role;


        $groups = \App\models\Group::all()->pluck('name','id')->toArray();


        if($role==9){
             $devices = \App\models\Device::with('group')
                                        ->where('status',1)
                                        ->get();
        }
        else{
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
              
            $devices = \App\models\Device::with('group')
                                        ->where('status',1)
                                        ->whereIn('group_id',$ugp)
                                        ->get();
        }
        return view('remote.index',['devices'=>$devices,'groups'=>$groups]);
    }

    public function getChangeMode(Request $request,$device_id){
        try{
            $data = $request->all();
            $device = \App\models\Device::where('id',$device_id)->first();
            if($device===null){
                throw new \Exception('找不到該裝置.');
            }
            if(!isset($data['mode'])||$data['mode']==''){
                throw new \Exception('請輸入模式.');
            }
            $device->mode = $data['mode'];
            $device->save();
            return response()->json(
                array(
                    'status' =>1
                ), 200);
        }
        catch(\Exception $e){
            return response()->json(
                array(
                    'status' =>0, 
                    'msg' => $e->getMessage()
                ), 200);
        }
     
    }
    public function getDeviceList(){
        $user = Auth::user();
        $role = $user->role;
        if($role==9){
            $devices = \App\models\Device::with('group')
                                        ->where('status',1)
                                        ->get();
        }
        else{
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
                
            $devices = \App\models\Device::with('group')
                                        ->where('status',1)
                                        ->whereIn('group_id',$ugp)
                                        ->get();
        }
       
        return response()->json([
            'data' => $devices
        ]);
    }


    public function getDevice(){
        $devices = \App\models\Device::all();
        $groups = \App\models\Group::all()->pluck('name','id')->toArray();
        return view('admin.device',['devices'=>$devices,'groups'=>$groups]);
    }

    public function getStatus($device_id){
        $tools = new \App\Tools2000;
        $rt = $tools->getStatus($device_id);

       
        if($rt['result']==true){
            return response()->json(
            array(
                'status' =>1, 
                'msg' => '',
                'data' => $rt['data']
            ), 200);
        }

        return response()->json(
        array(
            'status' =>0, 
            'msg' => $rt['msg']
        ), 200);


    }

    public function setStatus(Request $request,$device_id){
        $setData = $request->all();
       
        $tools = new \App\Tools2000;
        $rt = $tools->setStatus($device_id,$setData);

       
        if($rt['result']==true){
            return response()->json(
            array(
                'status' =>1, 
                'msg' => '',
                'data' => $rt['data']
            ), 200);
        }

        return response()->json(
        array(
            'status' =>0, 
            'msg' => $rt['msg']
        ), 200);

    }





    public function getTime($device_id){
        $tools = new \App\Tools2000;
        $rt = $tools->getTime($device_id);
        if($rt['result']==true){
            return response()->json(
            array(
                'status' =>1, 
                'msg' => '',
                'data' => $rt['data']
            ), 200);
        }

        return response()->json(
        array(
            'status' =>0, 
            'msg' => $rt['msg']
        ), 200);


    }
    public function setTime($device_id){
        $tools = new \App\Tools2000;
        $rt = $tools->setTime($device_id);
        if($rt['result']==true){
            return response()->json(
            array(
                'status' =>1, 
                'msg' => '',
                'data' => $rt['data']
            ), 200);
        }

        return response()->json(
        array(
            'status' =>0, 
            'msg' => $rt['msg']
        ), 200);


    }

    public function syncStatus(){
        $user = Auth::user();
        $tools = new \App\Tools2000;
        $role = $user->role;
        if($role==9){
            $devices = \App\models\Device::with('group')
                                        ->where('status',1)
                                        ->get();
        }
        else{
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
                
            $devices = \App\models\Device::with('group')
                                        ->where('status',1)
                                        ->whereIn('group_id',$ugp)
                                        ->get();
        }
        foreach($devices as $key => $device){
            $rt = $tools->getStatus($device->id);
           
            if($rt['result']==true){
             
                $data = $rt['data'];
                $r1 = isset($data['r1ststus'])?$data['r1ststus']:"";
                $r2 = isset($data['r2ststus'])?$data['r2ststus']:"";
                $r3 = isset($data['r3ststus'])?$data['r3ststus']:"";
                $r4 = isset($data['r4ststus'])?$data['r4ststus']:"";
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
                    's6'  => $s6
                ]);
            }
           
        }
        return redirect('/remote/index');

    }
  
}
