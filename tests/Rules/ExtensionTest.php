<?php

use Rakit\Validation\Rules\Extension;

test('valids', function () {
    $rule = new Extension;

    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('somefile.txt'))->toBeTrue();
    expect($rule->fillParameters(['.pdf', '.png', '.txt'])->check('somefile.txt'))->toBeTrue();
    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('path/to/somefile.txt'))->toBeTrue();
    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('./absolute/path/to/somefile.txt'))->toBeTrue();
    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('https://site.test/somefile.txt'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Extension;

    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check(''))->toBeFalse();
    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('.dotfile'))->toBeFalse();
    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('notafile'))->toBeFalse();
    expect($rule->fillParameters(['pdf', 'png', 'txt'])->check('somefile.php'))->toBeFalse();
    expect($rule->fillParameters(['.pdf', '.png', '.txt'])->check('somefile.php'))->toBeFalse();
});
