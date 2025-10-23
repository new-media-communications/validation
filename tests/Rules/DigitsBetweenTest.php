<?php

use Nmc\Validation\Rules\DigitsBetween;

test('valids', function () {
    $rule = new DigitsBetween;

    expect($rule->fillParameters([2, 6])->check(12345))->toBeTrue();
    expect($rule->fillParameters([2, 3])->check(12))->toBeTrue();
    expect($rule->fillParameters([2, 3])->check(123))->toBeTrue();
    expect($rule->fillParameters([3, 5])->check('12345'))->toBeTrue();
});

test('invalids', function () {
    $rule = new DigitsBetween;

    expect($rule->fillParameters([4, 6])->check(12))->toBeFalse();
    expect($rule->fillParameters([1, 3])->check(12345))->toBeFalse();
    expect($rule->fillParameters([1, 3])->check(12345))->toBeFalse();
    expect($rule->fillParameters([3, 6])->check('foobar'))->toBeFalse();
});
