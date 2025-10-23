<?php

namespace Rakit\Validation\Rules;

use Closure;
use InvalidArgumentException;
use Rakit\Validation\Rule;

class Callback extends Rule
{
    /** @var string */
    protected $message = 'The :attribute is not valid';

    /** @var array */
    protected $fillableParams = ['callback'];

    /**
     * Set the Callback closure
     *
     * @return self
     */
    public function setCallback(Closure $callback): Rule
    {
        return $this->setParameter('callback', $callback);
    }

    /**
     * Check the $value is valid
     *
     * @param  mixed  $value
     *
     * @throws \Exception
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $callback = $this->parameter('callback');
        if ($callback instanceof Closure === false) {
            $key = $this->attribute->getKey();
            throw new InvalidArgumentException(sprintf("Callback rule for '%s' is not callable.", $key));
        }

        $callback = $callback->bindTo($this);
        $invalidMessage = $callback($value);

        if (is_string($invalidMessage)) {
            $this->setMessage($invalidMessage);

            return false;
        } elseif ($invalidMessage === false) {
            return false;
        }

        return true;
    }
}
