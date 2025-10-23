<?php

use Rakit\Validation\Rules\Email;

beforeEach(function () {
    $this->rule = new Email;
});

test('valids', function () {
    expect($this->rule->check('johndoe@gmail.com'))->toBeTrue();
    expect($this->rule->check('johndoe@foo.bar'))->toBeTrue();
    expect($this->rule->check('foo123123@foo.bar.baz'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(1))->toBeFalse();
    expect($this->rule->check('john doe@gmail.com'))->toBeFalse();
    expect($this->rule->check('johndoe'))->toBeFalse();
    expect($this->rule->check('johndoe.gmail.com'))->toBeFalse();
    expect($this->rule->check('johndoe.gmail.com'))->toBeFalse();
});
