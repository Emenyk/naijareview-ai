<?php

namespace App\Http\Controllers;

use App\Ai\Agents\UserModelingAgent;
use Illuminate\Http\Request;

class TaskAController extends Controller
{
    public function index()
    {
        // for testing Ai agent integration
        // $response = (new UserModelingAgent)
        // ->prompt('Say hello in Nigerian Pidgin English');
        // return view('tasks.task-a.index', ['response' => $response]);

        return view('tasks.task-a.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'user_input' => 'required|string',
        ]);

        $userInput = $request->input('user_input');

        // Here you would typically call your AI service to generate the user model
        // For demonstration, we'll just return a dummy response

        $generatedModel = [
            'name' => 'John Doe',
            'age' => 30,
            'interests' => ['technology', 'sports', 'music'],
            'preferences' => ['email_notifications' => true, 'sms_notifications' => false],
        ];

        return response()->json([
            'success' => true,
            'data' => $generatedModel,
        ]);
    }
}
