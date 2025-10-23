<?php

use Rakit\Validation\Rules\Uppercase;

beforeEach(function () {
    $this->rule = new Uppercase;
});

test('valids', function () {
    expect($this->rule->check('USERNAME'))->toBeTrue();
    expect($this->rule->check('FULL NAME'))->toBeTrue();
    expect($this->rule->check('FULL_NAME'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('username'))->toBeFalse();
    expect($this->rule->check('Username'))->toBeFalse();
    expect($this->rule->check('userName'))->toBeFalse();
});
