<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use App\User;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest', ['except' => 'logout']);
    }


    public function getLogin(){
        if(Auth::user()!=null){
            return  redirect('/');
        }
        else{
            return view('auth.login');
        }

      
    }
    public function postLogin(Request $request)
    {

        try {
            $data = $request->all();
      
            $user = User::where('account','=',$data['account'])->first();
          
             if($user){
                $result = Auth::attempt(['account' => $data['account'], 'password' => $data['password']]);
                if(!$result){
                    return back()->withErrors(['account' => ['Login Fail.']]);
                }
                Auth::login($user,true);
                return redirect()->intended('/');
            }else{
                return back()->withErrors(['account' => ['Login Fail']]);
            }

        } catch (\Exception $e) {
            dd($e);
        }
       
      
        
    }

    public function getLogout(){
        Auth::logout();
	    return redirect('/login');
    }

}
