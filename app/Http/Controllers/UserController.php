<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    // Existing methods...

    /**
     * Show the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            Log::info('Validation passed', $validatedData);

            $validatedData['first_name'] = Str::lower($validatedData['first_name']);
            $validatedData['last_name'] = Str::lower($validatedData['last_name']);
            $validatedData['email'] = Str::lower($validatedData['email']);
            $validatedData['password'] = Hash::make($validatedData['password']);

            // Check if the email already exists
            if (User::where('email', $validatedData['email'])->exists()) {
                Log::error('Email already exists', ['email' => $validatedData['email']]);
                return response()->json(['success' => false, 'message' => 'email_taken'], 422);
            }

            $user = User::create($validatedData);
            Log::info('User created', ['user_id' => $user->id]);

            $user->sendEmailVerificationNotification();
            Log::info('Email verification sent', ['user_id' => $user->id]);

            return response()->json(['success' => true, 'message' => 'Registration successful. Please check your email to verify your account.']);
        } catch (\Exception $e) {
            Log::error('Error in registration', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Registration failed. Please try again later.']);
        }
    }

    /**
     * Handle the password change request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password successfully changed.']);
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('resetberhasil')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
