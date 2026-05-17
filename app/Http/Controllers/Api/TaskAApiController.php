<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\UserModelingAgent;
use App\Http\Controllers\Controller;
use App\Services\PersonaBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StructuredAgentResponse;

class TaskAApiController extends Controller
{
    /**
     * GET /api/v1/personas
     *
     * Returns all available persona IDs and labels.
     */
    public function personas(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => PersonaBuilder::forSelect(),
        ]);
    }

    /**
     * POST /api/v1/task-a/generate
     *
     * Request body:
     *   {
     *     "persona_id": "user_a",
     *     "product": "Mama Titi's Kitchen, Lagos"
     *   }
     *
     * Response:
     *   {
     *     "success": true,
     *     "data": {
     *       "rating": 2,
     *       "review": "I came in expecting...",
     *       "persona": { "id": "user_a", "name": "Chidi O.", "avg_rating": 2.1, ... }
     *     }
     *   }
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'persona_id' => 'required|string',
            'product'    => 'required|string|max:200',
        ]);

        $persona = PersonaBuilder::find($validated['persona_id']);

        if (! $persona) {
            return response()->json([
                'success' => false,
                'error'   => 'Unknown persona_id. Call GET /api/v1/personas for valid options.',
            ], 422);
        }

        $product = trim($validated['product']);

        try {
            /** @var StructuredAgentResponse $response */
            $response = (new UserModelingAgent($persona, $product))
                ->prompt("Generate a review for: {$product}");

            $result           = $response->toArray();
            $result['rating'] = max(1, min(5, (int) ($result['rating'] ?? 3)));

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'AI generation failed: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'rating'  => $result['rating'],
                'review'  => $result['review'],
                'persona' => [
                    'id'           => $persona['id'],
                    'name'         => $persona['name'],
                    'avg_rating'   => $persona['avg_rating'],
                    'review_count' => $persona['review_count'],
                    'style'        => $persona['style'],
                    'tone'         => $persona['tone'],
                ],
            ],
        ]);
    }
}
