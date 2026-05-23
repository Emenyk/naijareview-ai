<?php

namespace App\Services;

class DatasetService
{
    /**
     * Get user reviews from real Yelp sample file
     */
    public static function getUserReviews(string $userId): array
    {
        $file   = storage_path('app/dataset/reviews_sample.json');
        $handle = fopen($file, 'r');
        $reviews = [];

        while (($line = fgets($handle)) !== false) {
            $data = json_decode(trim($line), true);
            if (!$data) continue;
            if ($data['user_id'] === $userId) {
                $reviews[] = [
                    'rating' => (int) $data['stars'],
                    'text'   => $data['text'],
                ];
            }
        }

        fclose($handle);
        return $reviews;
    }

    /**
     * Get businesses from real Yelp dataset
     * filtered by category and location
     */
    public static function forRecommendation(
        string $domain,
        string $location,
        string $scenario,
        int    $limit = 30
    ): array {
        $file     = storage_path('app/dataset/yelp_academic_dataset_business.json');
        $handle   = fopen($file, 'r');
        $category = self::resolveCategory($domain);
        $location = strtolower(trim($location));
        $pool     = [];

        while (($line = fgets($handle)) !== false) {
            if (count($pool) >= $limit * 3) break;

            $data = json_decode(trim($line), true);
            if (!$data) continue;

            // Match category
            $cats = strtolower($data['categories'] ?? '');
            if (!str_contains($cats, strtolower($category))) continue;

            // For cross_domain skip location filter
            if ($scenario !== 'cross_domain' && !empty($location)) {
                $city = strtolower($data['city'] ?? '');
                $state = strtolower($data['state'] ?? '');
                if (
                    !str_contains($city, $location) &&
                    !str_contains($location, $city) &&
                    !str_contains($state, $location)
                ) continue;
            }

            $pool[] = [
                'business_id'  => $data['business_id'],
                'name'         => $data['name'],
                'category'     => $data['categories'],
                'rating'       => (float) ($data['stars'] ?? 0),
                'review_count' => (int) ($data['review_count'] ?? 0),
                'city'         => $data['city'] ?? '',
                'state'        => $data['state'] ?? '',
            ];
        }

        fclose($handle);

        // If no results with location, retry without location
        if (empty($pool) && $scenario !== 'cross_domain') {
            return self::forRecommendation(
                $domain, '', $scenario, $limit
            );
        }

        // Sort by rating for cold_start, mixed for others
        if ($scenario === 'cold_start') {
            usort($pool, fn($a, $b) =>
                $b['rating'] <=> $a['rating']
            );
        } else {
            shuffle($pool);
        }

        return array_slice($pool, 0, $limit);
    }

    /**
     * Format businesses as readable text for LLM prompt
     */
    public static function formatCatalogForPrompt(
        array $businesses
    ): string {
        $lines = '';
        foreach ($businesses as $biz) {
            $lines .= "- {$biz['name']} | " .
                      "Category: {$biz['category']} | " .
                      "Rating: {$biz['rating']}★ | " .
                      "Reviews: {$biz['review_count']} | " .
                      "Location: {$biz['city']}, {$biz['state']}\n";
        }
        return $lines;
    }

    /**
     * Resolve user typed domain to Yelp category keyword
     */
    public static function resolveCategory(string $domain): string
    {
        $domain = strtolower(trim($domain));

        $map = [
            'restaurant'    => 'Restaurants',
            'food'          => 'Restaurants',
            'dining'        => 'Restaurants',
            'eat'           => 'Restaurants',
            'pizza'         => 'Pizza',
            'burger'        => 'Burgers',
            'fast food'     => 'Fast Food',
            'cafe'          => 'Cafes',
            'coffee'        => 'Coffee',
            'bar'           => 'Bars',
            'nightlife'     => 'Nightlife',
            'hotel'         => 'Hotels',
            'salon'         => 'Hair Salons',
            'hair'          => 'Hair Salons',
            'beauty'        => 'Beauty & Spas',
            'spa'           => 'Beauty & Spas',
            'gym'           => 'Fitness',
            'fitness'       => 'Fitness',
            'hospital'      => 'Hospitals',
            'health'        => 'Health & Medical',
            'shop'          => 'Shopping',
            'shopping'      => 'Shopping',
            'supermarket'   => 'Grocery',
            'grocery'       => 'Grocery',
            'pharmacy'      => 'Pharmacy',
            'bank'          => 'Banks',
            'school'        => 'Education',
        ];

        foreach ($map as $keyword => $category) {
            if (str_contains($domain, $keyword)) {
                return $category;
            }
        }

        // Return the domain itself as the search term
        return ucfirst($domain);
    }

    /**
     * Resolve with feedback for controller use
     */
    public static function resolveCategoryWithFeedback(
        string $domain
    ): array {
        $resolved = self::resolveCategory($domain);
        $wasMapped = strtolower($resolved) !== strtolower($domain);
        return [$resolved, $wasMapped];
    }

    /**
     * Get supported categories list
     */
    public static function getSupportedCategories(): array
    {
        return [
            'Restaurants', 'Fast Food', 'Pizza', 'Burgers',
            'Cafes', 'Coffee', 'Bars', 'Nightlife',
            'Hotels', 'Hair Salons', 'Beauty & Spas',
            'Fitness', 'Health & Medical', 'Shopping',
            'Grocery', 'Pharmacy', 'Banks', 'Education',
        ];
    }
}