<?php

use Rakit\Validation\Rules\Lowercase;

beforeEach(function () {
    $this->rule = new Lowercase;
});

test('valids', function () {
    expect($this->rule->check('username'))->toBeTrue();
    expect($this->rule->check('full name'))->toBeTrue();
    expect($this->rule->check('full_name'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('USERNAME'))->toBeFalse();
    expect($this->rule->check('Username'))->toBeFalse();
    expect($this->rule->check('userName'))->toBeFalse();
});
