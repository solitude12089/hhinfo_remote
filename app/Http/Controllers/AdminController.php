<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Auth;
class AdminController extends Controller
{
    
    public function getAccount(){
        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }
        $users = \App\User::with('userGroupList')->get();
        $groups = \App\models\Group::all()->pluck('name','id')->toArray();
        $roles = \App\models\Role::all()->pluck('name','id')->toArray();
        return view('admin.account',['users'=>$users,'roles'=>$roles,'groups'=>$groups]);
    }


    public function getCreateAccount(){

        $roles = \App\models\Role::all();
        $groups = \App\models\Group::all();
       
        return view('admin.create-account-modal',['roles'=>$roles,'groups'=>$groups]);

    }

    public function postCreateAccount(Request $request){

        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }

        $data = $request->all();




        if(!isset($data['account'])||$data['account']==''){
              return redirect('admin/account')->with('alert-danger', '帳號不可為空.');
        }
         if(!isset($data['name'])||$data['name']==''){
              return redirect('admin/account')->with('alert-danger', '姓名不可為空.');
        }

        if(!isset($data['pwd'])||!isset($data['dpwd'])){
              return redirect('admin/account')->with('alert-danger', '密碼不可為空.');
        }

        if($data['pwd']!=$data['dpwd']){
              return redirect('admin/account')->with('alert-danger', '確認密碼不符.');
        }


        $user = \App\User::where('account',$data['account'])->first();
        if($user!=null){
            return redirect('admin/account')->with('alert-danger', '帳號重複,無法建立.');
        }

        $new_user = new \App\User;
        $new_user->account = $data['account'];
        $new_user->name = $data['name'];
        $new_user->password = Hash::make($data['pwd']);
        $new_user->role = $data['role'];
        $new_user->save();

        if(isset($data['group'])&&count($data['group'])!=0){
            foreach ($data['group'] as $key => $value) {
                $new_user_group = new \App\models\UserGroup;
                $new_user_group->user_id=$new_user->id;
                $new_user_group->group_id=$value;
                $new_user_group->save();
                # code...
            }
        }
       
       
        return redirect('admin/account')->with('alert-success', '帳號建立成功.');
    }

    public function getEditAccount($id){
        $roles = \App\models\Role::all();
        $groups = \App\models\Group::all();
        $user = \App\User::with('userGroupList')
                            ->where('id','=',$id)
                            ->first();


        return view('admin.edit-account-modal',['roles'=>$roles,'groups'=>$groups,'user'=>$user]);
    }

    public function postEditAccount(Request $request,$id){

        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }

        $olduser = \App\User::where('id',$id)->first();
        if($olduser==null){
            return redirect('admin/account')->with('alert-danger', '資料錯誤,無法修改.');
        }

        $data = $request->all();
        if(!isset($data['account'])||$data['account']==''){
              return redirect('admin/account')->with('alert-danger', '帳號不可為空.');
        }
         if(!isset($data['name'])||$data['name']==''){
              return redirect('admin/account')->with('alert-danger', '姓名不可為空.');
        }
        if(!isset($data['pwd'])||!isset($data['dpwd'])){
              return redirect('admin/account')->with('alert-danger', '密碼不可為空.');
        }
        if($data['pwd']!=$data['dpwd']){
              return redirect('admin/account')->with('alert-danger', '確認密碼不符.');
        }
        $check = \App\User::where('id','!=',$id)
                            ->where('account','=',$data['account'])
                            ->first();
        if($check!=null){
            return redirect('admin/account')->with('alert-danger', '帳號重複,無法修改.');
        }
       
        $olduser->account = $data['account'];
        $olduser->name = $data['name'];

        if($data['pwd']!='nochange'){
            $olduser->password = Hash::make($data['pwd']);    
        }

        $olduser->role = $data['role'];
        $olduser->status=$data['status'];
        $olduser->save();

        \App\models\UserGroup::where('user_id',$olduser->id)->delete();

        if(isset($data['group'])&&count($data['group'])!=0){

            foreach ($data['group'] as $key => $value) {
                $new_user_group = new \App\models\UserGroup;
                $new_user_group->user_id=$olduser->id;
                $new_user_group->group_id=$value;
                $new_user_group->save();
            }
        }


       
        return redirect('admin/account')->with('alert-success', '帳號修改成功.');
    }



    public function getGroup(){
        $users = \App\User::all()->pluck('name','id')->toArray();
        $groups = \App\models\Group::with('userGroupList')
                                    ->get();
       
      
        return view('admin.group',['users'=>$users,'groups'=>$groups]);
    }


    public function getCreateGroup(){
        $users = \App\User::pluck('name','id')->toArray();
       
        return view('admin.create-group-modal',['users'=>$users]);

    }

    public function postCreateGroup(Request $request){

        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }
        $data = $request->all();

        if(!isset($data['name'])||$data['name']==''){
              return redirect('admin/group')->with('alert-danger', '名稱不可為空.');
        }

     


        $group = \App\models\Group::where('name',$data['name'])->first();
        if($group!=null){
            return redirect('admin/group')->with('alert-danger', '名稱重複,無法建立.');
        }

        $new_group = new \App\models\Group;
        $new_group->name = $data['name'];
        $new_group->save();

        if(isset($data['member'])&&count($data['member'])!=0){
            foreach ($data['member'] as $key => $value) {
                $new_user_group = new \App\models\UserGroup;
                $new_user_group->user_id=$value;
                $new_user_group->group_id=$new_group->id;
                $new_user_group->save();
                # code...
            }
        }

       
       
        return redirect('admin/group')->with('alert-success', '區域建立成功.');
    }

    public function getEditGroup($id){
       
        $group = \App\models\Group::with('userGroupList')->where('id',$id)->first();
        $users = \App\User::pluck('name','id')->toArray();
        return view('admin.edit-group-modal',['group'=>$group,'users'=>$users]);
    }

    public function postEditGroup(Request $request,$id){

        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }

        $oldgroup = \App\models\Group::where('id',$id)->first();
        if($oldgroup==null){
            return redirect('admin/group')->with('alert-danger', '資料錯誤,無法修改.');
        }

        $data = $request->all();
        
        if(!isset($data['name'])||$data['name']==''){
              return redirect('admin/group')->with('alert-danger', '名稱不可為空.');
        }
      
        $check = \App\models\Group::where('id','!=',$id)
                            ->where('name','=',$data['name'])
                            ->first();
        if($check!=null){
            return redirect('admin/group')->with('alert-danger', '名稱重複,無法修改.');
        }
      
        $oldgroup->name = $data['name'];
        $oldgroup->save();

        \App\models\UserGroup::where('group_id',$oldgroup->id)->delete();

        if(isset($data['member'])&&count($data['member'])!=0){

            foreach ($data['member'] as $key => $value) {
                $new_user_group = new \App\models\UserGroup;
                $new_user_group->user_id=$value;
                $new_user_group->group_id=$oldgroup->id;
                $new_user_group->save();
            }
        }

       
        return redirect('admin/group')->with('alert-success', '區域修改成功.');
    }





    public function getDevice(){
        $devices = \App\models\Device::all();
        $familys=[];
        foreach($devices as $device){
            $familys[$device->group_id][]=$device->family;
        }
        foreach ($familys as $key => $value){
            $familys[$key] = array_values(array_unique($value));
        }
     
        $groups = \App\models\Group::all()->pluck('name','id')->toArray();
        return view('admin.device',['devices'=>$devices,'groups'=>$groups,'familys'=>$familys]);
    }

    public function getCreateDevice(){
    
        $groups = \App\models\Group::all()->pluck('name','id')->toArray();
        return view('admin.create-device-modal',['groups'=>$groups]);

    }

    public function postCreateDevice(Request $request){

        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }
        $data = $request->all();

      
        if(!isset($data['IP'])||$data['IP']==''){
              return redirect('admin/device')->with('alert-danger', 'IP不可為空.');
        }
        if(!isset($data['name'])||$data['name']==''){
              return redirect('admin/device')->with('alert-danger', '名稱不可為空.');
        }

        if(!isset($data['group'])||$data['group']==''){
              return redirect('admin/device')->with('alert-danger', '區域不可為空.');
        }
        if(!isset($data['family'])||$data['family']==''){
              return redirect('admin/device')->with('alert-danger', '群組不可為空.');
        }

     


        $device = \App\models\Device::where('ip',$data['IP'])->first();
        if($device!=null){
            return redirect('admin/device')->with('alert-danger', 'IP重複,無法建立.');
        }
       

        $new_device = new \App\models\Device;
        $new_device->ip = $data['IP'];
        $new_device->name = $data['name'];
        $new_device->group_id = $data['group'];
        $new_device->family = $data['family'];

        if(isset($data['description'])&&$data['description']!=''){
            $new_device->description = $data['description'];
        }

        $new_device->type = $data['type'];
        $new_device->save();
        return redirect('admin/device')->with('alert-success', '設備建立成功.');
    }

    public function getEditDevice($id){
       
        $device = \App\models\Device::where('id',$id)->first();
        $groups = \App\models\Group::all()->pluck('name','id')->toArray();
       
        return view('admin.edit-device-modal',['groups'=>$groups,'device'=>$device]);
    }

    public function postEditDevice(Request $request,$id){

        if(Auth::user()->role!=9){
            return redirect('/')->with('alert-danger', '權限不符.');
        }

        $olddevice = \App\models\Device::where('id',$id)->first();
        if($olddevice==null){
            return redirect('admin/device')->with('alert-danger', '資料錯誤,無法修改.');
        }

        $data = $request->all();

        if(!isset($data['IP'])||$data['IP']==''){
              return redirect('admin/device')->with('alert-danger', 'IP不可為空.');
        }
        if(!isset($data['name'])||$data['name']==''){
              return redirect('admin/device')->with('alert-danger', '名稱不可為空.');
        }

        if(!isset($data['group'])||$data['group']==''){
              return redirect('admin/device')->with('alert-danger', '區域不可為空.');
        }
        if(!isset($data['family'])||$data['family']==''){
              return redirect('admin/device')->with('alert-danger', '群組不可為空.');
        }

     


        $device = \App\models\Device::where('ip',$data['IP'])
                                    ->where('id','!=',$id)
                                    ->first();
        if($device!=null){
            return redirect('admin/device')->with('alert-danger', 'IP重複,無法修改.');
        }


      
      
        $olddevice->ip = $data['IP'];
        $olddevice->name = $data['name'];
        $olddevice->group_id = $data['group'];
        $olddevice->family = $data['family'];
        $olddevice->status = $data['status'];
        $olddevice->description = $data['description'];
        $olddevice->type = $data['type'];
        $olddevice->save();

    
       

       
        return redirect('admin/device')->with('alert-success', '設備修改成功.');
    }

    // public function get
}
