<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ThreadImageController;
use App\Http\Controllers\CommentImageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

// Public routes
Route::get('/', [AuthController::class, 'index'])->name('home');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Forgot Password Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Notification routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
});

// Admin routes (protected by admin middleware)
Route::middleware(['auth', App\Http\Middleware\EnsureAdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}', [AdminController::class, 'viewUser'])->name('admin.users.view');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/generate-password/{user}', [AdminController::class, 'generatePassword'])->name('admin.generate-password');
});

// Discussion routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/discussion', [ThreadController::class, 'index'])->name('discussion.index');
    Route::get('/threads/create', [ThreadController::class, 'create'])->name('threads.create');
    Route::post('/threads', [ThreadController::class, 'store'])->name('threads.store');
    Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show')
        ->missing(function () {
            return redirect()->route('discussion.index')->with('error', 'Thread not found.');
        });
    Route::get('/threads/{thread}/edit', [ThreadController::class, 'edit'])->name('threads.edit');
    Route::put('/threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    
    // Thread rating route
    Route::post('/threads/{thread}/rate', [ThreadController::class, 'rate'])->name('threads.rate');
    
    Route::post('/threads/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/threads/{thread}/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    
    // Image deletion routes
    Route::delete('/thread-images/{threadImage}', [ThreadImageController::class, 'destroy'])->name('thread-images.destroy');
    Route::delete('/comment-images/{commentImage}', [CommentImageController::class, 'destroy'])->name('comment-images.destroy');
});