<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Attribute;
use Nmc\Validation\Rule;
use Nmc\Validation\Validation;

class Present extends Rule
{
    /** @var bool */
    protected $implicit = true;

    /** @var string */
    protected $message = 'The :attribute must be present';

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->setAttributeAsRequired();

        assert($this->attribute instanceof Attribute);
        assert($this->validation instanceof Validation);

        return $this->validation->hasValue($this->attribute->getKey());
    }

    /**
     * Set attribute is required if $this->attribute is set
     *
     * @return void
     */
    protected function setAttributeAsRequired()
    {
        if ($this->attribute) {
            $this->attribute->setRequired(true);
        }
    }
}
