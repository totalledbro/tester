<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggota = Anggota::all();
        return view('anggota.index', compact('anggota'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'namadepan' => 'required',
            'namablkg' => 'required',
            'email' => 'required|email|unique:anggota',
            'password' => 'required|min:8',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($request->password);

        // Create a new anggota with hashed password
        $anggota=Anggota::create([
            'namadepan' => $request->namadepan,
            'namablkg' => $request->namablkg,
            'email' => $request->email,
            'password' => $hashedPassword,
        ]);
        Log::info('Anggota created:', $anggota->toArray());

        // Redirect to the index page
        return redirect('/verifikasi');
    }

    public function edit($id)
    {
        $anggota = Anggota::find($id);
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'namadepan' => 'required',
            'namablkg' => 'required',
            'email' => 'required|email|unique:anggota,email,'.$id,
            'password' => 'required|min:8',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($request->password);

        // Find the anggota and update with hashed password
        $anggota = Anggota::findOrFail($id);
        $anggota->update([
            'namadepan' => $request->namadepan,
            'namablkg' => $request->namablkg,
            'email' => $request->email,
            'password' => $hashedPassword,
        ]);

        // Redirect back to the index page
        return view('anggota.index')->with('success', 'Anggota updated successfully.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::guard('anggota')->attempt($request->only('email', 'password'))) {
            // Authentication successful
            return redirect()->intended('/dashboard'); // Redirect to the intended page or dashboard
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}

