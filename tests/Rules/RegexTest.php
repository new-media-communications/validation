<?php

use Rakit\Validation\Rules\Regex;

beforeEach(function () {
    $this->rule = new Regex;
});

test('valids', function () {
    expect($this->rule->fillParameters(['/^F/i'])->check('foo'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters(['/^F/i'])->check('bar'))->toBeFalse();
});
