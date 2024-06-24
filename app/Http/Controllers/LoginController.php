<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function actionlogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $emailVerified = !is_null($user->email_verified_at);
    
            if (!$emailVerified) {
                Auth::logout();
                // Send verification email if not verified
                $user->sendEmailVerificationNotification();
                Log::info('Email not verified. Sending verification email.');
                return response()->json(['success' => false, 'message' => 'email_not_verified']);
            }
    
            $redirectUrl = $user->role === 'administrator' ? route('stats') : route('dash');
            Log::info('Login successful. Redirecting user.', ['redirect_url' => $redirectUrl]);
            return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
        }
    
        Log::info('Login failed. Invalid credentials.');
        return response()->json(['success' => false, 'message' => 'invalid_credentials']);
    }
    
    public function actionlogout()
    {
        Auth::logout();
        return redirect()->route('dash');
    }
}
