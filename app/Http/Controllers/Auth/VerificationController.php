<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function verifyAndLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if the user is already verified
            if (!$user->hasVerifiedEmail()) {
                // Verify the user's email
                $user->markEmailAsVerified();
                event(new Verified($user)); // Fire the event that the email is verified
            }

            // Log the user in
            Auth::login($user);

            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => [__('The provided credentials are incorrect.')],
        ]);
    }
}
