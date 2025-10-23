<?php

use Nmc\Validation\Rules\Json;

test('valids', function () {
    $rule = new Json;

    expect($rule->check('{}'))->toBeTrue();
    expect($rule->check('[]'))->toBeTrue();
    expect($rule->check('false'))->toBeTrue();
    expect($rule->check('null'))->toBeTrue();
    expect($rule->check('{"username": "John Doe"}'))->toBeTrue();
    expect($rule->check('{"number": 12345678}'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Json;

    expect($rule->check(''))->toBeFalse();
    expect($rule->check(123))->toBeFalse();
    expect($rule->check(false))->toBeFalse();
    expect($rule->check('{"username": John Doe}'))->toBeFalse();
    expect($rule->check('{number: 12345678}'))->toBeFalse();
});
