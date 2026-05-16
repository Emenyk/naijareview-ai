<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PersonaBuilder
{
    private static ?array $personas = null;

    public static function all(): array
    {
        if (self::$personas !== null) {
            return self::$personas;
        }

        $path = storage_path('app/data/personas.json');
        self::$personas = json_decode(file_get_contents($path), true);

        return self::$personas;
    }

    public static function find(string $id): ?array
    {
        foreach (self::all() as $persona) {
            if ($persona['id'] === $id) {
                return $persona;
            }
        }

        return null;
    }

    public static function forSelect(): array
    {
        return array_map(fn ($p) => [
            'id'    => $p['id'],
            'label' => "{$p['name']} — {$p['style']} · Avg {$p['avg_rating']}★ · {$p['review_count']} reviews",
        ], self::all());
    }
}
