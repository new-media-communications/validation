<?php

use Rakit\Validation\Rules\Alpha;

beforeEach(function () {
    $this->rule = new Alpha;
});

test('valids', function () {
    expect($this->rule->check('foo'))->toBeTrue();
    expect($this->rule->check('foobar'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check(2))->toBeFalse();
    expect($this->rule->check([]))->toBeFalse();
    expect($this->rule->check(new stdClass))->toBeFalse();
    expect($this->rule->check('123asd'))->toBeFalse();
    expect($this->rule->check('asd123'))->toBeFalse();
    expect($this->rule->check('foo123bar'))->toBeFalse();
    expect($this->rule->check('foo bar'))->toBeFalse();
});
