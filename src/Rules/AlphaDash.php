<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class AlphaDash extends Rule
{
    /** @var string */
    protected $message = 'The :attribute only allows a-z, 0-9, _ and -';

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM\pN_-]+$/u', (string) $value) > 0;
    }
}
