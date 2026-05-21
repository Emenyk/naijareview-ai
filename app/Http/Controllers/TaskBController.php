<?php

namespace App\Http\Controllers;

use App\Ai\Agents\RecommendationAgent;
use App\Services\DatasetService;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StructuredAgentResponse;

class TaskBController extends Controller
{
    private const SESSION_KEY = 'rec_conversation';

    public function index()
    {
        session()->forget(self::SESSION_KEY);

        return view('tasks.task-b.index');
    }

    public function recommend(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'scenario'            => 'required|in:normal,cold_start,cross_domain',
            'persona_description' => 'required|string|max:1000',
            'domain'              => 'required|string|max:100',
            'location'            => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('task-b')
                ->withErrors($validator)
                ->withInput();
        }

        $scenario    = $request->input('scenario');
        $persona     = trim($request->input('persona_description'));
        $domain      = trim($request->input('domain'));
        $location    = trim($request->input('location', 'Lagos'));

        session()->forget(self::SESSION_KEY);

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
            return back()
                ->withErrors(['ai' => 'Recommendation failed: ' . $e->getMessage()])
                ->withInput();
        }

        $history = [
            ['role' => 'user',      'content' => $userPrompt],
            ['role' => 'assistant', 'content' => $response->text],
        ];

        session([self::SESSION_KEY => [
            'scenario'    => $scenario,
            'persona'     => $persona,
            'domain'      => $domain,
            'location'    => $location,
            'catalogText' => $catalogText,
            'history'     => $history,
        ]]);

        return view('tasks.task-b.index', [
            'recommendations' => $recommendations,
            'scenario'        => $scenario,
            'history'         => $this->formatHistoryForView($history),
        ]);
    }

    public function refine(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'refinement' => 'required|string|max:500',
            'scenario'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('task-b')
                ->withErrors($validator)
                ->withInput();
        }

        $session = session(self::SESSION_KEY);

        if (! $session) {
            return redirect()->route('task-b')
                ->withErrors(['ai' => 'Session expired. Please start a new recommendation request.']);
        }

        $refinement  = trim($request->input('refinement'));
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
            return back()
                ->withErrors(['ai' => 'Refinement failed: ' . $e->getMessage()])
                ->withInput();
        }

        $history[] = ['role' => 'user',      'content' => $refinement];
        $history[] = ['role' => 'assistant', 'content' => $response->text];

        session()->put(self::SESSION_KEY . '.history', $history);

        return view('tasks.task-b.index', [
            'recommendations' => $recommendations,
            'scenario'        => $session['scenario'],
            'history'         => $this->formatHistoryForView($history),
        ]);
    }

    private function formatHistoryForView(array $history): array
    {
        return array_map(fn ($entry) => [
            'role'    => $entry['role'],
            'message' => $entry['role'] === 'user'
                ? $entry['content']
                : 'Recommendations updated based on your request.',
        ], $history);
    }
}
