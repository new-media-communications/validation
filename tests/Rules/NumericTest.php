<?php

use Rakit\Validation\Rules\Numeric;

test('valids', function () {
    $rule = new Numeric;

    expect($rule->check('123'))->toBeTrue();
    expect($rule->check('123.456'))->toBeTrue();
    expect($rule->check('-123.456'))->toBeTrue();
    expect($rule->check(123))->toBeTrue();
    expect($rule->check(123.456))->toBeTrue();
});

test('invalids', function () {
    $rule = new Numeric;

    expect($rule->check('foo123'))->toBeFalse();
    expect($rule->check('123foo'))->toBeFalse();
    expect($rule->check([123]))->toBeFalse();
});
