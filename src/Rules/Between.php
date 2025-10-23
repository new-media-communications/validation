<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Between extends Rule
{
    use Traits\SizeTrait;

    /** @var string */
    protected $message = 'The :attribute must be between :min and :max';

    /** @var list<string> */
    protected $fillableParams = ['min', 'max'];

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $min = $this->getBytesSize($this->parameter('min'));
        $max = $this->getBytesSize($this->parameter('max'));

        $valueSize = $this->getValueSize($value);

        if (! is_numeric($valueSize)) {
            return false;
        }

        return $valueSize >= $min && $valueSize <= $max;
    }
}
