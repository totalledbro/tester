<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class LoginController extends Controller
{
    //
    public function actionlogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role === 'administrator') {
                return redirect()->route('admin');
            } else {
                return redirect()->route('dash');
            }
        }

        Session::flash('error', 'email atau Password Salah');
        return redirect()->route('welcome');
    }

    public function actionlogout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
