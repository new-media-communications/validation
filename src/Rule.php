<?php

namespace Rakit\Validation;

use Rakit\Validation\MissingRequiredParameterException;

abstract class Rule
{
    /** @var string */
    protected $key;

    /** @var \Rakit\Validation\Attribute|null */
    protected $attribute;

    /** @var \Rakit\Validation\Validation|null */
    protected $validation;

    /** @var bool */
    protected $implicit = false;

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $paramsTexts = [];

    /** @var array */
    protected $fillableParams = [];

    /** @var string */
    protected $message = "The :attribute is invalid";

    abstract public function check($value): bool;

    /**
     * Set Validation class instance
     */
    public function setValidation(Validation $validation): void
    {
        $this->validation = $validation;
    }

    /**
     * Set key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key ?: static::class;
    }

    /**
     * Set attribute
     */
    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * Get attribute
     *
     * @return \Rakit\Validation\Attribute|null
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Get parameters
     */
    public function getParameters(): array
    {
        return $this->params;
    }

    /**
     * Set params
     */
    public function setParameters(array $params): Rule
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Set parameters
     *
     * @param mixed $value
     */
    public function setParameter(string $key, $value): Rule
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Fill $params to $this->params
     */
    public function fillParameters(array $params): Rule
    {
        foreach ($this->fillableParams as $key) {
            if ($params === []) {
                break;
            }

            $this->params[$key] = array_shift($params);
        }

        return $this;
    }

    /**
     * Get parameter from given $key, return null if it not exists
     *
     * @return mixed
     */
    public function parameter(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Set parameter text that can be displayed in error message using ':param_key'
     */
    public function setParameterText(string $key, string $text): void
    {
        $this->paramsTexts[$key] = $text;
    }

    /**
     * Get $paramsTexts
     */
    public function getParametersTexts(): array
    {
        return $this->paramsTexts;
    }

    /**
     * Check whether this rule is implicit
     */
    public function isImplicit(): bool
    {
        return $this->implicit;
    }

    /**
     * Just alias of setMessage
     */
    public function message(string $message): Rule
    {
        return $this->setMessage($message);
    }

    /**
     * Set message
     */
    public function setMessage(string $message): Rule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Check given $params must be exists
     *
     * @return void
     * @throws \Rakit\Validation\MissingRequiredParameterException
     */
    protected function requireParameters(array $params)
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                $rule = $this->getKey();
                throw new MissingRequiredParameterException(sprintf("Missing required parameter '%s' on rule '%s'", $param, $rule));
            }
        }
    }
}
