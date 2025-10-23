<?php

use Rakit\Validation\Rules\Integer;

beforeEach(function () {
    $this->rule = new Integer;
});

test('valids', function () {
    expect($this->rule->check(0))->toBeTrue();
    expect($this->rule->check('0'))->toBeTrue();
    expect($this->rule->check('123'))->toBeTrue();
    expect($this->rule->check('-123'))->toBeTrue();
    expect($this->rule->check(123))->toBeTrue();
    expect($this->rule->check(-123))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('foo123'))->toBeFalse();
    expect($this->rule->check('123foo'))->toBeFalse();
    expect($this->rule->check([123]))->toBeFalse();
    expect($this->rule->check('123.456'))->toBeFalse();
    expect($this->rule->check('-123.456'))->toBeFalse();
});
