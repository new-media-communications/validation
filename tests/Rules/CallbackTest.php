<?php

use Rakit\Validation\Rules\Callback;

beforeEach(function () {
    $this->rule = new Callback;
    $this->rule->setCallback(function ($value) {
        return is_numeric($value) and $value % 2 === 0;
    });
});

test('valids', function () {
    expect($this->rule->check(2))->toBeTrue();
    expect($this->rule->check('4'))->toBeTrue();
    expect($this->rule->check('1000'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(1))->toBeFalse();
    expect($this->rule->check('abc12'))->toBeFalse();
    expect($this->rule->check('12abc'))->toBeFalse();
});
