<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Thread;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You must be logged in to add a comment.'
                ], 401);
            }
            
            return redirect()->route('login');
        }
        
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $imagePath = null;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('comment_images', 'public');
            }

            $comment = Comment::create([
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'content' => $request->content,
                'image_path' => $imagePath,
            ]);

            // Load the user relationship for the comment
            $comment->load('user');

            // If this is an AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'comment' => $comment
                ]);
            }

            return back()->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error adding comment.']);
        }
    }
    
    public function edit(Thread $thread, Comment $comment)
    {
        // This method is no longer needed as we're using modals
        // But we'll keep it for direct access if needed
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Check if the authenticated user owns the comment
        if (Auth::id() !== $comment->user_id) {
            return back()->withErrors(['error' => 'You are not authorized to edit this comment.']);
        }
        
        return view('comments.edit', compact('thread', 'comment'));
    }
    
    public function update(Request $request, Thread $thread, Comment $comment)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You must be logged in to edit a comment.'
                ], 401);
            }
            
            return redirect()->route('login');
        }
        
        // Check if the authenticated user owns the comment
        if (Auth::id() !== $comment->user_id) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You are not authorized to edit this comment.'
                ], 403);
            }
            
            return back()->withErrors(['error' => 'You are not authorized to edit this comment.']);
        }
        
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            // Prepare update data
            $updateData = [
                'content' => $request->content,
            ];
            
            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($comment->image_path) {
                    Storage::disk('public')->delete($comment->image_path);
                }
                
                // Store new image
                $imagePath = $request->file('image')->store('comment_images', 'public');
                $updateData['image_path'] = $imagePath;
            }
            
            $comment->update($updateData);
            
            // If this is an AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                // Load the user relationship for the comment
                $comment->load('user');
                
                return response()->json([
                    'success' => true,
                    'comment' => $comment
                ]);
            }
            
            return redirect()->route('threads.show', $thread)->with('success', 'Comment updated successfully.');
        } catch (\Exception $e) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Error updating comment.']);
        }
    }
}