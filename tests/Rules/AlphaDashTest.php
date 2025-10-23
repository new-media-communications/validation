<?php

use Rakit\Validation\Rules\AlphaDash;

beforeEach(function () {
    $this->rule = new AlphaDash;
});

test('valids', function () {
    expect($this->rule->check('123'))->toBeTrue();
    expect($this->rule->check('abc'))->toBeTrue();
    expect($this->rule->check('123abc'))->toBeTrue();
    expect($this->rule->check('abc123'))->toBeTrue();
    expect($this->rule->check('foo_123'))->toBeTrue();
    expect($this->rule->check('213-foo'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('foo bar'))->toBeFalse();
    expect($this->rule->check('123 bar '))->toBeFalse();
});
