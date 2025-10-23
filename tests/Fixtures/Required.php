<?php

namespace Tests;

use Nmc\Validation\Rule;

class Required extends Rule
{
    public function check($value): bool
    {
        return true;
    }
}
