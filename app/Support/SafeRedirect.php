<?php

namespace App\Support;

/**
 * Only allow redirects inside this website (block http://evil.com links).
 */
class SafeRedirect
{
    public static function internal(?string $url, string $fallback = '/'): string
    {
        if (! is_string($url) || $url === '') {
            return $fallback;
        }

        // Must start with / but not // or contain ://
        $isSafe = str_starts_with($url, '/')
            && ! str_starts_with($url, '//')
            && ! str_contains($url, '://');

        return $isSafe ? $url : $fallback;
    }
}
