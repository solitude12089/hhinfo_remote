<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;

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
                                        ->with('device');
                                        

        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $systemlog = $systemlog->whereIn('group_id', $ugp);
        }
            
        $systemlog = $systemlog->take(500)
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
                   
                    // $rt_data[] = [
                    //     'date' => $value->created_at,
                    //     'user' => $value->user_id==0?'系統':$value->user->name,
                    //     'action' => '遠端操作',
                    //     'target' => $value->device->family.'-'.$value->device->name,
                    //     'msg' => $msg
                    // ];
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
            $rt_data[] = [
                $tv['date'],
                $tv['user'],
                $tv['target'],
                $action,
                $msg
            ];
        }
     
        return view('systemlog.index',['rt_data' => $rt_data]);

      
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
