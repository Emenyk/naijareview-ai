<?php

namespace App\Ai\Agents;

use App\Helpers\NigerianContextFormatter;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Promptable;
use Stringable;

class RecommendationAgent implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        private readonly string $scenario,
        private readonly string $personaDescription,
        private readonly string $domain,
        private readonly string $location,
        private readonly string $catalogText,
        private readonly array $conversationHistory = [],
    ) {}

    public function instructions(): Stringable|string
    {
        $scenarioContext = match ($this->scenario) {
            'cold_start' => "COLD START SCENARIO: This user has NO prior purchase or review history. Base your recommendations purely on their described preferences, demographics, and expressed context. Lean toward popular, widely-liked choices that carry low risk for a first-time user.",
            'cross_domain' => "CROSS-DOMAIN SCENARIO: This user has history in a DIFFERENT domain/category than what they are now requesting. You must infer transferable preferences — for example, a lover of gritty crime dramas may enjoy hardboiled fiction or a thriller novel. State your cross-domain reasoning explicitly in each recommendation's reason.",
            default => "NORMAL SCENARIO: This user has an established preference profile in the requested domain. Lean into their specific preferences, not just popular items. Surface items that match their demonstrated tastes.",
        };

        $nigerianContext = NigerianContextFormatter::getRecommendationContext($this->location);

        return <<<INSTRUCTIONS
        You are an intelligent, reasoning-first recommendation agent for a Nigerian review and discovery platform.

        Your goal is to produce deeply personalised recommendations — not generic popularity rankings.

        ## User Request
        - Scenario: {$this->scenario}
        - Domain / Category: {$this->domain}
        - Location: {$this->location}
        - Persona Description: {$this->personaDescription}

        ## Available Item Catalog
        {$this->catalogText}

        ## Location and Cultural Context
        {$nigerianContext}

        ## Reasoning Protocol
        Before outputting recommendations, you must internally reason through:
        1. What does this specific user ACTUALLY want versus what they literally asked for?
        2. What would genuinely surprise and delight them — not just what is obvious?
        3. How does the scenario type (cold start / cross domain / normal) shift which items to surface?
        4. For cross-domain: what transferable signal exists from the user's stated history?
        5. If this is a refinement turn: what specifically must change from the prior list, and why?

        ## Output Requirements
        - Rank exactly 10 items selected from the catalog above.
        - The catalog above already reflects the requested domain and location constraints.
        - Do NOT recommend items not present in the catalog.
        - Every recommendation MUST include a specific, personalised `reason` that directly references this user's persona, preferences, scenario, or location.
        - Reasons must NOT be generic descriptions of the item — they must explain WHY it fits THIS user.
        - For cross-domain picks, the reason must explicitly state the cross-domain inference.

        Return ONLY structured JSON: an array of 10 `recommendations`, each with `name` (string) and `reason` (string).
        INSTRUCTIONS;
    }

    public function messages(): iterable
    {
        $messages = [];

        foreach ($this->conversationHistory as $entry) {
            if ($entry['role'] === 'user') {
                $messages[] = new UserMessage($entry['content']);
            } else {
                $messages[] = new AssistantMessage($entry['content']);
            }
        }

        return $messages;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'recommendations' => $schema->array()
                ->items(
                    $schema->object([
                        'name'   => $schema->string()->description('Item name from the catalog')->required(),
                        'reason' => $schema->string()->description('Specific, personalised reason this item fits this exact user')->required(),
                    ])->withoutAdditionalProperties()
                )
                ->min(10)
                ->max(10)
                ->description('Ranked list of exactly 10 personalised recommendations')
                ->required(),
        ];
    }
}
