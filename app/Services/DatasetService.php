<?php

namespace App\Services;

class DatasetService
{
    /**
     * Get all reviews for a specific user ID
     * from our sampled reviews file
     */
    public function getUserReviews(string $userId): array
    {
        $file = storage_path('app/dataset/reviews_sample.json');
        $handle = fopen($file, 'r');
        $reviews = [];

        while (($line = fgets($handle)) !== false) {
            $data = json_decode(trim($line), true);

            if (!$data) continue;

            if ($data['user_id'] === $userId) {
                $reviews[] = [
                    'stars'    => $data['stars'],
                    'text'     => $data['text'],
                    'date'     => $data['date'],
                    'business_id' => $data['business_id'],
                ];
            }
        }

        fclose($handle);
        return $reviews;
    }

    /**
     * Get a list of sample users who have
     * enough reviews to build a good persona
     */
    public function getSampleUsers(int $minReviews = 5): array
    {
        $file = storage_path('app/dataset/reviews_sample.json');
        $handle = fopen($file, 'r');
        $userCounts = [];

        // Count how many reviews each user has
        while (($line = fgets($handle)) !== false) {
            $data = json_decode(trim($line), true);
            if (!$data) continue;

            $uid = $data['user_id'];
            if (!isset($userCounts[$uid])) {
                $userCounts[$uid] = [
                    'user_id'      => $uid,
                    'review_count' => 0,
                    'total_stars'  => 0,
                ];
            }
            $userCounts[$uid]['review_count']++;
            $userCounts[$uid]['total_stars'] += $data['stars'];
        }

        fclose($handle);

        // Filter users with enough reviews
        $qualified = array_filter($userCounts, function ($user) use ($minReviews) {
            return $user['review_count'] >= $minReviews;
        });

        // Calculate average rating for each
        $result = array_map(function ($user) {
            $avg = round($user['total_stars'] / $user['review_count'], 1);
            $user['avg_rating'] = $avg;
            $user['personality'] = match(true) {
                $avg <= 2.0 => 'Highly critical writer',
                $avg <= 2.9 => 'Critical writer',
                $avg <= 3.5 => 'Balanced reviewer',
                $avg <= 4.2 => 'Generally positive',
                default     => 'Enthusiastic praiser',
            };
            return $user;
        }, $qualified);

        // Return top 10 sorted by review count
        usort($result, fn($a, $b) => $b['review_count'] - $a['review_count']);

        return array_slice(array_values($result), 0, 10);
    }

    /**
     * Get business name by business ID
     */
    public function getBusinessName(string $businessId): string
    {
        $file = storage_path('app/dataset/yelp_academic_dataset_business.json');
        $handle = fopen($file, 'r');

        while (($line = fgets($handle)) !== false) {
            $data = json_decode(trim($line), true);
            if (!$data) continue;

            if ($data['business_id'] === $businessId) {
                fclose($handle);
                return $data['name'];
            }
        }

        fclose($handle);
        return 'Unknown Business';
    }
}