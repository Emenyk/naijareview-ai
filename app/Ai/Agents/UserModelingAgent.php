<?php

namespace App\Ai\Agents;

use App\Helpers\NigerianContextFormatter;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class UserModelingAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        private readonly array $persona,
        private readonly string $product,
    ) {}

    public function instructions(): Stringable|string
    {
        $sampleReviews = '';
        foreach (array_slice($this->persona['reviews'], 0, 6) as $i => $r) {
            $sampleReviews .= "\n  Review " . ($i + 1) . " ({$r['rating']}★): \"{$r['text']}\"";
        }

        $nigerianHints = NigerianContextFormatter::getWritingHints();

        return <<<INSTRUCTIONS
        You are a behavioural user simulation agent. Your task is to generate a realistic Yelp-style review for a product or business, written EXACTLY in the voice, tone, and writing style of the persona described below.

        ## Persona Profile
        - Name: {$this->persona['name']}
        - Average Star Rating: {$this->persona['avg_rating']} / 5
        - Total Reviews Written: {$this->persona['review_count']}
        - Writing Style: {$this->persona['style']}
        - Tone: {$this->persona['tone']}
        - Common Themes They Focus On: {$this->persona['themes']}

        ## Sample Reviews Written By This Person
        {$sampleReviews}

        ## Your Instructions
        1. Carefully study the sample reviews above — their sentence length, vocabulary, punctuation, emotional register, and structure.
        2. Generate a NEW review for the following product or business: "{$this->product}"
        3. The review MUST feel like it was genuinely written by this exact person — not a generic AI review.
        4. The star rating MUST be consistent with this user's average ({$this->persona['avg_rating']}★) within ±1 star.
        5. {$nigerianHints}
        6. Write in first person. Do NOT include the persona's name in the review text.
        7. Do NOT add AI disclaimers or mention that this content is generated.
        8. The review must be specific to the product described — not a template review with blanks filled in.

        Return ONLY the structured JSON: a `rating` integer (1–5) and a `review` string.
        INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'rating' => $schema->integer()
                ->description("Star rating 1–5, consistent with this user's behavioral pattern (avg {$this->persona['avg_rating']}★ ±1)")
                ->required(),
            'review' => $schema->string()
                ->description("Full review text written authentically in this user's exact voice, tone and style")
                ->required(),
        ];
    }
}
