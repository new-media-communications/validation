<?php

use Rakit\Validation\Rules\Digits;

beforeEach(function () {
    $this->rule = new Digits;
});

test('valids', function () {
    expect($this->rule->fillParameters([4])->check(1243))->toBeTrue();
    expect($this->rule->fillParameters([6])->check(124567))->toBeTrue();
    expect($this->rule->fillParameters([3])->check('123'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters([7])->check(12345678))->toBeFalse();
    expect($this->rule->fillParameters([4])->check(12))->toBeFalse();
    expect($this->rule->fillParameters([3])->check('foo'))->toBeFalse();
});
