<?php

use Rakit\Validation\Rules\In;

beforeEach(function () {
    $this->rule = new In;
});

test('valids', function () {
    expect($this->rule->fillParameters([1,2,3])->check(1))->toBeTrue();
    expect($this->rule->fillParameters(['1', 'bar', '3'])->check('bar'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters([1,2,3])->check(4))->toBeFalse();
});

test('stricts', function () {
    // Not strict
    expect($this->rule->fillParameters(['1', '2', '3'])->check(1))->toBeTrue();
    expect($this->rule->fillParameters(['1', '2', '3'])->check(true))->toBeTrue();

    // Strict
    $this->rule->strict();
    expect($this->rule->fillParameters(['1', '2', '3'])->check(1))->toBeFalse();
    expect($this->rule->fillParameters(['1', '2', '3'])->check(1))->toBeFalse();
});
