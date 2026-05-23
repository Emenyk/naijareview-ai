<?php

namespace App\Services;

class PersonaBuilder
{
    private static ?array $cachedPersonas = null;

    /**
     * Build personas from real Yelp review data
     */
    public static function all(): array
    {
        if (self::$cachedPersonas !== null) {
            return self::$cachedPersonas;
        }

        $file = storage_path('app/dataset/reviews_sample.json');
        $handle = fopen($file, 'r');
        $userMap = [];

        while (($line = fgets($handle)) !== false) {
            $data = json_decode(trim($line), true);
            if (!$data) continue;

            $uid = $data['user_id'];

            if (!isset($userMap[$uid])) {
                $userMap[$uid] = [
                    'user_id'     => $uid,
                    'reviews'     => [],
                    'total_stars' => 0,
                ];
            }

            $userMap[$uid]['reviews'][] = [
                'rating' => (int) $data['stars'],
                'text'   => $data['text'],
            ];
            $userMap[$uid]['total_stars'] += $data['stars'];
        }

        fclose($handle);

        // Keep only users with 5+ reviews
        $qualified = array_filter(
            $userMap,
            fn($u) => count($u['reviews']) >= 5
        );

        // Sort by review count descending
        usort($qualified, fn($a, $b) =>
            count($b['reviews']) - count($a['reviews'])
        );

        // Build final persona objects — top 8
        $personas = [];
        foreach (array_slice($qualified, 0, 8) as $user) {
            $personas[] = self::buildPersona($user);
        }

        self::$cachedPersonas = $personas;
        return $personas;
    }

    /**
     * Find a persona by ID
     */
    public static function find(string $id): ?array
    {
        foreach (self::all() as $persona) {
            if ($persona['id'] === $id) {
                return $persona;
            }
        }
        return null;
    }

    /**
     * Return simplified list for dropdown
     */
    public static function forSelect(): array
    {
        return array_map(fn($p) => [
            'id'    => $p['id'],
            'label' => "{$p['tone']} · Avg {$p['avg_rating']}★ · {$p['review_count']} reviews",
        ], self::all());
    }

    /**
     * Build a single persona from raw user data
     */
    private static function buildPersona(array $user): array
    {
        $reviews      = $user['reviews'];
        $count        = count($reviews);
        $avgRating    = round($user['total_stars'] / $count, 1);
        $style        = self::detectStyle($reviews);
        $tone         = self::detectTone($avgRating);
        $themes       = self::detectThemes($reviews);

        return [
            'id'           => $user['user_id'],
            'name'         => self::generateName($avgRating, $style),
            'avg_rating'   => $avgRating,
            'review_count' => $count,
            'style'        => $style,
            'tone'         => $tone,
            'themes'       => implode(', ', $themes),
            'reviews'      => array_slice($reviews, 0, 6),
            'samples'      => array_slice($reviews, 0, 3),
            'summary'      => self::buildSummary(
                $avgRating, $style, $tone, $themes
            ),
        ];
    }

    private static function detectStyle(array $reviews): string
    {
        $lengths = array_map(
            fn($r) => str_word_count($r['text']),
            $reviews
        );
        $avg = array_sum($lengths) / count($lengths);

        if ($avg < 30)  return 'Brief and direct';
        if ($avg < 80)  return 'Moderate detail';
        if ($avg < 150) return 'Detailed writer';
        return 'Storyteller — very detailed';
    }

    private static function detectTone(float $avg): string
    {
        if ($avg <= 2.0) return 'Highly critical';
        if ($avg <= 2.9) return 'Critical writer';
        if ($avg <= 3.5) return 'Balanced reviewer';
        if ($avg <= 4.2) return 'Generally positive';
        return 'Enthusiastic praiser';
    }

private static function detectThemes(array $reviews): array
{
    $text = strtolower(
        implode(' ', array_column($reviews, 'text'))
    );

    $themes = [];
    $map = [
        'service'     => ['service','staff','team','employee','rude','friendly','helpful','attentive','manager'],
        'quality'     => ['quality','good','bad','excellent','terrible','poor','great','awful','amazing','horrible'],
        'price'       => ['price','expensive','cheap','worth','overpriced','value','affordable','cost','money'],
        'cleanliness' => ['clean','dirty','neat','messy','hygiene','tidy','filthy','spotless'],
        'experience'  => ['experience','visit','overall','recommend','return','again','disappointed','impressed'],
        'wait time'   => ['wait','slow','fast','quick','minutes','hour','delay','prompt','rushed'],
        'location'    => ['location','area','parking','access','close','far','convenient','neighborhood'],
        'atmosphere'  => ['atmosphere','ambience','vibe','noise','loud','quiet','cozy','comfortable','crowded'],
    ];

    foreach ($map as $theme => $keywords) {
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) {
                $themes[] = $theme;
                break;
            }
        }
    }

    return array_unique($themes);
}

    private static function generateName(
        float $avg, string $style
    ): string {
        $toneWord = match(true) {
            $avg <= 2.0 => 'Harsh Critic',
            $avg <= 2.9 => 'Critical Reviewer',
            $avg <= 3.5 => 'Balanced Reviewer',
            $avg <= 4.2 => 'Positive Reviewer',
            default     => 'Enthusiastic Fan',
        };
        return "{$toneWord} ({$style})";
    }

    private static function buildSummary(
        float $avg,
        string $style,
        string $tone,
        array $themes
    ): string {
        $themeList = implode(', ', $themes);
        return "This user is a {$tone} with an average rating of " .
               "{$avg} stars. Writing style: {$style}. " .
               "Frequently mentions: {$themeList}. " .
               "Mirror their vocabulary, sentence length, and " .
               "rating tendency precisely.";
    }
}