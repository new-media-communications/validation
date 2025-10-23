<?php

use Rakit\Validation\Rules\Date;

test('valids', function () {
    $rule = new Date;

    expect($rule->check('2010-10-10'))->toBeTrue();
    expect($rule->fillParameters(['d-m-Y'])->check('10-10-2010'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Date;

    expect($rule->check('10-10-2010'))->toBeFalse();
    expect($rule->fillParameters(['Y-m-d'])->check('2010-10-10 10:10'))->toBeFalse();
});
