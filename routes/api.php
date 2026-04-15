<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // Notes
    Route::apiResource('notes', NoteController::class);
    //Route::get('/notes', [NoteController::class, 'index']);
    Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);
    Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);
    Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);

    Route::patch('notes/{id}/pin', [NoteController::class, 'pin']);
    Route::patch('notes/{id}/unpin', [NoteController::class, 'unpin']);
    Route::patch('notes/{id}/publish', [NoteController::class, 'publish']);
    Route::patch('notes/{id}/archive', [NoteController::class, 'archive']);
    Route::patch('notes/{id}/draft', [NoteController::class, 'draft']);

    // Tasks
    Route::apiResource('notes.tasks', TaskController::class);

    // Profile / auth actions
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Categories - all logged users can read
    Route::apiResource('categories', CategoryController::class);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Categories - only admin can create, update, delete
    Route::apiResource('categories', CategoryController::class);
});

Route::get('/attachments/{attachments:public_id}/link', [AttachmentController::class, 'link']);

Route::apiResource('notes', NoteController::class);
Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);



Route::get('notes/{note}/comments', [CommentController::class, 'indexForNote']);
Route::post('notes/{note}/comments', [CommentController::class, 'storeForNote']);

Route::get('notes/{note}/tasks/{task}/comments', [CommentController::class, 'indexForTask']);
Route::post('notes/{note}/tasks/{task}/comments', [CommentController::class, 'storeForTask']);

Route::patch('comments/{comment}', [CommentController::class, 'update']);
Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

Route::get('/myNotes', [NoteController::class, 'myNotes']);
