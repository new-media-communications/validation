<?php

use Rakit\Validation\Rules\Required;

test('valids', function () {
    $rule = new Required;

    expect($rule->check('foo'))->toBeTrue();
    expect($rule->check([1]))->toBeTrue();
    expect($rule->check(1))->toBeTrue();
    expect($rule->check(true))->toBeTrue();
    expect($rule->check('0'))->toBeTrue();
    expect($rule->check(0))->toBeTrue();
    expect($rule->check(new stdClass))->toBeTrue();
});

test('invalids', function () {
    $rule = new Required;

    expect($rule->check(null))->toBeFalse();
    expect($rule->check(''))->toBeFalse();
    expect($rule->check([]))->toBeFalse();
});
