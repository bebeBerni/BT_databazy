<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;


Route::apiResource('notes', NoteController::class);

Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);

Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);

Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);

Route::get('notes-actions/search', [NoteController::class, 'search']);

Route::patch('/notes/{id}/restore', [NoteController::class, 'restore']);

Route::apiResource('categories', CategoryController::class);

Route::patch('/notes/{id}/pin', [NoteController::class, 'pin']);
Route::patch('/notes/{id}/unpin', [NoteController::class, 'unpin']);
Route::patch('/notes/{id}/publish', [NoteController::class, 'publish']);
Route::patch('/notes/{id}/archive', [NoteController::class, 'archive']);
Route::patch('/notes/{id}/draft', [NoteController::class, 'draft']);

Route::apiResource('notes.tasks', TaskController::class);


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// zvyšné endpointy zostanú zatiaľ bez zmeny...
Route::apiResource('notes', NoteController::class);

// vy ich tam máte viac... nemažte si ich...

Route::middleware('auth:sanctum')->group(function () {
    // všetci prihlásení môžu čítať kategórie
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // iba admin môže vytvárať, upravovať, mazať kategórie
    Route::middleware('admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });
});


Route::middleware('auth:sanctum')->post('/logout-all', [AuthController::class, 'logoutAll']);

Route::middleware('auth:sanctum')->post('/change-password', [AuthController::class, 'changePassword']);

Route::middleware('auth:sanctum')->post('/profile', [AuthController::class, 'updateProfile']);
