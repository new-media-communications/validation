<?php

use Nmc\Validation\Rules\Lowercase;

test('valids', function () {
    $rule = new Lowercase;

    expect($rule->check('username'))->toBeTrue();
    expect($rule->check('full name'))->toBeTrue();
    expect($rule->check('full_name'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Lowercase;

    expect($rule->check('USERNAME'))->toBeFalse();
    expect($rule->check('Username'))->toBeFalse();
    expect($rule->check('userName'))->toBeFalse();
});
