<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThreadImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ThreadImageController extends Controller
{
    public function destroy(ThreadImage $threadImage)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'You must be logged in to delete an image.'
            ], 401);
        }

        // Check if the user owns the thread that the image belongs to
        if (Auth::id() !== $threadImage->thread->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'You are not authorized to delete this image.'
            ], 403);
        }

        try {
            // Delete the image file from storage
            Storage::disk('public')->delete($threadImage->image_path);
            
            // Delete the image record from the database
            $threadImage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }
}