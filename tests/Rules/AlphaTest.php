<?php

use Nmc\Validation\Rules\Alpha;

test('valids', function () {
    $rule = new Alpha;

    expect($rule->check('foo'))->toBeTrue();
    expect($rule->check('foobar'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Alpha;

    expect($rule->check(2))->toBeFalse();
    expect($rule->check([]))->toBeFalse();
    expect($rule->check(new stdClass))->toBeFalse();
    expect($rule->check('123asd'))->toBeFalse();
    expect($rule->check('asd123'))->toBeFalse();
    expect($rule->check('foo123bar'))->toBeFalse();
    expect($rule->check('foo bar'))->toBeFalse();
});
