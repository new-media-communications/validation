<?php

use Rakit\Validation\Rules\Uppercase;

test('valids', function () {
    $rule = new Uppercase;

    expect($rule->check('USERNAME'))->toBeTrue();
    expect($rule->check('FULL NAME'))->toBeTrue();
    expect($rule->check('FULL_NAME'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Uppercase;

    expect($rule->check('username'))->toBeFalse();
    expect($rule->check('Username'))->toBeFalse();
    expect($rule->check('userName'))->toBeFalse();
});
