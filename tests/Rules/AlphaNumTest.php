<?php

use Rakit\Validation\Rules\AlphaNum;

test('valids', function () {
    $rule = new AlphaNum;

    expect($rule->check('123'))->toBeTrue();
    expect($rule->check('abc'))->toBeTrue();
    expect($rule->check('123abc'))->toBeTrue();
    expect($rule->check('abc123'))->toBeTrue();
});

test('invalids', function () {
    $rule = new AlphaNum;

    expect($rule->check('foo 123'))->toBeFalse();
    expect($rule->check('123 foo'))->toBeFalse();
    expect($rule->check(' foo123 '))->toBeFalse();
});
