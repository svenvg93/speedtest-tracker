<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContainsString implements ValidationRule
{
    protected array $needles;

    public function __construct(
        string|array $needle,
        protected bool $caseSensitive = false
    ) {
        $this->needles = is_array($needle) ? $needle : [$needle];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $haystack = $this->caseSensitive ? $value : strtolower($value);

        foreach ($this->needles as $needle) {
            $needleToCheck = $this->caseSensitive ? $needle : strtolower($needle);
            if (str_contains($haystack, $needleToCheck)) {
                return; // Found at least one match, validation passes
            }
        }

        // No matches found
        if (count($this->needles) === 1) {
            $fail("The :attribute must contain '{$this->needles[0]}'.");
        } else {
            $formattedNeedles = implode("' or '", $this->needles);
            $fail("The :attribute must contain '{$formattedNeedles}'.");
        }
    }
}
