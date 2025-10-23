<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Date extends Rule
{
    /** @var string */
    protected $message = 'The :attribute is not valid date format';

    /** @var list<string> */
    protected $fillableParams = ['format'];

    /** @var array<string, mixed> */
    protected $params = [
        'format' => 'Y-m-d',
    ];

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $format = $this->parameter('format');

        return date_create_from_format($format, $value) !== false;
    }
}
