<?php

use Rakit\Validation\Rules\AlphaNum;

beforeEach(function () {
    $this->rule = new AlphaNum;
});

test('valids', function () {
    expect($this->rule->check('123'))->toBeTrue();
    expect($this->rule->check('abc'))->toBeTrue();
    expect($this->rule->check('123abc'))->toBeTrue();
    expect($this->rule->check('abc123'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('foo 123'))->toBeFalse();
    expect($this->rule->check('123 foo'))->toBeFalse();
    expect($this->rule->check(' foo123 '))->toBeFalse();
});
