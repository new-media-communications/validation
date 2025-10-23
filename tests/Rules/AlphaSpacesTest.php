<?php

use Nmc\Validation\Rules\AlphaSpaces;

test('valids', function () {
    $rule = new AlphaSpaces;

    expect($rule->check('abc'))->toBeTrue();
    expect($rule->check('foo bar'))->toBeTrue();
    expect($rule->check('foo bar  bar'))->toBeTrue();
});

test('invalids', function () {
    $rule = new AlphaSpaces;

    expect($rule->check('123'))->toBeFalse();
    expect($rule->check('123abc'))->toBeFalse();
    expect($rule->check('abc123'))->toBeFalse();
    expect($rule->check('foo_123'))->toBeFalse();
    expect($rule->check('213-foo'))->toBeFalse();
});
