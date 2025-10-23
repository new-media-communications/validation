<?php

use Nmc\Validation\Rules\Boolean;

test('valids', function () {
    $rule = new Boolean;

    expect($rule->check(\true))->toBeTrue();
    expect($rule->check(\false))->toBeTrue();
    expect($rule->check(1))->toBeTrue();
    expect($rule->check(0))->toBeTrue();
    expect($rule->check('1'))->toBeTrue();
    expect($rule->check('0'))->toBeTrue();
    expect($rule->check('y'))->toBeTrue();
    expect($rule->check('n'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Boolean;

    expect($rule->check(11))->toBeFalse();
    expect($rule->check([]))->toBeFalse();
    expect($rule->check('foo123'))->toBeFalse();
    expect($rule->check('123foo'))->toBeFalse();
    expect($rule->check([123]))->toBeFalse();
    expect($rule->check('123.456'))->toBeFalse();
    expect($rule->check('-123.456'))->toBeFalse();
});
