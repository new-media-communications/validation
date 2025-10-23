<?php

use Rakit\Validation\Rules\Integer;

test('valids', function () {
    $rule = new Integer;

    expect($rule->check(0))->toBeTrue();
    expect($rule->check('0'))->toBeTrue();
    expect($rule->check('123'))->toBeTrue();
    expect($rule->check('-123'))->toBeTrue();
    expect($rule->check(123))->toBeTrue();
    expect($rule->check(-123))->toBeTrue();
});

test('invalids', function () {
    $rule = new Integer;

    expect($rule->check('foo123'))->toBeFalse();
    expect($rule->check('123foo'))->toBeFalse();
    expect($rule->check([123]))->toBeFalse();
    expect($rule->check('123.456'))->toBeFalse();
    expect($rule->check('-123.456'))->toBeFalse();
});
