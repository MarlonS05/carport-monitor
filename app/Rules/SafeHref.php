<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class SafeHref implements ValidationRule
{
    /** @var list<string> */
    private const DANGEROUS_SCHEMES = [
        'javascript:',
        'data:',
        'vbscript:',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        $normalized = strtolower(ltrim($value));

        foreach (self::DANGEROUS_SCHEMES as $scheme) {
            if (str_starts_with($normalized, $scheme)) {
                $fail('The :attribute contains an unsafe URL scheme.');

                return;
            }
        }
    }
}
