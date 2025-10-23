<?php

use Nmc\Validation\Rules\TypeArray;

test('valids', function () {
    $rule = new TypeArray;

    expect($rule->check([]))->toBeTrue();
    expect($rule->check([1, 2, 3]))->toBeTrue();
    expect($rule->check([1, 2, [4, 5, 6]]))->toBeTrue();
});

test('invalids', function () {
    $rule = new TypeArray;

    expect($rule->check('[]'))->toBeFalse();
    expect($rule->check('[1,2,3]'))->toBeFalse();
});
