<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Validation;

class RequiredIf extends Required
{
    /** @var bool */
    protected $implicit = true;

    /** @var string */
    protected $message = 'The :attribute is required';

    /**
     * Given $params and assign the $this->params
     *
     * @param  array<array-key, mixed>  $params
     */
    #[\Override]
    public function fillParameters(array $params): static
    {
        $this->params['field'] = array_shift($params);
        $this->params['values'] = $params;

        return $this;
    }

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    #[\Override]
    public function check($value): bool
    {
        $this->requireParameters(['field', 'values']);

        $anotherAttribute = $this->parameter('field');
        $definedValues = $this->parameter('values');
        $anotherValue = $this->getAttribute()?->getValue($anotherAttribute);

        assert($this->validation instanceof Validation);

        $validator = $this->validation->getValidator();
        $requiredValidator = $validator('required');

        if (in_array($anotherValue, $definedValues)) {
            $this->setAttributeAsRequired();

            return $requiredValidator->check($value);
        }

        return true;
    }
}
