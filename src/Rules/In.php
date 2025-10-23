<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Helper;
use Rakit\Validation\Rule;

class In extends Rule
{
    /** @var string */
    protected $message = 'The :attribute only allows :allowed_values';

    /** @var bool */
    protected $strict = false;

    /**
     * Given $params and assign the $this->params
     */
    #[\Override]
    public function fillParameters(array $params): static
    {
        if (count($params) === 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $this->params['allowed_values'] = $params;

        return $this;
    }

    /**
     * Set strict value
     */
    public function strict(bool $strict = true): void
    {
        $this->strict = $strict;
    }

    /**
     * Check $value is existed
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->requireParameters(['allowed_values']);

        $allowedValues = $this->parameter('allowed_values');

        $or = $this->validation ? $this->validation->getTranslation('or') : 'or';
        $allowedValuesText = Helper::join(Helper::wraps($allowedValues, "'"), ', ', sprintf(', %s ', $or));
        $this->setParameterText('allowed_values', $allowedValuesText);

        return in_array($value, $allowedValues, $this->strict);
    }
}
