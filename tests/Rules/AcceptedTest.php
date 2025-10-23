<?php

use Rakit\Validation\Rules\Accepted;

beforeEach(function () {
    $this->rule = new Accepted;
});

test('valids', function () {
    expect($this->rule->check('yes'))->toBeTrue();
    expect($this->rule->check('on'))->toBeTrue();
    expect($this->rule->check('1'))->toBeTrue();
    expect($this->rule->check(1))->toBeTrue();
    expect($this->rule->check(true))->toBeTrue();
    expect($this->rule->check('true'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(''))->toBeFalse();
    expect($this->rule->check('onn'))->toBeFalse();
    expect($this->rule->check(' 1'))->toBeFalse();
    expect($this->rule->check(10))->toBeFalse();
});
