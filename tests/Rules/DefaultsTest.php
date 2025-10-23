<?php

use Rakit\Validation\Rules\Defaults;

beforeEach(function () {
    $this->rule = new Defaults;
});

test('defaults', function () {
    expect($this->rule->fillParameters([10])->check(0))->toBeTrue();
    expect($this->rule->fillParameters(['something'])->check(null))->toBeTrue();
    expect($this->rule->fillParameters([[1,2,3]])->check(false))->toBeTrue();
    expect($this->rule->fillParameters([[1,2,3]])->check([]))->toBeTrue();
});
