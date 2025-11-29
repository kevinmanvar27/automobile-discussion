<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\UserPasswordMail;

class AuthController extends Controller
{
    public function index()
    {
        // If user is logged in, redirect to discussion page
        if (Auth::check()) {
            return redirect()->route('discussion.index');
        }
        
        // Otherwise, redirect to login page
        return redirect()->route('login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'mobile_no' => 'required|numeric|digits:10',
            'email' => 'required|string|email|max:255|unique:users',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        // Generate a temporary placeholder password
        $placeholderPassword = Hash::make(Str::random(40)); // Long random string

        $user = User::create([
            'name' => $request->name,
            'shop_name' => $request->shop_name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'city' => $request->city,
            'address' => $request->address,
            'verified' => false, // User needs admin verification
            'password' => $placeholderPassword, // Placeholder password
        ]);

        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Registration successful. Please wait for admin verification.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Find the user
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists, is verified, and password is correct
        if ($user && $user->verified && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            
            return redirect()->intended('discussion');
        }

        // Special case: if user exists but password is the placeholder, they need to reset
        if ($user && !$user->verified) {
            return back()->withErrors([
                'email' => 'Your account is not verified by admin yet.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email address.']);
        }

        // Generate a new secure password
        $newPassword = Str::random(12);
        
        // Update user's password
        $user->password = Hash::make($newPassword);
        $user->save();

        // Send the email with the new password
        Mail::to($user->email)->send(new UserPasswordMail($user, $newPassword));

        return back()->with('success', 'Password reset link sent to your email address.');
    }
}