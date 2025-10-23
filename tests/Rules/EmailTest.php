<?php

use Rakit\Validation\Rules\Email;

test('valids', function () {
    $rule = new Email;

    expect($rule->check('johndoe@gmail.com'))->toBeTrue();
    expect($rule->check('johndoe@foo.bar'))->toBeTrue();
    expect($rule->check('foo123123@foo.bar.baz'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Email;

    expect($rule->check(1))->toBeFalse();
    expect($rule->check('john doe@gmail.com'))->toBeFalse();
    expect($rule->check('johndoe'))->toBeFalse();
    expect($rule->check('johndoe.gmail.com'))->toBeFalse();
    expect($rule->check('johndoe.gmail.com'))->toBeFalse();
});
