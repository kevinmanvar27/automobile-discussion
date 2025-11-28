<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommentImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CommentImageController extends Controller
{
    public function destroy(CommentImage $commentImage)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'You must be logged in to delete an image.'
            ], 401);
        }

        // Check if the user owns the comment that the image belongs to
        if (Auth::id() !== $commentImage->comment->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'You are not authorized to delete this image.'
            ], 403);
        }

        try {
            // Delete the image file from storage
            Storage::disk('public')->delete($commentImage->image_path);
            
            // Delete the image record from the database
            $commentImage->delete();

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