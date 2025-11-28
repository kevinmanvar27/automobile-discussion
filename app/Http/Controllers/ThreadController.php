<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Thread;
use App\Models\ThreadImage;
use App\Models\ThreadRating;

class ThreadController extends Controller
{
    public function index(Request $request)
    {
        $query = Thread::with(['user', 'images', 'ratings'])->withCount('comments')->latest();
        
        // Apply search filter if search parameter is provided
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('subject', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $threads = $query->paginate(25);
        
        // Add average rating to each thread
        foreach ($threads as $thread) {
            $thread->average_rating = $thread->averageRating;
            // Get user's rating if authenticated
            if (Auth::check()) {
                $userRating = $thread->ratings->where('user_id', Auth::id())->first();
                $thread->user_rating = $userRating ? $userRating->rating : 0;
            } else {
                $thread->user_rating = 0;
            }
        }
        
        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'threads' => $threads->items(),
                'pagination' => [
                    'current_page' => $threads->currentPage(),
                    'last_page' => $threads->lastPage(),
                    'per_page' => $threads->perPage(),
                    'total' => $threads->total(),
                ]
            ]);
        }
        
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
            if ($request->wantsJson() || $request->ajax()) {
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $thread = Thread::create([
                'user_id' => Auth::id(),
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
            
            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('thread_images', 'public');
                    ThreadImage::create([
                        'thread_id' => $thread->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            // If this is an AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                // Load the images relationship for the thread
                $thread->load('images');
                
                return response()->json([
                    'success' => true,
                    'thread' => $thread
                ]);
            }

            return redirect()->route('threads.show', $thread)->with('success', 'Thread created successfully.');
        } catch (\Exception $e) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
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
        $thread->load(['user', 'comments.user', 'images', 'ratings']);
        
        // Add average rating to the thread
        $thread->average_rating = $thread->averageRating;
        
        // Get user's rating if authenticated
        if (Auth::check()) {
            $userRating = $thread->ratings->where('user_id', Auth::id())->first();
            $thread->user_rating = $userRating ? $userRating->rating : 0;
        } else {
            $thread->user_rating = 0;
        }
        
        return view('threads.show', compact('thread'));
    }
    
    public function edit(Thread $thread)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'You must be logged in to edit a thread.'
            ], 401);
        }
        
        // Check if the authenticated user owns the thread
        if (Auth::id() !== $thread->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'You are not authorized to edit this thread.'
            ], 403);
        }
        
        // Load images relationship
        $thread->load('images');
        
        // Return JSON response for AJAX requests
        return response()->json($thread);
    }
    
    public function update(Request $request, Thread $thread)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You must be logged in to edit a thread.'
                ], 401);
            }
            
            return redirect()->route('login');
        }
        
        // Check if the authenticated user owns the thread
        if (Auth::id() !== $thread->user_id) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You are not authorized to edit this thread.'
                ], 403);
            }
            
            return back()->withErrors(['error' => 'You are not authorized to edit this thread.']);
        }
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $thread->update([
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
            
            // Handle multiple image uploads if provided
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('thread_images', 'public');
                    ThreadImage::create([
                        'thread_id' => $thread->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }
            
            // If this is an AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                // Load the images relationship for the thread
                $thread->load('images');
                
                return response()->json([
                    'success' => true,
                    'thread' => $thread
                ]);
            }
            
            return redirect()->route('threads.show', $thread)->with('success', 'Thread updated successfully.');
        } catch (\Exception $e) {
            // If this is an AJAX request, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Error updating thread.']);
        }
    }
    
    public function rate(Request $request, Thread $thread)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'You must be logged in to rate a thread.'
            ], 401);
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);
        
        try {
            // Check if user has already rated this thread
            $existingRating = ThreadRating::where('thread_id', $thread->id)
                ->where('user_id', Auth::id())
                ->first();
            
            if ($existingRating) {
                // Update existing rating
                $existingRating->update([
                    'rating' => $request->rating
                ]);
            } else {
                // Create new rating
                ThreadRating::create([
                    'thread_id' => $thread->id,
                    'user_id' => Auth::id(),
                    'rating' => $request->rating
                ]);
            }
            
            // Reload thread with ratings to get updated average
            $thread->load('ratings');
            $averageRating = $thread->averageRating;
            $ratingCount = $thread->ratings->count();
            
            return response()->json([
                'success' => true,
                'average_rating' => $averageRating,
                'rating_count' => $ratingCount,
                'message' => $existingRating ? 'Rating updated successfully.' : 'Rating added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}