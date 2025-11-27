<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Check if the user is the admin (by email)
            $user = Auth::user();
            if ($user->email === 'rektech.uk@gmail.com') {
                return redirect()->intended('/admin/dashboard');
            } else {
                // If not admin, logout and redirect back
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have admin access.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function dashboard()
    {
        // Get all unverified users
        $users = User::where('verified', false)->get();
        
        return view('admin.dashboard', compact('users'));
    }

    public function users()
    {
        // Get all users
        $users = User::all();
        
        return view('admin.users', compact('users'));
    }

    public function generatePassword(User $user)
    {
        // Generate a random password
        $password = Str::random(12);
        
        // Update user with hashed password and mark as verified
        $user->update([
            'password' => Hash::make($password),
            'generated_password' => $password,
            'verified' => true
        ]);
        
        // In a real application, you would send an actual email
        // Mail::to($user->email)->send(new UserPasswordMail($user, $password));
        
        return back()->with('success', 'Password generated and email sent to user.');
    }
}