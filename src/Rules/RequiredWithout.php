<?php

namespace Rakit\Validation\Rules;

class RequiredWithout extends Required
{
    /** @var bool */
    protected $implicit = true;

    /** @var string */
    protected $message = 'The :attribute is required';

    /**
     * Given $params and assign $this->params
     */
    #[\Override]
    public function fillParameters(array $params): static
    {
        $this->params['fields'] = $params;

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
        $this->requireParameters(['fields']);
        $fields = $this->parameter('fields');
        $validator = $this->validation->getValidator();
        $requiredValidator = $validator('required');

        foreach ($fields as $field) {
            if (! $this->validation->hasValue($field)) {
                $this->setAttributeAsRequired();

                return $requiredValidator->check($value);
            }
        }

        return true;
    }
}
