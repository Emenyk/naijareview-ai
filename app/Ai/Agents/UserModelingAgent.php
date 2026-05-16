<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

class UserModelingAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    private string $personaSummary;
    private array  $sampleReviews;
    private string $product;

    public function __construct(
        string $personaSummary,
        array  $sampleReviews,
        string $product
    ) {
        $this->personaSummary = $personaSummary;
        $this->sampleReviews  = $sampleReviews;
        $this->product        = $product;
    }

    /**
     * System instructions for Mistral
     */
    public function instructions(): string
    {
        $samples = $this->formatSamples();

        return <<<PROMPT
            You are a user behavior simulation engine.

            Your ONLY job is to study this specific user's writing 
            pattern and produce a review that sounds exactly like 
            THEY wrote it — not like anyone else.

            USER PROFILE:
            {$this->personaSummary}

            STUDY THESE REAL REVIEWS FROM THIS USER CAREFULLY:
            {$samples}

            From those reviews, identify:
            - Words or phrases they repeat often
            - Their sentence length and rhythm
            - How they open and close reviews
            - What they complain about vs what they praise
            - How emotional or neutral their tone is
            - Whether they use humor, sarcasm, or are straight

            PRODUCT TO REVIEW:
            "{$this->product}"

            NOW WRITE:
            A review of "{$this->product}" that sounds EXACTLY like 
            this user wrote it. Mirror their vocabulary, their rhythm, 
            their personality. If they swear, reflect that. If they 
            are formal, be formal. If they are brief, be brief.
            If they are sarcastic, be sarcastic.

            The reader should feel like the same person who wrote 
            those sample reviews above also wrote this one.

            NIGERIAN CONTEXT:
            Only apply this if the user's writing style naturally 
            supports it. Do not force it. If the user writes like 
            a formal American, keep it that way.

            Return valid JSON only. No explanation outside the JSON.
        PROMPT;
    }

    /**
     * Structured output schema — what Mistral must return
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'rating' => $schema->integer()
                ->min(1)
                ->max(5)
                ->required(),
            'review' => $schema->string()
                ->required(),
            'confidence' => $schema->string()
                ->enum(['low', 'medium', 'high'])
                ->required(),
        ];
    }

    /**
     * Format sample reviews into readable text for the prompt
     */
    private function formatSamples(): string
    {
        if (empty($this->sampleReviews)) {
            return 'No sample reviews available.';
        }

        $formatted = '';
        foreach ($this->sampleReviews as $index => $review) {
            $num = $index + 1;
            $formatted .= "Review {$num} ({$review['stars']} stars):\n";
            $formatted .= "\"{$review['text']}\"\n\n";
        }

        return trim($formatted);
    }
}