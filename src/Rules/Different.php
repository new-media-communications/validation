<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Different extends Rule
{
    /** @var string */
    protected $message = 'The :attribute must be different with :field';

    /** @var list<string> */
    protected $fillableParams = ['field'];

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $field = $this->parameter('field');
        $anotherValue = $this->validation?->getValue($field);

        return $value != $anotherValue;
    }
}
