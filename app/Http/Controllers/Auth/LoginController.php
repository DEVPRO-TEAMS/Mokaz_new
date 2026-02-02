<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


      public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password']))) {
            $user = auth()->user();

            User::where('uuid', $user->uuid)->update(['last_login' => Carbon::now()->format('Y-m-d H:i:s')]);
            // dd($user);
            if ($user->user_type == 'admin') {

                return redirect()->route('admin.index');
            }elseif ($user->user_type == 'partner') {
                return redirect()->route('partner.index');
            }elseif ($user->user_type == 'comManager') {
                return redirect()->route('comManager.statistics');
            }elseif ($user->user_type == 'user') {
                
                return redirect()->route('user.index');
            }else {
                return redirect()->route('home');
            }

        } else {
            return redirect()->back()->with('error', 'L\'adresse électronique et le mot de passe sont incorrects.');
        }
    }
}
