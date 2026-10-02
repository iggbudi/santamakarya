<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleMapsUrl implements ValidationRule
{
    public function __construct(private bool $embed = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $parts = is_string($value) ? parse_url($value) : false;
        $safe = $parts && filter_var($value, FILTER_VALIDATE_URL) && ($parts['scheme'] ?? null) === 'https'
         && ! isset($parts['user']) && ! isset($parts['pass']) && (! isset($parts['port']) || $parts['port'] === 443)
         && ! preg_match('/[\x00-\x20\x7f\x27\x22\\\\]/', $value);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '/';
        if ($this->embed) {
            $safe = $safe && $host === 'www.google.com' && preg_match('~^/maps/embed(?:/|$)~', $path);
        } else {
            $safe = $safe && ($host === 'maps.google.com' || $host === 'maps.app.goo.gl' || ($host === 'goo.gl' && str_starts_with($path, '/maps/')) || (in_array($host, ['www.google.com', 'google.com'], true) && preg_match('~^/maps(?:/|$)~', $path)));
        }
        if (! $safe) {
            $fail($this->embed ? 'Masukkan URL HTTPS embed Google Maps (www.google.com/maps/embed), bukan HTML iframe.' : 'Masukkan tautan HTTPS Google Maps yang valid.');
        }
    }
}
