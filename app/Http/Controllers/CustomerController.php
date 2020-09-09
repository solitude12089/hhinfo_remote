<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     
        $customers = \App\models\Customer::all();
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
        
        $data = $request->all();
   
        if(!isset($data['phone'])||$data['phone']==''){
              return redirect('/customer/index')->with('alert-danger', '電話不可為空.');
        }
         if(!isset($data['name'])||$data['name']==''){
              return redirect('/customer/index')->with('alert-danger', '名稱不可為空.');
        }

     
     


        $customer = \App\models\Customer::where('phone',$data['phone'])->first();
        if($customer!=null){
            return redirect('/customer/index')->with('alert-danger', '電話重複,無法建立.');
        }

       
       

        $new_customer = new \App\models\Customer;
        $new_customer->phone = $data['phone'];
        $new_customer->name = $data['name'];
   
        if(isset($data['card_uuid'])&&$data['card_uuid']!=''){
            $old_card = \App\models\Customer::where('card_uuid',$data['card_uuid'])->first();
            if($old_card!==null){
                $old_card->card_uuid = '';
                $old_card->save();
            }

            $new_customer->card_uuid = $data['card_uuid'];
        }
        $new_customer->save();
        return redirect('/customer/index')->with('alert-success', '客戶建立成功.');
    }

  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = \App\models\Customer::where('id',$id)->first();
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
   
        if(isset($data['card_uuid'])&&$data['card_uuid']!=''){
            $old_customer->card_uuid = $data['card_uuid'];

            $old_card = \App\models\Customer::where('card_uuid',$data['card_uuid'])
                                        ->where('id','!=',$id)
                                        ->first();
            if($old_card!==null){
                $old_card->card_uuid = '';
                $old_card->save();
            }


        }
        else{
            $old_customer->card_uuid = null;
        }
        $old_customer->save();
        return redirect('/customer/index')->with('alert-success', '客戶修改功.');
    
    }


    public function checkcardid(Request $request){
        $data = $request->all();
        $check = \App\models\Customer::where('card_uuid',$data['card_id']);

        if($data['id']==null){
            $check = $check->first();
        }
        else{
            $check = $check->where('id','!=',$data['id'])
                            ->first();
        }

        if($check==null){
            return response()->json(1, 200);
        }
        else{
            return response()->json($check, 200);
        }
    }
    

    public function list(Request $request){
        $data = $request->all();
        $customers = \App\models\Customer::where('phone','like',$data['search'].'%')
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
        $spcard->family= $data['family'];
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
        $spcard->family= $data['family'];
        $spcard->user_id =$user->id;
        $spcard->save();
        return redirect('/customer/spcard')->with('alert-success', '規則修改成功.');
    }



}
