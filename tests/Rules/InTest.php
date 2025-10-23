<?php

use Nmc\Validation\Rules\In;

test('valids', function () {
    $rule = new In;

    expect($rule->fillParameters([1, 2, 3])->check(1))->toBeTrue();
    expect($rule->fillParameters(['1', 'bar', '3'])->check('bar'))->toBeTrue();
});

test('invalids', function () {
    $rule = new In;

    expect($rule->fillParameters([1, 2, 3])->check(4))->toBeFalse();
});

test('stricts', function () {
    $rule = new In;

    // Not strict
    expect($rule->fillParameters(['1', '2', '3'])->check(1))->toBeTrue();
    expect($rule->fillParameters(['1', '2', '3'])->check(true))->toBeTrue();

    // Strict
    $rule->strict();
    expect($rule->fillParameters(['1', '2', '3'])->check(1))->toBeFalse();
    expect($rule->fillParameters(['1', '2', '3'])->check(1))->toBeFalse();
});
