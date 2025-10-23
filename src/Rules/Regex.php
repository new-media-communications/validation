<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Regex extends Rule
{
    /** @var string */
    protected $message = 'The :attribute is not valid format';

    /** @var list<string> */
    protected $fillableParams = ['regex'];

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        $regex = $this->parameter('regex');

        return preg_match($regex, (string) $value) > 0;
    }
}
