<?php

use Rakit\Validation\Rules\NotIn;

beforeEach(function () {
    $this->rule = new NotIn;
});

test('valids', function () {
    expect($this->rule->fillParameters(['2', '3', '4'])->check('1'))->toBeTrue();
    expect($this->rule->fillParameters([1, 2, 3])->check(5))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters(['bar', 'baz', 'qux'])->check('bar'))->toBeFalse();
});

test('stricts', function () {
    // Not strict
    expect($this->rule->fillParameters(['1', '2', '3'])->check(1))->toBeFalse();
    expect($this->rule->fillParameters(['1', '2', '3'])->check(true))->toBeFalse();

    // Strict
    $this->rule->strict();
    expect($this->rule->fillParameters(['1', '2', '3'])->check(1))->toBeTrue();
    expect($this->rule->fillParameters(['1', '2', '3'])->check(1))->toBeTrue();
});
