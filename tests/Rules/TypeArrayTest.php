<?php

use Rakit\Validation\Rules\TypeArray;

beforeEach(function () {
    $this->rule = new TypeArray;
});

test('valids', function () {
    expect($this->rule->check([]))->toBeTrue();
    expect($this->rule->check([1,2,3]))->toBeTrue();
    expect($this->rule->check([1,2,[4,5,6]]))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('[]'))->toBeFalse();
    expect($this->rule->check('[1,2,3]'))->toBeFalse();
});
