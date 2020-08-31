<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
class AccountController extends Controller
{
   
    public function getReset(){
        return view('account.reset');
    }
    public function postReset(Request $request){
        $data = $request->all();
      
        if(!isset($data['pwd'])||!isset($data['dpwd'])){
              return redirect('account/reset')->with('alert-danger', '密碼不可為空.');
        }

        if($data['pwd']!=$data['dpwd']){
              return redirect('account/reset')->with('alert-danger', '確認密碼不符.');
        }

        $user = Auth::user();
        $user->password = Hash::make($data['pwd']);
        $user->save();


     
        return redirect('account/reset')->with('alert-success', '變更成功.');
    }
}
