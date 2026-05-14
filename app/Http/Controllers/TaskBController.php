<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskBController extends Controller
{
    public function index()
    {
        return view('tasks.task-b.index');
    }

    public function recommend(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'preferences' => 'nullable|string',
        ]);

        // Here you would typically call your recommendation logic, possibly using an AI service
        // For demonstration, we'll just return a dummy recommendation

        $recommendations = [
            'Product A',
            'Product B',
            'Product C',
        ];

        return response()->json([
            'user_id' => $validated['user_id'],
            'recommendations' => $recommendations,
        ]);
    }   
}
