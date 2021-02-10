<?php

namespace App\Http\Controllers;

use Auth;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        $_timeRanges = \App\models\TimeRange::all();
        $timeRanges = [];
        foreach ($_timeRanges as $key => $value){
            $timeRanges[$value->id] = $value;
        }
        $devices=[];
        $deviceMap = [];
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $groups = \App\models\Group::whereIn('id', $ugp)->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::whereIn('group_id', $ugp)
                                            ->where('status',1)
                                            ->where('style','=','一般')
                                            ->get();
        } else {
            $groups = \App\models\Group::all()->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::where('status',1)
                                        ->where('style','=','一般')
                                        ->get();
        }

        foreach ($_devices as $key => $value) {
            $devices[$value->group_id][] = $value;
            $deviceMap[$value->id]=$value;
        }

        $customers = \App\models\Customer::where('status', 1)->get();

        return view('booking.index', ['devices' => $devices, 'groups' => $groups, 'timeRanges' => $timeRanges, 'customers' => $customers,'deviceMap'=>$deviceMap]);
    }

    public function postSearch(Request $request)
    {
        $data = $request->all();
       
        $devices = \App\models\Device::with(['BookingHistory' => function ($q) use ($data) {
            $q->where('date', '>=', $data['startDate'])
                ->where('date', '<=', $data['endDate']);
        }])
            ->where('group_id', '=', $data['group'])
            ->where('style','=','一般')
            ->where('status', '=', 1);

        if(isset($data['device'])&&$data['device']!=''){
            $devices = $devices->where('id', '=', $data['device']);
        }
        $devices=$devices->get();
        foreach ($devices as $key => $device) {
            $mark = [];
            foreach ($device->BookingHistory as $k => $bh) {
                $mark[$bh->date][$bh->range_id] = $bh;
            }
            $devices[$key]->BookingHistory_Mark = $mark;
        }

        $ranges = $this->MyDateRange($data['startDate'], $data['endDate']);
        $now = [
            'day' => date('Y-m-d'),
            'range' => date('H'),
        ];
        $dayMap = [];
        foreach ($ranges as $key => $value) {
            $w = date('w', strtotime($value));
            $str_day = '';
            switch ($w) {
                case 1:
                    $str_day = '星期一';
                    break;
                case 2:
                    $str_day = '星期二';
                    break;
                case 3:
                    $str_day = '星期三';
                    break;
                case 4:
                    $str_day = '星期四';
                    break;
                case 5:
                    $str_day = '星期五';
                    break;
                case 6:
                    $str_day = '星期六';
                    break;
                case 0:
                    $str_day = '星期日';
                    break;
            }
            $dayMap[$value]=$str_day;
        }

        if (isset($data['sp_pick']) && count($data['sp_pick']) != 0) {
            foreach ($ranges as $key => $value) {
                $w = date('w', strtotime($value));
                if (!in_array($w, $data['sp_pick'])) {
                    unset($ranges[$key]);
                }
            }
        }

        $timeRanges = \App\models\TimeRange::all()->pluck('description', 'id')->toArray();

        $ss = view('booking.searchTable', ['devices' => $devices, 'ranges' => $ranges, 'timeRanges' => $timeRanges,'dayMap'=>$dayMap,'now' => $now]);

        return $ss;
    }

 

    public function postBooking(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();

        if (!isset($data['customer']) || $data['customer'] == '') {
            return redirect('booking/index')->with('alert-danger', '預約失敗,預約者不可為空值.');
        }

        if (!isset($data['aircontrol']) || $data['aircontrol'] == '') {
            return redirect('booking/index')->with('alert-danger', '預約失敗,是否租用冷氣不可為空值.');
        }
        if (!isset($data['booking']) || count($data['booking']) == 0) {
            return redirect('booking/index')->with('alert-danger', '預約失敗,預約時段不可為空值.');
        }
        try {
            DB::beginTransaction();
           
          
            $success_bh=[];
            foreach ($data['booking'] as $device_id => $arr) {
                foreach ($arr as $date => $arr2) {
                    foreach ($arr2 as $key => $range_id) {
                        $bh = new \App\models\BookingHistory;
                        $bh->device_id = $device_id;
                        $bh->date = $date;
                        $bh->customer_id = $data['customer'];
                        $bh->range_id = $range_id;
                        $bh->aircontrol = $data['aircontrol'];
                        $bh->user_id = $user->id;
                        $bh->description = $data['note'];
                        $bh->save();
                        $success_bh[]=$bh;


                        $syslog =  new \App\models\SystemLog;
                        $syslog->type = 'booking';
                        $syslog->function_name = 'booking';
                        $syslog->user_id = $user->id;
                        $syslog->col1 = $device_id;
                        $syslog->col2 = $data['customer'];
                        $syslog->col3 = $date;
                        $syslog->col4 = $range_id;
                        $syslog->col5 = $data['aircontrol'];
                        $syslog->save();


                    }
                }
            }
            DB::commit();


            $nowRanges = date('H');
            $toDay = date('Y-m-d');
            $tools = new \App\Tools2000;
            foreach($success_bh as $key => $value){
                if($value->range_id==$nowRanges&&$value->date==$toDay){
                    if($value->aircontrol==1){
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
                    $rt = $tools->setStatus($value->device_id,$setData);
                }
            }

            return redirect('booking/index')->with('alert-success', '預約成功');
        }
        catch(\Exception $e){
           
            $errorCode = $e->errorInfo[1];
            $msg = $e->getMessage();
            if($errorCode == 1062){
                $msg='預約時段重複,請重新整理後再試.';
            }
            return redirect('booking/index')->with('alert-danger', '預約失敗,'.$msg);
        }
      

      
    }

    public function getQuery()
    {
        $user = Auth::user();
        $devices=[];
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $groups = \App\models\Group::whereIn('id', $ugp)->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::whereIn('group_id', $ugp)
                                            ->where('status',1)
                                            ->where('style','=','一般')
                                          
                                             ->get();
        } else {
            $groups = \App\models\Group::all()->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::where('status',1)
                                           ->where('style','=','一般')
                                           ->get();
        }
        foreach ($_devices as $key => $value) {
            $devices[$value->group_id][] = $value;
        }
       
      


        

        return view('booking.query', ['groups' => $groups, 'devices' => $devices]);
    }

    public function postQuery(Request $request)
    {
        $data = $request->all();
        $whereRaw = ' where d.group_id = ' . $data['group'];
        $whereRaw = $whereRaw. ' and bh.date >= \'' .$data['startDate']. '\' and bh.date <= \'' .$data['endDate'].'\'';
        if($data['device']!=''){
            $whereRaw = $whereRaw. ' and d.id = ' . $data['device'];
        }
        if($data['customer']!=''){
            $whereRaw = $whereRaw. ' and c.id = \'' .$data['customer'].'\'';
        }
        if($data['note']!=''){
            $whereRaw = $whereRaw. ' and bh.description like \'%' .$data['note'].'%\'';
        }

    
       

        $sql = 'SELECT  bh.id as bh_id,
                        c.id as user_id,
                        c.name as user,
                        c.phone,bh.range_id,
                        tr.description as tr_description,
                        bh.date,
                        bh.description,
                        d.id as device_id,
                        d.family,d.name,
                        bh.aircontrol
                FROM hhinfo_remote.booking_histories as bh
                join hhinfo_remote.devices as d
                on bh.device_id = d.id
                join hhinfo_remote.customers as c
                on bh.customer_id = c.id
                join hhinfo_remote.time_ranges as tr
                on bh.range_id = tr.id';
        $sql = $sql.$whereRaw.' order by bh.date,c.id,bh.range_id';
        $query = DB::select(DB::raw($sql));
       // dd($sql,$query);
        $rt_data = [];
        foreach ($query as $key => $value){
            $ckbok = '<input type=checkbox name="remove[]" value="'.$value->bh_id.'"></input>';
            // $action = '<button class="btn btn-danger btn-xs" target_id="'.$value->bh_id.'" onclick="remove(this)">刪除</button>';
            $rt_data[]=[
                $ckbok,
                $value->user,
                $value->phone,
                $value->family.'-'.$value->name,
                $value->date,
                $value->tr_description,
                $value->description,
                $value->aircontrol==0?'否':'是',
            ];
        }
     
    
        return response()->json($rt_data, 200);

    }

    public function getCalendar()
    {
        $user = Auth::user();
        if (Auth::user()->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $groups = \App\models\Group::whereIn('id', $ugp)->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::whereIn('group_id', $ugp)
                                           ->where('status',1)
                                           ->where('style','=','一般')
                                          
                                           ->get();
        } else {
            $groups = \App\models\Group::all()->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::where('status',1)
                                        ->where('style','=','一般')
                                        ->get();
        }
        $devices=[];
        foreach ($_devices as $key => $value) {
            $devices[$value->group_id][] = $value;
            # code...
        }

        return view('booking.calendar', ['groups' => $groups, 'devices' => $devices]);
    }

    public function postCalendar(Request $request)
    {
        $data = $request->all();

        $sql = 'SELECT  c.id as user_id,c.name as user,c.phone,bh.range_id,tr.start,tr.end,bh.date,bh.description,d.id as device_id ,d.family,d.name,bh.aircontrol
                FROM hhinfo_remote.booking_histories as bh
                join hhinfo_remote.devices as d
                on bh.device_id = d.id
                join hhinfo_remote.customers as c
                on bh.customer_id = c.id
                join hhinfo_remote.time_ranges as tr
                on bh.range_id = tr.id
                where d.group_id = ' . $data['group'] . ' and d.id = \'' . $data['device'] . '\'  and  EXTRACT(YEAR_MONTH from bh.date) = \'' . str_replace('-', '', $data['date']) . '\'
                order by bh.date,c.id,bh.range_id';
       
        $query = DB::select(DB::raw($sql));
      
        $rt = [];
        foreach ($query as $key => $value) {
            $rt[$value->date][$value->user_id][] = $value;
        }
      
        $eventList = [];
        foreach ($rt as $k_date => $v) {
            foreach ($v as $k_user => $vv) {
                $event = [];
                foreach ($vv as $k => $vvv) {
                    if (isset($vv[$k + 1]) && $vv[$k + 1]->range_id == $vvv->range_id + 1) {
                        if($event==[]){
                            if($vvv->aircontrol==1){
                                $event['title'] = '(冷)'.$vvv->user;
                             
                            }
                            else{
                                $event['title'] = $vvv->user;
                            }
                            $event['note'] = $vvv->description;
                            $event['start'] = $k_date . ' ' . $vvv->start;
                        }
                    } else {
                        if ($event != []) {
                            $event['end'] = $k_date . ' ' . $vvv->end;
                            $eventList[] = $event;
                            $event = [];
                        } else {
                            if($vvv->aircontrol==1){
                                $event['title'] = '(冷)'.$vvv->user;
                              
                            }
                            else{
                                $event['title'] = $vvv->user;

                            }
                            $event['note'] = $vvv->description;
                            $event['start'] = $k_date . ' ' . $vvv->start;
                            $event['end'] = $k_date . ' ' . $vvv->end;
                            $eventList[] = $event;
                            $event = [];
                        }
                    }

                }
            }
        }
      
        return response()->json($eventList, 200);

    }

    public function MyDateRange($start, $end)
    {
        $array = [];
        if ($start == $end) {
            return [$start];
        }

        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end)
        );
       
        foreach ($period as $date) {
            $array[] = $date->format('Y-m-d');
        }
        $array[] = $end;
        return $array;
    }


    public function remove(Request $request){
        try{
            $user = Auth::user();
            $data =$request->all();
            
           
            if(!isset($data['remove_id'])||count($data['remove_id'])==0){
                throw new \Exception('刪除失敗,請點選資料');
            }
            DB::beginTransaction();
            $bhs = \App\models\BookingHistory::whereIn('id',$data['remove_id'])->get();
            foreach ($bhs as $key => $bh){
                $syslog =  new \App\models\SystemLog;
                $syslog->type = 'booking';
                $syslog->function_name = 'remove';
                $syslog->user_id = $user->id;
                $syslog->col1 = $bh->device_id;
                $syslog->col2 = $bh->customer_id;
                $syslog->col3 = $bh->date;
                $syslog->col4 = $bh->range_id;
                $syslog->save();
                $bh->delete();
            }
            DB::commit();
            return redirect('booking/query')->with('alert-success', '刪除成功');
        }
        catch(\Exception $e){
            DB::rollback();
            return redirect('booking/query')->with('alert-danger',$e->getMessage());
        }
       
    }
}
