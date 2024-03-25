<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash class
use App\Administrator;

class AdministratorController extends Controller
{
    public function index()
    {
        $administrators = Administrator::all();
        return view('administrators.index', compact('administrators'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'namadepan' => 'required',
            'namablkg' => 'required',
            'email' => 'required|email|unique:administrators',
            'password' => 'required',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($request->password);

        // Create a new administrator with hashed password
        Administrator::create([
            'namadepan' => $request->namadepan,
            'namablkg' => $request->namablkg,
            'email' => $request->email,
            'password' => $hashedPassword,
        ]);

        // Redirect to the index page
        return redirect()->route('administrators.index')
                         ->with('success', 'Administrator created successfully.');
    }

    public function edit($id)
    {
        $administrator = Administrator::findOrFail($id);
        return view('administrators.edit', compact('administrator'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'namadepan' => 'required',
            'namablkg' => 'required',
            'email' => 'required|email|unique:administrators,email,'.$id,
            'password' => 'required',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($request->password);

        // Find the administrator and update with hashed password
        $administrator = Administrator::findOrFail($id);
        $administrator->update([
            'namadepan' => $request->namadepan,
            'namablkg' => $request->namablkg,
            'email' => $request->email,
            'password' => $hashedPassword,
        ]);

        // Redirect back to the index page
        return redirect()->route('administrators.index')
                         ->with('success', 'Administrator updated successfully.');
    }

    public function destroy($id)
    {
        $administrator = Administrator::findOrFail($id);
        $administrator->delete();
        return redirect()->route('administrators.index')
                         ->with('success', 'Administrator deleted successfully.');
    }
}
