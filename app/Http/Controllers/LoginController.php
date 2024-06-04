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
            $redirectUrl = $user->role === 'administrator' ? route('stats') : route('dash');
    
            return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
        }
    
        return response()->json(['success' => false, 'message' => 'Invalid credentials']);
    }
    

    public function actionlogout()
    {
        Auth::logout();
        return redirect()->route('dash');
    }
}
