<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Helper;
use Nmc\Validation\Rule;

class NotIn extends Rule
{
    /** @var string */
    protected $message = 'The :attribute is not allowing :disallowed_values';

    /** @var bool */
    protected $strict = false;

    /**
     * Given $params and assign the $this->params
     *
     * @param  array<array-key, mixed>  $params
     */
    #[\Override]
    public function fillParameters(array $params): static
    {
        if (count($params) === 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $this->params['disallowed_values'] = $params;

        return $this;
    }

    /**
     * Set strict value
     *
     * @param  bool  $strict
     */
    public function strict($strict = true): void
    {
        $this->strict = $strict;
    }

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->requireParameters(['disallowed_values']);

        $disallowedValues = (array) $this->parameter('disallowed_values');

        $and = $this->validation ? $this->validation->getTranslation('and') : 'and';
        $disallowedValuesText = Helper::join(Helper::wraps($disallowedValues, "'"), ', ', sprintf(', %s ', $and));
        $this->setParameterText('disallowed_values', $disallowedValuesText);

        return ! in_array($value, $disallowedValues, $this->strict);
    }
}
