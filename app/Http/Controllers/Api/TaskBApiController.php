<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\RecommendationAgent;
use App\Http\Controllers\Controller;
use App\Services\DatasetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Ai\Responses\StructuredAgentResponse;

class TaskBApiController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * POST /api/v1/task-b/recommend
     *
     * Request body:
     *   {
     *     "scenario": "normal" | "cold_start" | "cross_domain",
     *     "persona_description": "Chidi, 28, Lagos. Loves spicy local food...",
     *     "domain": "Restaurants",
     *     "location": "Lagos"
     *   }
     *
     * Response:
     *   {
     *     "success": true,
     *     "data": {
     *       "recommendations": [{ "name": "...", "reason": "..." }],
     *       "session_token": "uuid",
     *       "scenario": "normal"
     *     }
     *   }
     */
    public function recommend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario'            => 'required|in:normal,cold_start,cross_domain',
            'persona_description' => 'required|string|max:1000',
            'domain'              => 'required|string|max:100',
            'location'            => 'nullable|string|max:100',
        ]);

        $scenario    = $validated['scenario'];
        $persona     = trim($validated['persona_description']);
        $domain      = trim($validated['domain']);
        $location    = trim($validated['location'] ?? 'Lagos');

        $businesses  = DatasetService::forRecommendation($domain, $location, $scenario);
        $catalogText = DatasetService::formatCatalogForPrompt($businesses);

        $userPrompt = "User Profile: {$persona}\nDomain: {$domain}\nLocation: {$location}\n\nGenerate 10 personalised recommendations for this user.";

        try {
            /** @var StructuredAgentResponse $response */
            $response = (new RecommendationAgent($scenario, $persona, $domain, $location, $catalogText))
                ->prompt($userPrompt);

            $structured      = $response->toArray();
            $recommendations = $structured['recommendations'] ?? [];

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Recommendation failed: ' . $e->getMessage(),
            ], 500);
        }

        $sessionToken = (string) Str::uuid();

        Cache::put("rec_api_{$sessionToken}", [
            'scenario'    => $scenario,
            'persona'     => $persona,
            'domain'      => $domain,
            'location'    => $location,
            'catalogText' => $catalogText,
            'history'     => [
                ['role' => 'user',      'content' => $userPrompt],
                ['role' => 'assistant', 'content' => $response->text],
            ],
        ], self::CACHE_TTL);

        return response()->json([
            'success' => true,
            'data'    => [
                'recommendations' => $recommendations,
                'session_token'   => $sessionToken,
                'scenario'        => $scenario,
            ],
        ]);
    }

    /**
     * POST /api/v1/task-b/refine
     *
     * Request body:
     *   {
     *     "session_token": "uuid-from-recommend-response",
     *     "refinement": "Remove expensive options. Nigerian food only."
     *   }
     *
     * Response:
     *   {
     *     "success": true,
     *     "data": {
     *       "recommendations": [{ "name": "...", "reason": "..." }],
     *       "session_token": "uuid"
     *     }
     *   }
     */
    public function refine(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => 'required|string',
            'refinement'    => 'required|string|max:500',
        ]);

        $cacheKey = "rec_api_{$validated['session_token']}";
        $session  = Cache::get($cacheKey);

        if (! $session) {
            return response()->json([
                'success' => false,
                'error'   => 'Session expired or not found. Make a fresh /recommend request to start a new conversation.',
            ], 404);
        }

        $refinement  = trim($validated['refinement']);
        $history     = $session['history'] ?? [];
        $catalogText = $session['catalogText'] ?? '';

        try {
            /** @var StructuredAgentResponse $response */
            $response = (new RecommendationAgent(
                $session['scenario'],
                $session['persona'],
                $session['domain'],
                $session['location'],
                $catalogText,
                $history,
            ))->prompt($refinement);

            $structured      = $response->toArray();
            $recommendations = $structured['recommendations'] ?? [];

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Refinement failed: ' . $e->getMessage(),
            ], 500);
        }

        $history[] = ['role' => 'user',      'content' => $refinement];
        $history[] = ['role' => 'assistant', 'content' => $response->text];

        Cache::put($cacheKey, array_merge($session, ['history' => $history]), self::CACHE_TTL);

        return response()->json([
            'success' => true,
            'data'    => [
                'recommendations' => $recommendations,
                'session_token'   => $validated['session_token'],
            ],
        ]);
    }
}
