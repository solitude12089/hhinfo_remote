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
              return redirect('/customer')->with('alert-danger', '電話不可為空.');
        }
         if(!isset($data['name'])||$data['name']==''){
              return redirect('/customer')->with('alert-danger', '名稱不可為空.');
        }

     
     


        $customer = \App\models\Customer::where('phone',$data['phone'])->first();
        if($customer!=null){
            return redirect('/customer')->with('alert-danger', '電話重複,無法建立.');
        }

       

        $new_customer = new \App\models\Customer;
        $new_customer->phone = $data['phone'];
        $new_customer->name = $data['name'];
   
        if(isset($data['card_uuid'])&&$data['card_uuid']!=''){
             $new_customer->card_uuid = $data['card_uuid'];
        }
        $new_customer->save();
        return redirect('/customer')->with('alert-success', '客戶建立成功.');
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
              return redirect('/customer')->with('alert-danger', '電話不可為空.');
        }
         if(!isset($data['name'])||$data['name']==''){
              return redirect('/customer')->with('alert-danger', '名稱不可為空.');
        }

     
     


        $customer = \App\models\Customer::where('phone',$data['phone'])
                                        ->where('id','!=',$id)->first();
        if($customer!=null){
            return redirect('/customer')->with('alert-danger', '電話重複,無法修改.');
        }

     
        $old_customer = \App\models\Customer::where('id','=',$id)->first();
        $old_customer->phone = $data['phone'];
        $old_customer->name = $data['name'];
        $old_customer->status = $data['status'];
   
        if(isset($data['card_uuid'])&&$data['card_uuid']!=''){
             $old_customer->card_uuid = $data['card_uuid'];
        }
        else{
            $old_customer->card_uuid = null;
        }
        $old_customer->save();
        return redirect('/customer')->with('alert-success', '客戶修改功.');
    
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
}
