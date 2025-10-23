<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Boolean extends Rule
{
    /** @var string */
    protected $message = 'The :attribute must be a boolean';

    /**
     * Check the value is valid
     *
     * @param  mixed  $value
     *
     * @throws \Exception
     */
    public function check($value): bool
    {
        return \in_array($value, [\true, \false, 'true', 'false', 1, 0, '0', '1', 'y', 'n'], \true);
    }
}
