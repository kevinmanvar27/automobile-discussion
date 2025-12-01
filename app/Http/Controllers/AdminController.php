<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Mail\UserPasswordMail;

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
            if ($user->email === 'madhuram.motors@gmail.com') {
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
        // Get all unverified users (pending verification)
        $pendingUsers = User::where('verified', false)->orderBy('created_at', 'asc')->get();
        
        // Get recent threads
        $recentThreads = Thread::with('user')->latest()->take(5)->get();
        
        // Get recent comments
        $recentComments = Comment::with(['user', 'thread'])->latest()->take(5)->get();
        
        // Get statistics
        $totalUsers = User::count();
        $totalThreads = Thread::count();
        $totalComments = Comment::count();
        $verifiedUsers = User::where('verified', true)->count();
        
        return view('admin.dashboard', compact('pendingUsers', 'recentThreads', 'recentComments', 'totalUsers', 'totalThreads', 'totalComments', 'verifiedUsers'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function viewUser(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function editUser(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|numeric|digits:10',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'verified' => 'boolean',
        ]);

        // Update user data
        $user->update($request->only(['name', 'shop_name', 'mobile_no', 'city', 'address', 'verified']));

        // Return JSON response for AJAX requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user
            ]);
        }

        // For non-AJAX requests, redirect back with success message
        return back()->with('success', 'User updated successfully.');
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
        
        // Send email with the generated password
        Mail::to($user->email)->send(new UserPasswordMail($user, $password));
        
        return back()->with('success', 'Password generated and email sent to user.');
    }

    public function usersByComments()
    {
        // Get users with their comment counts, ordered by comment count descending
        $users = User::withCount('comments')
                    ->orderBy('comments_count', 'desc')
                    ->get();
        
        return view('admin.users-by-comments', compact('users'));
    }
}