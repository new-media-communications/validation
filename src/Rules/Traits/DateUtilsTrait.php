<?php

namespace Rakit\Validation\Rules\Traits;

use Exception;

trait DateUtilsTrait
{
    /**
     * Check the $date is valid
     */
    protected function isValidDate(string $date): bool
    {
        return strtotime($date) !== false;
    }

    /**
     * Throw exception
     */
    protected function throwException(string $value): \Exception
    {
        // phpcs:ignore
        return new Exception(sprintf("Expected a valid date, got '%s' instead. 2016-12-08, 2016-12-02 14:58, tomorrow are considered valid dates", $value));
    }

    /**
     * Given $date and get the time stamp
     *
     * @param  mixed  $date
     */
    protected function getTimeStamp($date): int
    {
        return strtotime((string) $date);
    }
}
