<?php

use App\Http\Controllers\TaskAController;
use App\Http\Controllers\TaskBController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn() => redirect()->route('task-a'));

// Task A — User Modeling
Route::get('/task-a', [TaskAController::class, 'index'])
    ->name('task-a');
Route::post('/task-a/generate', [TaskAController::class, 'generate'])
    ->name('task-a.generate');

// Task B — Recommendation (placeholder for now)
Route::get('/task-b', [TaskBController::class, 'index'])
    ->name('task-b');
Route::post('/task-b/recommend', [TaskBController::class, 'recommend'])
    ->name('task-b.recommend');
Route::post('/task-b/refine', [TaskBController::class, 'refine'])
    ->name('task-b.refine');