<?php

use Rakit\Validation\Rules\DigitsBetween;

beforeEach(function () {
    $this->rule = new DigitsBetween;
});

test('valids', function () {
    expect($this->rule->fillParameters([2, 6])->check(12345))->toBeTrue();
    expect($this->rule->fillParameters([2, 3])->check(12))->toBeTrue();
    expect($this->rule->fillParameters([2, 3])->check(123))->toBeTrue();
    expect($this->rule->fillParameters([3, 5])->check('12345'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters([4, 6])->check(12))->toBeFalse();
    expect($this->rule->fillParameters([1, 3])->check(12345))->toBeFalse();
    expect($this->rule->fillParameters([1, 3])->check(12345))->toBeFalse();
    expect($this->rule->fillParameters([3, 6])->check('foobar'))->toBeFalse();
});
