<?php

use Rakit\Validation\Rules\Accepted;

test('valids', function () {
    $rule = new Accepted;

    expect($rule->check('yes'))->toBeTrue();
    expect($rule->check('on'))->toBeTrue();
    expect($rule->check('1'))->toBeTrue();
    expect($rule->check(1))->toBeTrue();
    expect($rule->check(true))->toBeTrue();
    expect($rule->check('true'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Accepted;

    expect($rule->check(''))->toBeFalse();
    expect($rule->check('onn'))->toBeFalse();
    expect($rule->check(' 1'))->toBeFalse();
    expect($rule->check(10))->toBeFalse();
});
