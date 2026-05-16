<?php

namespace App\Http\Controllers;

use App\Ai\Agents\UserModelingAgent;
use App\Services\DatasetService;
use App\Services\PersonaBuilder;
use Illuminate\Http\Request;

class TaskAController extends Controller
{
    public function __construct(
        private DatasetService $dataset,
        private PersonaBuilder $persona
    ) {}

    /**
     * Show the Task A page with sample users
     */
    public function index()
    {
        $sampleUsers = $this->dataset->getSampleUsers();

        return view('tasks.task-a.index', [
            'sampleUsers' => $sampleUsers,
            'result'      => null,
            'persona'     => null,
        ]);
    }

    /**
     * Handle the form submission and generate review
     */
    public function generate(Request $request)
    {
        // Validate input
        $request->validate([
            'user_id' => 'required|string',
            'product' => 'required|string|max:200',
        ]);

        $userId  = $request->user_id;
        $product = $request->product;

        // Get user reviews from dataset
        $reviews = $this->dataset->getUserReviews($userId);

        // Build persona from reviews
        $persona = $this->persona->build($reviews);

        // If no reviews found, return with error
        if ($persona['review_count'] === 0) {
            return back()->with('error', 
                'No reviews found for this user in our dataset.'
            );
        }

        // Create and prompt the agent
        $agent = new UserModelingAgent(
            personaSummary: $persona['summary'],
            sampleReviews:  $persona['samples'],
            product:        $product
        );

        $response = $agent->prompt(
            "Generate a review for: {$product}"
        );

        // Get structured result
        $result = [
            'rating'     => $response['rating'],
            'review'     => $response['review'],
            'confidence' => $response['confidence'],
        ];

        // Return the same page with results
        return view('tasks.task-a.index', [
            'sampleUsers' => $this->dataset->getSampleUsers(),
            'result'      => $result,
            'persona'     => $persona,
            'selectedUser'=> $userId,
            'product'     => $product,
        ]);
    }
}