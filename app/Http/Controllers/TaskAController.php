<?php

namespace App\Http\Controllers;

use App\Ai\Agents\UserModelingAgent;
use App\Services\PersonaBuilder;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StructuredAgentResponse;

class TaskAController extends Controller
{
    public function index()
    {
        return view('tasks.task-a.index', [
            'personas' => PersonaBuilder::forSelect(),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'persona_id' => 'required|string',
            'product'    => 'required|string|max:200',
        ]);

        $persona = PersonaBuilder::find($request->input('persona_id'));

        if (! $persona) {
            return back()->withErrors(['persona_id' => 'Unknown persona selected.'])->withInput();
        }

        $product = trim($request->input('product'));

        try {
            /** @var StructuredAgentResponse $response */
            $response = (new UserModelingAgent($persona, $product))
                ->prompt("Generate a review for: {$product}");

            $result = $response->toArray();
            $result['rating'] = max(1, min(5, (int) ($result['rating'] ?? 3)));
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['ai' => 'AI generation failed: ' . $e->getMessage()])
                ->withInput();
        }

        return view('tasks.task-a.index', [
            'personas' => PersonaBuilder::forSelect(),
            'result'   => $result,
            'persona'  => $persona,
            'product'  => $product,
        ]);
    }
}
