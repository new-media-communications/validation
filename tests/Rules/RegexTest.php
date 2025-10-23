<?php

use Rakit\Validation\Rules\Regex;

test('valids', function () {
    expect((new Regex)->fillParameters(['/^F/i'])->check('foo'))->toBeTrue();
});

test('invalids', function () {
    expect((new Regex)->fillParameters(['/^F/i'])->check('bar'))->toBeFalse();
});
