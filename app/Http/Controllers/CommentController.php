<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Thread;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // If this is an AJAX request, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You must be logged in to add a comment.'
                ], 401);
            }
            
            return redirect()->route('login');
        }
        
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $comment = Comment::create([
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'content' => $request->content,
            ]);

            // Load the user relationship for the comment
            $comment->load('user');

            // If this is an AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'comment' => $comment
                ]);
            }

            return back()->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            // If this is an AJAX request, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error adding comment.']);
        }
    }
}