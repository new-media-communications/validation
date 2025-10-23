<?php

use Rakit\Validation\Rules\AlphaSpaces;

beforeEach(function () {
    $this->rule = new AlphaSpaces;
});

test('valids', function () {
    expect($this->rule->check('abc'))->toBeTrue();
    expect($this->rule->check('foo bar'))->toBeTrue();
    expect($this->rule->check('foo bar  bar'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('123'))->toBeFalse();
    expect($this->rule->check('123abc'))->toBeFalse();
    expect($this->rule->check('abc123'))->toBeFalse();
    expect($this->rule->check('foo_123'))->toBeFalse();
    expect($this->rule->check('213-foo'))->toBeFalse();
});
