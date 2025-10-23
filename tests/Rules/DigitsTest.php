<?php

use Nmc\Validation\Rules\Digits;

test('valids', function () {
    $rule = new Digits;

    expect($rule->fillParameters([4])->check(1243))->toBeTrue();
    expect($rule->fillParameters([6])->check(124567))->toBeTrue();
    expect($rule->fillParameters([3])->check('123'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Digits;

    expect($rule->fillParameters([7])->check(12345678))->toBeFalse();
    expect($rule->fillParameters([4])->check(12))->toBeFalse();
    expect($rule->fillParameters([3])->check('foo'))->toBeFalse();
});
