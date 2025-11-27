<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Thread;
use App\Models\Comment;

class ThreadController extends Controller
{
    public function index()
    {
        // Get all threads with their users, ordered by latest first
        $threads = Thread::with('user')->latest()->get();
        
        return view('discussion.index', compact('threads'));
    }

    public function create()
    {
        return view('threads.create');
    }

    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // If this is an AJAX request, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You must be logged in to create a thread.'
                ], 401);
            }
            
            return redirect()->route('login');
        }
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            $thread = Thread::create([
                'user_id' => Auth::id(),
                'subject' => $request->subject,
                'description' => $request->description,
            ]);

            // If this is an AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'thread' => $thread
                ]);
            }

            return redirect()->route('threads.show', $thread)->with('success', 'Thread created successfully.');
        } catch (\Exception $e) {
            // If this is an AJAX request, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error creating thread.']);
        }
    }

    public function show(Thread $thread)
    {
        // Load the thread with its user and comments (with their users)
        $thread->load(['user', 'comments.user']);
        
        return view('threads.show', compact('thread'));
    }
}