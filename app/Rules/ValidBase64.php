<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidBase64 implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid base64 string.');

            return;
        }

        if (base64_decode($value, true) === false) {
            $fail('The :attribute must be a valid base64 string.');
        }
    }
}
