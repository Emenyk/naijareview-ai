<?php

namespace App\Services;

class DatasetService
{
    private static ?array $businesses = null;

    private const CROSS_DOMAIN_MAP = [
        'restaurant'    => ['books', 'entertainment'],
        'books'         => ['entertainment', 'restaurant'],
        'entertainment' => ['books', 'restaurant'],
        'electronics'   => ['books', 'entertainment'],
        'hospitality'   => ['restaurant', 'entertainment'],
        'health'        => ['restaurant', 'entertainment'],
    ];

    public static function all(): array
    {
        if (self::$businesses !== null) {
            return self::$businesses;
        }

        $path = storage_path('app/data/businesses.json');
        self::$businesses = json_decode(file_get_contents($path), true);

        return self::$businesses;
    }

    /**
     * Get businesses filtered and ranked for a recommendation request.
     *
     * For cold_start: return popular items across all categories.
     * For cross_domain: include items from related categories.
     * For normal: filter by requested domain and location.
     */
    public static function forRecommendation(
        string $domain,
        string $location,
        string $scenario,
        int $limit = 30
    ): array {
        $all = self::all();

        $domain = strtolower(trim($domain));
        $location = strtolower(trim($location));

        if ($scenario === 'cold_start') {
            $pool = self::filterByLocation($all, $location);
            usort($pool, fn ($a, $b) => $b['avg_rating'] <=> $a['avg_rating']);

            return array_slice($pool, 0, $limit);
        }

        $primaryCategory = self::resolvePrimaryCategory($domain);

        if ($scenario === 'cross_domain') {
            $relatedCategories = self::CROSS_DOMAIN_MAP[$primaryCategory] ?? [];
            $allCategories = array_merge([$primaryCategory], $relatedCategories);
            $pool = array_filter($all, fn ($b) => in_array($b['category'], $allCategories));
        } else {
            $pool = array_filter($all, fn ($b) => $b['category'] === $primaryCategory);
        }

        $pool = self::filterByLocation(array_values($pool), $location);

        if (count($pool) < 10) {
            $remaining = array_filter($all, fn ($b) => !in_array($b, $pool));
            $pool = array_merge($pool, array_slice(array_values($remaining), 0, $limit - count($pool)));
        }

        usort($pool, fn ($a, $b) => $b['avg_rating'] <=> $a['avg_rating']);

        return array_slice($pool, 0, $limit);
    }

    private static function filterByLocation(array $businesses, string $location): array
    {
        if (empty($location) || $location === 'n/a') {
            return $businesses;
        }

        $local = array_filter($businesses, fn ($b) => str_contains(strtolower($b['location']), $location) || $b['location'] === 'N/A');
        $local = array_values($local);

        return count($local) >= 5 ? $local : $businesses;
    }

    private static function resolvePrimaryCategory(string $domain): string
    {
        $map = [
            'restaurant' => 'restaurant', 'food' => 'restaurant', 'dining' => 'restaurant',
            'eat' => 'restaurant', 'eating' => 'restaurant', 'cuisine' => 'restaurant',
            'book' => 'books', 'books' => 'books', 'novel' => 'books', 'reading' => 'books',
            'literature' => 'books', 'fiction' => 'books',
            'movie' => 'entertainment', 'movies' => 'entertainment', 'film' => 'entertainment',
            'films' => 'entertainment', 'cinema' => 'entertainment', 'nollywood' => 'entertainment',
            'entertainment' => 'entertainment', 'shows' => 'entertainment',
            'electronics' => 'electronics', 'gadget' => 'electronics', 'gadgets' => 'electronics',
            'tech' => 'electronics', 'technology' => 'electronics', 'phone' => 'electronics',
            'hotel' => 'hospitality', 'hotels' => 'hospitality', 'stay' => 'hospitality',
            'accommodation' => 'hospitality', 'hospitality' => 'hospitality',
            'gym' => 'health', 'spa' => 'health', 'wellness' => 'health', 'health' => 'health',
        ];

        foreach ($map as $keyword => $category) {
            if (str_contains($domain, $keyword)) {
                return $category;
            }
        }

        return 'restaurant';
    }

    public static function formatCatalogForPrompt(array $businesses): string
    {
        $lines = '';
        foreach ($businesses as $biz) {
            $tags = implode(', ', array_slice($biz['tags'], 0, 5));
            $lines .= "\n- [{$biz['name']}] ({$biz['category']}, {$biz['location']}, {$biz['avg_rating']}★, {$biz['price_range']} price) — {$biz['description']} [Tags: {$tags}]";
        }

        return $lines;
    }
}
