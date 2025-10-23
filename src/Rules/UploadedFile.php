<?php

namespace Nmc\Validation\Rules;

use Nmc\Validation\Attribute;
use Nmc\Validation\Helper;
use Nmc\Validation\MimeTypeGuesser;
use Nmc\Validation\Rule;
use Nmc\Validation\Rules\Interfaces\BeforeValidate;
use Nmc\Validation\Validation;

class UploadedFile extends Rule implements BeforeValidate
{
    use Traits\FileTrait;
    use Traits\SizeTrait;

    /** @var string */
    protected $message = 'The :attribute is not valid uploaded file';

    /** @var string|int */
    protected $maxSize;

    /** @var string|int */
    protected $minSize;

    /** @var list<string> */
    protected $allowedTypes = [];

    /**
     * Given $params and assign $this->params
     *
     * @param  array<array-key, mixed>  $params
     */
    #[\Override]
    public function fillParameters(array $params): static
    {
        $this->minSize(array_shift($params));
        $this->maxSize(array_shift($params));
        $this->fileTypes($params);

        return $this;
    }

    /**
     * Given $size and set the max size
     *
     * @param  string|int  $size
     */
    public function maxSize($size): static
    {
        $this->params['max_size'] = $size;

        return $this;
    }

    /**
     * Given $size and set the min size
     *
     * @param  string|int  $size
     */
    public function minSize($size): static
    {
        $this->params['min_size'] = $size;

        return $this;
    }

    /**
     * Given $min and $max then set the range size
     *
     * @param  string|int  $min
     * @param  string|int  $max
     */
    public function sizeBetween($min, $max): static
    {
        $this->minSize($min);
        $this->maxSize($max);

        return $this;
    }

    /**
     * Given $types and assign $this->params
     *
     * @param  mixed  $types
     */
    public function fileTypes($types): static
    {
        if (is_string($types)) {
            $types = explode('|', $types);
        }

        $this->params['allowed_types'] = $types;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeValidate(): void
    {
        $attribute = $this->getAttribute();

        assert($attribute instanceof Attribute);

        // We only resolve uploaded file value
        // from complex attribute such as 'files.photo', 'images.*', 'images.foo.bar', etc.
        if (! $attribute->isUsingDotNotation()) {
            return;
        }

        $keys = explode('.', $attribute->getKey());
        $firstKey = array_shift($keys);

        assert($this->validation instanceof Validation);

        $firstKeyValue = $this->validation->getValue($firstKey);

        $resolvedValue = $this->resolveUploadedFileValue($firstKeyValue);

        // Return original value if $value can't be resolved as uploaded file value
        if (! $resolvedValue) {
            return;
        }

        $this->validation->setValue($firstKey, $resolvedValue);
    }

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     */
    public function check($value): bool
    {
        $minSize = $this->parameter('min_size');
        $maxSize = $this->parameter('max_size');
        $allowedTypes = $this->parameter('allowed_types');

        if ($allowedTypes) {
            $or = $this->validation ? $this->validation->getTranslation('or') : 'or';
            $this->setParameterText('allowed_types', Helper::join(Helper::wraps($allowedTypes, "'"), ', ', sprintf(', %s ', $or)));
        }

        // below is Required rule job
        if (! $this->isValueFromUploadedFiles($value) || $value['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if (! $this->isUploadedFile($value)) {
            return false;
        }

        // just make sure there is no error
        if ($value['error']) {
            return false;
        }

        if ($minSize) {
            $bytesMinSize = $this->getBytesSize($minSize);
            if ($value['size'] < $bytesMinSize) {
                $this->setMessage('The :attribute file is too small, minimum size is :min_size');

                return false;
            }
        }

        if ($maxSize) {
            $bytesMaxSize = $this->getBytesSize($maxSize);
            if ($value['size'] > $bytesMaxSize) {
                $this->setMessage('The :attribute file is too large, maximum size is :max_size');

                return false;
            }
        }

        if (! empty($allowedTypes)) {
            $guesser = new MimeTypeGuesser;
            $ext = $guesser->getExtension($value['type']);
            unset($guesser);

            if (! in_array($ext, $allowedTypes)) {
                $this->setMessage('The :attribute file type must be :allowed_types');

                return false;
            }
        }

        return true;
    }
}
