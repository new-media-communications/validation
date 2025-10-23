<?php

use Rakit\Validation\Rules\Defaults;

test('defaults', function () {
    $rule = new Defaults;

    expect($rule->fillParameters([10])->check(0))->toBeTrue();
    expect($rule->fillParameters(['something'])->check(null))->toBeTrue();
    expect($rule->fillParameters([[1, 2, 3]])->check(false))->toBeTrue();
    expect($rule->fillParameters([[1, 2, 3]])->check([]))->toBeTrue();
});
