<?php

namespace App\Services;

class PersonaBuilder
{
    /**
     * Build a complete user persona from their reviews
     */
    public function build(array $reviews): array
    {
        if (empty($reviews)) {
            return $this->emptyPersona();
        }

        $avgRating    = $this->calculateAvgRating($reviews);
        $style        = $this->detectWritingStyle($reviews);
        $personality  = $this->detectPersonality($avgRating);
        $samples      = $this->getSampleReviews($reviews);
        $commonThemes = $this->detectCommonThemes($reviews);

        return [
            'avg_rating'    => $avgRating,
            'review_count'  => count($reviews),
            'style'         => $style,
            'personality'   => $personality,
            'common_themes' => $commonThemes,
            'samples'       => $samples,
            'summary'       => $this->buildSummary(
                $avgRating, $style, $personality, $commonThemes
            ),
        ];
    }

    /**
     * Calculate average star rating
     */
    private function calculateAvgRating(array $reviews): float
    {
        $total = array_sum(array_column($reviews, 'stars'));
        return round($total / count($reviews), 1);
    }

    /**
     * Detect writing style based on review length
     */
    private function detectWritingStyle(array $reviews): string
    {
        $lengths = array_map(
            fn($r) => str_word_count($r['text']),
            $reviews
        );
        $avgLength = array_sum($lengths) / count($lengths);

        if ($avgLength < 30)  return 'Brief and direct';
        if ($avgLength < 80)  return 'Moderate detail';
        if ($avgLength < 150) return 'Detailed writer';
        return 'Storyteller — very detailed';
    }

    /**
     * Detect personality type from average rating
     */
    private function detectPersonality(float $avgRating): string
    {
        if ($avgRating <= 2.0) return 'Highly critical';
        if ($avgRating <= 2.9) return 'Critical writer';
        if ($avgRating <= 3.5) return 'Balanced reviewer';
        if ($avgRating <= 4.2) return 'Generally positive';
        return 'Enthusiastic praiser';
    }

    /**
     * Detect common themes across reviews
     */
    private function detectCommonThemes(array $reviews): array
    {
        $allText = strtolower(
            implode(' ', array_column($reviews, 'text'))
        );

        $themes = [];

        $themeKeywords = [
            'service'     => ['service', 'staff', 'waiter', 'rude', 'friendly', 'slow'],
            'food quality'=> ['food', 'taste', 'delicious', 'bland', 'fresh', 'cold'],
            'price'       => ['price', 'expensive', 'cheap', 'worth', 'overpriced', 'value'],
            'ambience'    => ['atmosphere', 'ambience', 'noise', 'clean', 'dirty', 'cozy'],
            'wait time'   => ['wait', 'long', 'quick', 'fast', 'minutes', 'hour'],
        ];

        foreach ($themeKeywords as $theme => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($allText, $keyword)) {
                    $themes[] = $theme;
                    break;
                }
            }
        }

        return array_unique($themes);
    }

    /**
     * Get up to 3 sample reviews (shortened)
     */
    private function getSampleReviews(array $reviews): array
    {
        return array_map(
            fn($r) => [
                'stars' => $r['stars'],
                'text'  => $r['text'],            
            ],
            array_slice($reviews, 0, 3)
        );
    }

    /**
     * Build a plain English summary of the persona
     * This is what gets sent to Mistral as context
     */
    private function buildSummary(
        float $avgRating,
        string $style,
        string $personality,
        array $themes
    ): string {
        $themeList = implode(', ', $themes);

        return "This user is a {$personality} with an average rating " .
               "of {$avgRating} stars. Their writing style is: {$style}. " .
               "They frequently mention: {$themeList}. " .
               "When simulating this user, match their tone, vocabulary, " .
               "and rating tendency precisely.";
    }

    /**
     * Return empty persona when no reviews found
     */
    private function emptyPersona(): array
    {
        return [
            'avg_rating'    => 0,
            'review_count'  => 0,
            'style'         => 'Unknown',
            'personality'   => 'Unknown',
            'common_themes' => [],
            'samples'       => [],
            'summary'       => 'No review history found for this user.',
        ];
    }
}