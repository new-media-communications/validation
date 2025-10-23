<?php

namespace Rakit\Validation;

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

    /** @var array<array-key, mixed> */
    protected $params = [];

    /** @var array<string, string> */
    protected $paramsTexts = [];

    /** @var list<string> */
    protected $fillableParams = [];

    /** @var string */
    protected $message = 'The :attribute is invalid';

    /**
     * @param  mixed  $value
     */
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
     *
     * @return array<array-key, mixed>
     */
    public function getParameters(): array
    {
        return $this->params;
    }

    /**
     * Set params
     *
     * @param  array<array-key, mixed>  $params
     */
    public function setParameters(array $params): static
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Set parameters
     */
    public function setParameter(string $key, mixed $value): static
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Fill $params to $this->params
     *
     * @param  array<array-key, mixed>  $params
     */
    public function fillParameters(array $params): static
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
     *
     * @return array<string, string>
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
    public function message(string $message): static
    {
        return $this->setMessage($message);
    }

    /**
     * Set message
     */
    public function setMessage(string $message): static
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
     * @param  list<string>  $params
     * @return void
     *
     * @throws \Rakit\Validation\MissingRequiredParameterException
     */
    protected function requireParameters(array $params)
    {
        foreach ($params as $param) {
            if (! isset($this->params[$param])) {
                $rule = $this->getKey();
                throw new MissingRequiredParameterException(sprintf("Missing required parameter '%s' on rule '%s'", $param, $rule));
            }
        }
    }
}
