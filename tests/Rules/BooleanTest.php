<?php

use Rakit\Validation\Rules\Boolean;


beforeEach(function () {
    $this->rule = new Boolean;
});

test('valids', function () {
    expect($this->rule->check(\true))->toBeTrue();
    expect($this->rule->check(\false))->toBeTrue();
    expect($this->rule->check(1))->toBeTrue();
    expect($this->rule->check(0))->toBeTrue();
    expect($this->rule->check('1'))->toBeTrue();
    expect($this->rule->check('0'))->toBeTrue();
    expect($this->rule->check('y'))->toBeTrue();
    expect($this->rule->check('n'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(11))->toBeFalse();
    expect($this->rule->check([]))->toBeFalse();
    expect($this->rule->check('foo123'))->toBeFalse();
    expect($this->rule->check('123foo'))->toBeFalse();
    expect($this->rule->check([123]))->toBeFalse();
    expect($this->rule->check('123.456'))->toBeFalse();
    expect($this->rule->check('-123.456'))->toBeFalse();
});
