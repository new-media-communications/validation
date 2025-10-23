<?php

use Rakit\Validation\Rules\Numeric;

beforeEach(function () {
    $this->rule = new Numeric;
});

test('valids', function () {
    expect($this->rule->check('123'))->toBeTrue();
    expect($this->rule->check('123.456'))->toBeTrue();
    expect($this->rule->check('-123.456'))->toBeTrue();
    expect($this->rule->check(123))->toBeTrue();
    expect($this->rule->check(123.456))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('foo123'))->toBeFalse();
    expect($this->rule->check('123foo'))->toBeFalse();
    expect($this->rule->check([123]))->toBeFalse();
});
