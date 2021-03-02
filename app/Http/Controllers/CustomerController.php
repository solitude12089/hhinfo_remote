<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
      
        $customers = \App\models\Customer::with('cardList')
                                        ->with('last_update_user')
                                        ->get();
                                     
        return view('customer.index',['customers' => $customers]);
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('customer.create');
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
        try{
            $user = Auth::user();
            $data = $request->all();
            if(!isset($data['phone'])||$data['phone']==''){
                throw new \Exception('電話不可為空.');
            }
            if(!isset($data['name'])||$data['name']==''){
                throw new \Exception('名稱不可為空.');
            }
            $customer = \App\models\Customer::where('phone',$data['phone'])->first();
            if($customer!=null){
                throw new \Exception('電話重複,無法建立.');
            }
            DB::beginTransaction();
            $new_customer = new \App\models\Customer;
            $new_customer->phone = $data['phone'];
            $new_customer->name = $data['name'];
            $new_customer->user_id = $user->id;
            $new_customer->created_id =$user->id;
            $new_customer->save();
            

            if(isset($data['card_uuid'])&&count($data['card_uuid'])!=0){
                foreach($data['card_uuid'] as $key => $value){
                    $old_card = \App\models\Card::firstOrNew(['card_uuid'=>$value]);
                    $old_card->customer_id = $new_customer->id;
                    $old_card->save();
                }
            }
            DB::commit();
            return redirect('/customer/index')->with('alert-success', '客戶建立成功.');
        }
        catch(\Exception $e){
            DB::rollback();
            return redirect('/customer/index')->with('alert-danger', $e->getMessage());
        }
        
    }

  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = \App\models\Customer::with('cardList')->where('id',$id)->first();
        return view('customer.edit',['customer'=>$customer]);
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
        $user = Auth::user();
        $data = $request->all();
      
        if(!isset($data['phone'])||$data['phone']==''){
              return redirect('/customer/index')->with('alert-danger', '電話不可為空.');
        }
         if(!isset($data['name'])||$data['name']==''){
              return redirect('/customer/index')->with('alert-danger', '名稱不可為空.');
        }

     
     


        $customer = \App\models\Customer::where('phone',$data['phone'])
                                        ->where('id','!=',$id)->first();
        if($customer!=null){
            return redirect('/customer/index')->with('alert-danger', '電話重複,無法修改.');
        }

     
        $old_customer = \App\models\Customer::where('id','=',$id)->first();
        $old_customer->phone = $data['phone'];
        $old_customer->name = $data['name'];
        $old_customer->status = $data['status'];

        $old_card_remove = \App\models\Card::where('customer_id',$id)->delete();
        if(isset($data['card_uuid'])&&count($data['card_uuid'])!=0){
            foreach($data['card_uuid'] as $key => $value){
                $old_card = \App\models\Card::firstOrNew(['card_uuid'=>$value]);
                $old_card->customer_id = $id;
                $old_card->save();
              
            }
        }
        $old_customer->user_id =$user->id;
        $old_customer->save();
        return redirect('/customer/index')->with('alert-success', '客戶修改功.');
    
    }


    public function checkcardid(Request $request){

        try{
            $data = $request->all();
        
            if(!isset($data['phone'])||$data['phone']==''){
                throw new \Exception('電話不可為空');
            }
            $old_customer = \App\models\Customer::where('phone',$data['phone'])
                                            ->where('status',1);
            if($data['id']!=null){
                $old_customer = $old_customer->where('id','!=',$data['id']);
              
            }
            $old_customer=$old_customer->count();
            
            if($old_customer!=0){
                throw new \Exception('電話重複,無法建立');
            }
            if(isset($data['card_ids']) && count($data['card_ids'])!=0){
                $check_arr = [];
                foreach ($data['card_ids'] as $key => $value){
                    $check = \App\models\Card::with('customer')
                            ->where('card_uuid',$value);
                    if($data['id']==null){
                        $check = $check->first();
                    }
                    else{
                        $check = $check->where('customer_id','!=',$data['id'])
                                        ->first();
                    }
                    if($check!=null){
                        $check_arr[]=$check;
                    }
                }
                if(count($check_arr)!=0){
                    return response()->json([
                        'result' => 2,
                        'checks' => $check_arr
                    ],200);
                }
            }


            return response()->json([
                'result' => 1
            ],200);
           
           
        }
        catch(\Exception $e){
            return response()->json([
                'result' => 0,
                'msg' => $e->getMessage()
            ],200);
        }
       
        
    }
    

    public function list(Request $request){
        $data = $request->all();
        $customers = \App\models\Customer::where('phone','like',$data['search'].'%')
                                        ->orWhere('name','like','%'.$data['search'].'%')
                                        ->get();
        $rt = [];
        foreach ($customers as $customer){
            $rt[$customer->id] = $customer->phone.' - '.$customer->name;
        }
        return response()->json(
            $rt, 200);
        
    }

    public function spcard(){
        $user = Auth::user();
        $spcards = \App\models\Spcard::with('customer')
                                    ->with('group');
                                    
        if ($user->role != 9) {
           
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
           
            $spcards = $spcards->whereIn('group_id',$ugp);
        }
        $spcards = $spcards->get();

      
        
        
        return view('customer.spcard',['spcards'=>$spcards]);

    }

    public function getcreatespcard(){
        $user = Auth::user();
        if ($user->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $groups = \App\models\Group::whereIn('id', $ugp)->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::whereIn('group_id', $ugp)->get();
        } else {
            $groups = \App\models\Group::all()->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::all();
        }
        $familys = [];
        foreach ($_devices as $key => $value){
            $familys[$value->group_id][]=$value->family;
        }
        foreach ($familys as $key => $value){
            $familys[$key] = array_unique($value);
        }
       
        return view('customer.create-spcard',['groups'=>$groups,'familys'=>$familys]);
    }

    public function postcreatespcard(Request $request){
        $user = Auth::user();
        $data = $request->all();
        
        $old_spcard = \App\models\Spcard::where('customer_id',$data['customer'])->first();
        if($old_spcard!=null){
            return redirect('/customer/spcard')->with('alert-danger', '用戶重複,無法建立規則.');
        }


        $spcard = new \App\models\Spcard;
        $spcard->customer_id = $data['customer'];
        $spcard->group_id = $data['group'];
        $spcard->family= isset($data['family']) ? $data['family'] :[];
        $spcard->authority = isset($data['authority']) ? $data['authority']: [];
        $spcard->user_id =$user->id;
        $spcard->save();
        return redirect('/customer/spcard')->with('alert-success', '規則建立成功.');
    }

    public function geteditspcard($spcard_id){
        $user = Auth::user();
        if ($user->role != 9) {
            $ugp = $user->userGroupList->pluck('group_id')->toArray();
            $groups = \App\models\Group::whereIn('id', $ugp)->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::whereIn('group_id', $ugp)->get();
        } else {
            $groups = \App\models\Group::all()->pluck('name', 'id')->toArray();
            $_devices = \App\models\Device::all();
        }
        $familys = [];
        foreach ($_devices as $key => $value){
            $familys[$value->group_id][]=$value->family;
        }
        foreach ($familys as $key => $value){
            $familys[$key] = array_unique($value);
        }
        $spcard = \App\models\Spcard::where('id',$spcard_id)->first();
        return view('customer.edit-spcard',['spcard'=>$spcard,'groups'=>$groups,'familys'=>$familys]);
    }

    public function posteditspcard(Request $request,$spcard_id){
        $user = Auth::user();
        $data = $request->all();
       
        $old_spcard = \App\models\Spcard::where('customer_id',$data['customer'])
                                        ->where('id','!=',$spcard_id)
                                        ->first();
        if($old_spcard!=null){
            return redirect('/customer/spcard')->with('alert-danger', '用戶重複,無法修改規則.');
        }


        $spcard = \App\models\Spcard::where('id',$spcard_id)->first();
        $spcard->customer_id = $data['customer'];
        $spcard->group_id = $data['group'];
        $spcard->family= isset($data['family']) ? $data['family'] :[];
        $spcard->authority = isset($data['authority']) ? $data['authority']: [];
        $spcard->user_id =$user->id;
        $spcard->save();
        return redirect('/customer/spcard')->with('alert-success', '規則修改成功.');
    }

    public function postremovespcard(Request $request){
        $user = Auth::user();
        $data = $request->all();
      
        if(!isset($data['remove_id'])||$data['remove_id']==''){
            return redirect('/customer/spcard')->with('alert-danger', '找不到項目,無法刪除.');
        }
       
        $old_spcard = \App\models\Spcard::where('id','=',$data['remove_id'])
                                        ->first();
        if($old_spcard==null){
            return redirect('/customer/spcard')->with('alert-danger', '找不到項目,無法刪除.');
        }
        $old_spcard->delete();
        return redirect('/customer/spcard')->with('alert-success', '刪除成功.');
    }





}
