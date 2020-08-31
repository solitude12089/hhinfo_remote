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
                dd($msg,$msg2);
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
             $devices = \App\models\Device::where('status',1)->get();
        }
        else{
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
              
            $devices = \App\models\Device::where('status',1)
                                        ->whereIn('group_id',$ugp)
                                        ->get();
        }

    
        return view('remote.index',['devices'=>$devices,'groups'=>$groups]);
     
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

  
}
