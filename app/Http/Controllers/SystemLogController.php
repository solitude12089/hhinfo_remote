<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use DB;
use DateTime;
use DateInterval;
class SystemLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $systemlog = \App\models\SystemLog::with('user')
                                        ->with('customer')
                                        ->with('device')
                                        ->where('type','normal');


        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $systemlog = $systemlog->whereIn('group_id', $ugp);
        }

        $systemlog = $systemlog->take(1000)
                                ->orderBy('created_at','DESC')
                                ->get();

        $rt_data = [];
        $temp_queue = [];
        foreach ($systemlog as $key => $value){
            switch($value->function_name){
                case 'device control':
                    $act = json_decode($value->col2);
                    $msg = '';
                    foreach ($act as $k => $v){

                        if(strlen($msg)!=0){
                            $msg .= ',';
                        }
                        switch($k){
                            case '1':
                                $msg .= '開門';
                            break;
                            case '2':
                                $msg .= '關門';
                            break;
                            case '3':
                                if($v==255){
                                    $msg .= '一般用電:開';
                                }
                                if($v=='0'){
                                    $msg .= '一般用電:關';
                                }
                            break;
                            case '4':
                                if($v==255){
                                    $msg .= '冷氣用電:開';
                                }
                                if($v=='0'){
                                    $msg .= '冷氣用電:關';
                                }
                            break;
                        }
                    }
                    $rt_data[] = [
                        $value->created_at->format('Y-m-d H:i:s'),
                        $value->user_id==0?'系統':$value->user->name,
                        $value->device->family.'-'.$value->device->name,
                        '遠端操作',
                        $msg
                    ];
                    break;
                case 'swipe card':
                    // $rt_data[] = [
                    //     'date' => $value->created_at,
                    //     'user' => $value->customer==null?'':$value->customer->name,
                    //     'action' => '有效刷卡',
                    //     'target' => $value->device->family.'-'.$value->device->name,
                    //     'msg' => '刷卡'
                    // ];


                    if($value->col2!==null){
                        $temp_queue[$value->col2]['action'][] = '有效刷卡';
                        $temp_queue[$value->col2]['msg'][] = '合法卡';
                        $temp_queue[$value->col2]['user'] = $value->customer==null?'':$value->customer->name;
                    }

                    break;
                case 'swipe return':
                    // $rt_data[] = [
                    //     'date' => $value->created_at,
                    //     'user' => $value->customer==null?'':$value->customer->name,
                    //     'action' => '刷卡回應',
                    //     'target' => $value->device->family.'-'.$value->device->name,
                    //     'msg' => $value->col3
                    // ];
                    if($value->col2!==null){
                        $temp_queue[$value->col2]['action'][] = '刷卡回應';
                        $temp_queue[$value->col2]['msg'][] = $value->col3;
                        $temp_queue[$value->col2]['user'] = $value->customer==null?'':$value->customer->name;
                    }

                    break;
                case 'swipe event':
                    // $rt_data[] = [
                    //     'date' => $value->created_at,
                    //     'user' => '',
                    //     'action' => '刷卡事件',
                    //     'target' => $value->device->family.'-'.$value->device->name,
                    //     'msg' => $value->col2
                    // ];

                    $temp_queue[$value->id]['date'] = $value->created_at->format('Y-m-d H:i:s');
                    $temp_queue[$value->id]['action'][] = '刷卡事件';
                    $temp_queue[$value->id]['target'] = $value->device->family.'-'.$value->device->name;
                    $temp_queue[$value->id]['msg'][] = '卡號 : '.$value->col2;
                    if(!isset($temp_queue[$value->id]['user'])){
                        $temp_queue[$value->id]['user'] = '';
                    }
                break;
                case 'phone control':
                    $rt_data[] = [
                        $value->created_at->format('Y-m-d H:i:s'),
                        $value->customer==null?'':$value->customer->name,
                        $value->device->family.'-'.$value->device->name,
                        '手機操作',
                        $value->col3
                    ];
                break;
            }
        }
        foreach($temp_queue as $tk => $tv){
            // $rt_data[] = [
                    //     'date' => $value->created_at,
                    //     'user' => $value->customer==null?'':$value->customer->name,
                    //     'action' => '刷卡回應',
                    //     'target' => $value->device->family.'-'.$value->device->name,
                    //     'msg' => $value->col3
                    // ];
            $action='';
            $msg = '';
            foreach($tv['action'] as $tvak => $tvav){
                $action = $action.$tvav.'<br>';
            }
            foreach($tv['msg'] as $tvmk => $tvmv){
                $msg = $msg.$tvmv.'<br>';
            }
            if(!isset($tv['date'])){
                $tv['date'] = '';
            }
            $rt_data[] = [
                isset($tv['date']) ? $tv['date'] :'',
                isset($tv['time']) ? $tv['time'] :'',
                isset($tv['target']) ? $tv['target'] :'',
                $action,
                $msg
            ];
        }

        return view('systemlog.index',['rt_data' => $rt_data]);


    }


    public function control_log()
    {
        $user = Auth::user();
        $_devices =[];
        $groups = [];
        $devices = [];
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $groups = \App\models\Group::whereIn('id', $ugp)->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::whereIn('group_id', $ugp)
                                            ->where('status',1)
                                            ->get();
        } else {
            $groups = \App\models\Group::all()->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::where('status',1)
                                           ->get();
        }
        foreach ($_devices as $key => $value) {
            $devices[$value->group_id][] = $value;
        }
      
        return view('systemlog.control_log', ['groups' => $groups, 'devices' => $devices]);
  

      
    }

    public function control_log_search(Request $request){
        $data = $request->all();
        $ds = new DateTime( $data['startDate']);
        $startDate =  $ds->format('Y-m-d H:i:s');
        $de = new DateTime( $data['endDate']);
        $endDate = $de->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');
        $systemlog = \App\models\SystemLog::where('type','normal')
                                        ->where('group_id',$data['group'])
                                        ->where('created_at','<',$endDate)
                                        ->where('created_at','>=',$startDate);
        if(isset($data['device'])&&$data['device']!=''){
            $systemlog = $systemlog->where('col1',$data['device']);
        }

     
        if(isset($data['log_type'])&&count($data['log_type'])!=0){
            $_function_name = [];
            foreach ($data['log_type'] as $key => $value){
                switch($value){
                    case 'swipe_event':
                        $_function_name[] = 'swipe card';
                        $_function_name[] = 'swipe event';
                        $_function_name[] = 'swipe return';
                    break;
                    case 'device_control':
                        $_function_name[] = 'device control';
                    break;
                    case 'phone_control':
                        $_function_name[] = 'phone control';
                    break;
                }
            }

            $systemlog = $systemlog->whereIn('function_name',$_function_name);
        }
        $systemlog = $systemlog ->orderBy('created_at','DESC')
                                ->get();
                       
       
        $rt_data = [];
        $temp_queue = [];
        foreach ($systemlog as $key => $value){
            switch($value->function_name){
                case 'device control':
                    $act = json_decode($value->col2);
                    $msg = '';
                    foreach ($act as $k => $v){
                        if(strlen($msg)!=0){
                            $msg .= ',';
                        }
                        switch($k){
                            case '1':
                                $msg .= '開門';
                            break;
                            case '2':
                                $msg .= '關門';
                            break;
                            case '3':
                                if($v==255){
                                    $msg .= '一般用電:開';
                                }
                                if($v=='0'){
                                    $msg .= '一般用電:關';
                                }
                            break;
                            case '4':
                                if($v==255){
                                    $msg .= '冷氣用電:開';
                                }
                                if($v=='0'){
                                    $msg .= '冷氣用電:關';
                                }
                            break;
                        }
                    }
                    $rt_data[] = [
                        $value->created_at->format('Y-m-d H:i:s'),
                        $value->user_id==0?'系統':$value->user->name,
                        $value->device->family.'-'.$value->device->name,
                        '遠端操作',
                        $msg
                    ];
                    break;
                case 'swipe card':
                    if($value->col2!==null){
                        $temp_queue[$value->col2]['action'][] = '有效刷卡';
                        $temp_queue[$value->col2]['msg'][] = '合法卡';
                        $temp_queue[$value->col2]['user'] = $value->customer==null?'':$value->customer->name;
                    }
                    break;
                case 'swipe return':
                    if($value->col2!==null){
                        $temp_queue[$value->col2]['action'][] = '刷卡回應';
                        $temp_queue[$value->col2]['msg'][] = $value->col3;
                        $temp_queue[$value->col2]['user'] = $value->customer==null?'':$value->customer->name;
                    }
                    break;
                case 'swipe event':
                    $temp_queue[$value->id]['date'] = $value->created_at->format('Y-m-d H:i:s');
                    $temp_queue[$value->id]['action'][] = '刷卡事件';
                    $temp_queue[$value->id]['target'] = $value->device->family.'-'.$value->device->name;
                    $temp_queue[$value->id]['msg'][] = '卡號 : '.$value->col2;
                    if(!isset($temp_queue[$value->id]['user'])){
                        $temp_queue[$value->id]['user'] = '';
                    }
                break;
                case 'phone control':
                    $rt_data[] = [
                        $value->created_at->format('Y-m-d H:i:s'),
                        $value->customer==null?'':$value->customer->name,
                        $value->device->family.'-'.$value->device->name,
                        '手機操作',
                        $value->col3
                    ];
                break;
            }
        }

        foreach($temp_queue as $tk => $tv){
            $action='';
            $msg = '';
            foreach($tv['action'] as $tvak => $tvav){
                $action = $action.$tvav.'<br>';
            }
            foreach($tv['msg'] as $tvmk => $tvmv){
                $msg = $msg.$tvmv.'<br>';
            }
            if(!isset($tv['date'])){
                $tv['date'] = '';
            }
            $rt_data[] = [
                isset($tv['date']) ? $tv['date'] :'',
                isset($tv['user']) ? $tv['user'] :'',
                isset($tv['target']) ? $tv['target'] :'',
                $action,
                $msg
            ];
        }
        return response()->json($rt_data, 200);
    }

    public function booking_history(){
        $user = Auth::user();
        $more_where = '';
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $users = \App\models\UserGroup::whereIn('group_id', $ugp)
                                            ->pluck('user_id')->toArray();
            
            $more_where = 'and sl.user_id in ('.implode(',', $users).')';
        }



        $sql = "SELECT  u.id as u_id,u.name as u_name,c.id as c_id,c.name as c_name,c.phone as c_phone,sl.col4 as range_id,AddTime(col4, '00:00:00') as start,AddTime(col4, '00:30:00') as end,sl.col3 as `date`,d.id as device_id ,d.family,d.name,sl.col5 as `aircontral`,sl.created_at
                FROM hhinfo_remote.system_logs as sl
                join hhinfo_remote.devices as d
                on sl.col1 = d.id
                join hhinfo_remote.customers as c
                on sl.col2 = c.id
                join hhinfo_remote.users as u
                on sl.user_id = u.id
                where sl.type = 'booking' and sl.function_name = 'booking' ".$more_where."
                order by sl.created_at,sl.col3,sl.col4";
        $query = DB::select(DB::raw($sql));
      
        $rt_data = [];
        foreach ($query as $key => $v) {
            $v = (array)$v;
          
            $rt_data[] = [
                $v['created_at'],
                $v['date'],
                $v['start'].' - '.$v['end'],
                $v['c_phone'].'-'.$v['c_name'],
                $v['family'].'-'.$v['name'],
                $v['aircontral']=="1"?'是':'否',
                $v['u_name'],
           
            ];
           
        }
        return view('systemlog.booking_history',['rt_data' => $rt_data]);
      
       
    }

    public function remove_history(){
        $user = Auth::user();
        $more_where = '';
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $users = \App\models\UserGroup::whereIn('group_id', $ugp)
                                            ->pluck('user_id')->toArray();
            
            $more_where = 'and sl.user_id in ('.implode(',', $users).')';
        }
        $sql = "SELECT  u.id as u_id,u.name as u_name,c.id as c_id,c.name as c_name,c.phone as c_phone,sl.col4 as range_id,AddTime(col4, '00:00:00') as start,AddTime(col4, '00:30:00') as end,sl.col3 as `date`,d.id as device_id ,d.family,d.name,sl.created_at
                FROM hhinfo_remote.system_logs as sl
                join hhinfo_remote.devices as d
                on sl.col1 = d.id
                join hhinfo_remote.customers as c
                on sl.col2 = c.id
                join hhinfo_remote.users as u
                on sl.user_id = u.id
                where sl.type = 'booking' and sl.function_name = 'remove' ".$more_where."
                order by sl.created_at,sl.col3,sl.col4";
       
        $query = DB::select(DB::raw($sql));
      
        $rt_data = [];
        foreach ($query as $key => $v) {
            $v = (array)$v;
          
            $rt_data[] = [
                $v['created_at'],
                $v['date'],
                $v['start'].' - '.$v['end'],
                $v['c_phone'].'-'.$v['c_name'],
                $v['family'].'-'.$v['name'],
                $v['u_name'],
               
            ];
           
        }
        return view('systemlog.remove_history',['rt_data' => $rt_data]);
    }

    public function s2_change(){
        $user = Auth::user();
        $more_where = '';
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $users = \App\models\UserGroup::whereIn('group_id', $ugp)
                                            ->pluck('user_id')->toArray();
            
            $more_where = 'and sl.user_id in ('.implode(',', $users).')';
        }
        $sql = "SELECT  c.name as c_name,
                d.id as device_id ,
                d.family,
                d.name as d_name,
                sl.col2,
                sl.created_at
            FROM hhinfo_remote.system_logs as sl
            join hhinfo_remote.devices as d
            on sl.col1 = d.id
            left join hhinfo_remote.booking_histories as bh
            join hhinfo_remote.customers as c
            on bh.customer_id = c.id
            on sl.col4 = bh.range_id and sl.col3 = bh.date and sl.col1 = bh.device_id
            where sl.type = 'normal' and sl.function_name = 's2 change' ".$more_where."
            order by sl.created_at desc";

        $query = DB::select(DB::raw($sql));

        $rt_data = [];
        foreach ($query as $key => $v) {
            $v = (array)$v;

            $rt_data[] = [
                $v['c_name'],
                $v['family'].' - '.$v['d_name'],
                $v['col2']=="1"?$v['created_at']:"",
                $v['col2']=="0"?$v['created_at']:""
            
            ];

          

        }
      
        return view('systemlog.s2_change',['rt_data' => $rt_data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
