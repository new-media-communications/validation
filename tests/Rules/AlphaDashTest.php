<?php

use Nmc\Validation\Rules\AlphaDash;

test('valids', function () {
    $rule = new AlphaDash;

    expect($rule->check('123'))->toBeTrue();
    expect($rule->check('abc'))->toBeTrue();
    expect($rule->check('123abc'))->toBeTrue();
    expect($rule->check('abc123'))->toBeTrue();
    expect($rule->check('foo_123'))->toBeTrue();
    expect($rule->check('213-foo'))->toBeTrue();
});

test('invalids', function () {
    $rule = new AlphaDash;

    expect($rule->check('foo bar'))->toBeFalse();
    expect($rule->check('123 bar '))->toBeFalse();
});
