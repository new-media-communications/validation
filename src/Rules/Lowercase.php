<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Lowercase extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be lowercase";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     */
    public function check($value): bool
    {
        return mb_strtolower((string) $value, mb_detect_encoding((string) $value)) === $value;
    }
}
