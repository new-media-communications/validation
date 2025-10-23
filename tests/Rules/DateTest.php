<?php

use Rakit\Validation\Rules\Date;

beforeEach(function () {
    $this->rule = new Date;
});

test('valids', function () {
    expect($this->rule->check('2010-10-10'))->toBeTrue();
    expect($this->rule->fillParameters(['d-m-Y'])->check('10-10-2010'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('10-10-2010'))->toBeFalse();
    expect($this->rule->fillParameters(['Y-m-d'])->check('2010-10-10 10:10'))->toBeFalse();
});
