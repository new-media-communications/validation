<?php

use Rakit\Validation\Rules\Required;

beforeEach(function () {
    $this->rule = new Required;
});

test('valids', function () {
    expect($this->rule->check('foo'))->toBeTrue();
    expect($this->rule->check([1]))->toBeTrue();
    expect($this->rule->check(1))->toBeTrue();
    expect($this->rule->check(true))->toBeTrue();
    expect($this->rule->check('0'))->toBeTrue();
    expect($this->rule->check(0))->toBeTrue();
    expect($this->rule->check(new stdClass))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(null))->toBeFalse();
    expect($this->rule->check(''))->toBeFalse();
    expect($this->rule->check([]))->toBeFalse();
});
