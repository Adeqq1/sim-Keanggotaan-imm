<?php

namespace App\Support;

use Illuminate\Http\Request;

final class SortParams
{
    /** @param array<string> $keys */
    public static function resolve(Request $request, array $keys, string $default, string $defaultDirection = 'desc'): array
    {
        $sort = $request->input('sort');
        $direction = $request->input('direction');

        return [
            'key' => is_string($sort) && in_array($sort, $keys, true) ? $sort : $default,
            'direction' => is_string($direction) && in_array(strtolower($direction), ['asc', 'desc'], true)
                ? strtolower($direction) : $defaultDirection,
        ];
    }
}
