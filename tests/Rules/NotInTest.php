<?php

use Nmc\Validation\Rules\NotIn;

test('valids', function () {
    $rule = new NotIn;

    expect($rule->fillParameters(['2', '3', '4'])->check('1'))->toBeTrue();
    expect($rule->fillParameters([1, 2, 3])->check(5))->toBeTrue();
});

test('invalids', function () {
    $rule = new NotIn;

    expect($rule->fillParameters(['bar', 'baz', 'qux'])->check('bar'))->toBeFalse();
});

test('stricts', function () {
    $rule = new NotIn;

    // Not strict
    expect($rule->fillParameters(['1', '2', '3'])->check(1))->toBeFalse();
    expect($rule->fillParameters(['1', '2', '3'])->check(true))->toBeFalse();

    // Strict
    $rule->strict();
    expect($rule->fillParameters(['1', '2', '3'])->check(1))->toBeTrue();
    expect($rule->fillParameters(['1', '2', '3'])->check(1))->toBeTrue();
});
