<?php

use Rakit\Validation\Rules\Callback;

test('valids', function () {
    $rule = new Callback;
    $rule->setCallback(function ($value) {
        return is_numeric($value) and $value % 2 === 0;
    });

    expect($rule->check(2))->toBeTrue();
    expect($rule->check('4'))->toBeTrue();
    expect($rule->check('1000'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Callback;
    $rule->setCallback(function ($value) {
        return is_numeric($value) and $value % 2 === 0;
    });

    expect($rule->check(1))->toBeFalse();
    expect($rule->check('abc12'))->toBeFalse();
    expect($rule->check('12abc'))->toBeFalse();
});
