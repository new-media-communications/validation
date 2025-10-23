<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Numeric extends Rule
{
    /** @var string */
    protected $message = 'The :attribute must be numeric';

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        return is_numeric($value);
    }
}
