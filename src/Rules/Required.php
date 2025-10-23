<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Rule;

class Required extends Rule
{
    use Traits\FileTrait;

    /** @var bool */
    protected $implicit = true;

    /** @var string */
    protected $message = 'The :attribute is required';

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $this->setAttributeAsRequired();

        if ($this->attribute && $this->attribute->hasRule('uploaded_file')) {
            return $this->isValueFromUploadedFiles($value) && $value['error'] != UPLOAD_ERR_NO_FILE;
        }

        if (is_string($value)) {
            return mb_strlen(trim($value), 'UTF-8') > 0;
        }

        if (is_array($value)) {
            return $value !== [];
        }

        return ! is_null($value);
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
