<?php

namespace App\Helpers;

class NigerianContextFormatter
{
    public static function getWritingHints(): string
    {
        return <<<HINT
Nigerian Context: This review platform covers Nigerian businesses and products.
Where authentic to the persona's voice, you may naturally incorporate Nigerian
context such as: references to familiar Lagos/Abuja/PH landmarks, local food names
(jollof, egusi, suya, puff puff, etc.), Nigerian expressions used naturally (e.g.
"e no make sense", "the vibes were right"), and culturally grounded observations
about service, pricing and expectations in the Nigerian market.
Do NOT force this — only use it if it fits the persona's style naturally.
HINT;
    }

    public static function getRecommendationContext(string $location): string
    {
        $locationContext = match (strtolower(trim($location))) {
            'lagos' => "The user is in Lagos — Nigeria's commercial capital. Factor in Lagos-specific considerations: traffic and proximity matter enormously (VI vs. Surulere is a significant difference), the pace is fast and expectations are urban. Lagos users value convenience, quality and the right atmosphere.",
            'abuja' => "The user is in Abuja — Nigeria's calmer, more planned capital. Abuja users value quality and ambiance. Distances are manageable. A more formal, government-adjacent culture. Slightly more international in taste.",
            'port harcourt', 'ph' => "The user is in Port Harcourt — Nigeria's oil city. Seafood is central to the food culture. The nightlife scene is vibrant. A strong Rivers State cultural identity. Users here appreciate local authenticity.",
            'enugu' => "The user is in Enugu — the Igbo heartland. Authentic Igbo cuisine and culture are highly valued. A more relaxed pace than Lagos. Strong literary and academic culture.",
            default => "The user is in Nigeria. Consider Nigerian cultural context, pricing expectations relative to the Nigerian market, and locally relevant recommendations.",
        };

        return $locationContext . "\n\nAlways prioritise recommendations that are genuinely available and relevant in Nigeria.";
    }

    public static function formatPersonaForPrompt(array $persona): string
    {
        $reviewLines = '';
        foreach (array_slice($persona['reviews'], 0, 4) as $i => $review) {
            $reviewLines .= "\n  [" . ($i + 1) . "] ({$review['rating']}★): \"{$review['text']}\"";
        }

        return <<<PERSONA
        User: {$persona['name']}
        Average Rating: {$persona['avg_rating']}/5 across {$persona['review_count']} reviews
        Writing Style: {$persona['style']}
        Tone: {$persona['tone']}
        Key Themes: {$persona['themes']}
        Sample Reviews:{$reviewLines}
        PERSONA;
    }
}
