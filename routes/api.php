<?php

use App\Http\Controllers\Api\TaskAApiController;
use App\Http\Controllers\Api\TaskBApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NaijaReview AI — JSON API
|--------------------------------------------------------------------------
| Task A: POST /api/v1/task-a/generate
| Task B: POST /api/v1/task-b/recommend
|         POST /api/v1/task-b/refine
|         GET  /api/v1/personas
|         GET  /api/v1/health
*/

Route::prefix('v1')->group(function () {

    Route::get('/health', fn () => response()->json(['status' => 'ok', 'app' => 'NaijaReview AI']));

    Route::get('/personas', [TaskAApiController::class, 'personas']);

    // Task A
    Route::prefix('task-a')->group(function () {
        Route::post('/generate', [TaskAApiController::class, 'generate']);
    });

    // Task B
    Route::prefix('task-b')->group(function () {
        Route::post('/recommend', [TaskBApiController::class, 'recommend']);
        Route::post('/refine',    [TaskBApiController::class, 'refine']);
    });
});
