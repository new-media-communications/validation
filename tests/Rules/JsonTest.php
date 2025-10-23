<?php

use Rakit\Validation\Rules\Json;

beforeEach(function () {
    $this->rule = new Json;
});

test('valids', function () {
    expect($this->rule->check('{}'))->toBeTrue();
    expect($this->rule->check('[]'))->toBeTrue();
    expect($this->rule->check('false'))->toBeTrue();
    expect($this->rule->check('null'))->toBeTrue();
    expect($this->rule->check('{"username": "John Doe"}'))->toBeTrue();
    expect($this->rule->check('{"number": 12345678}'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(''))->toBeFalse();
    expect($this->rule->check(123))->toBeFalse();
    expect($this->rule->check(false))->toBeFalse();
    expect($this->rule->check('{"username": John Doe}'))->toBeFalse();
    expect($this->rule->check('{number: 12345678}'))->toBeFalse();
});
