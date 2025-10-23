<?php

use Rakit\Validation\Rules\Extension;

beforeEach(function () {
    $this->rule = new Extension;
});

test('valids', function () {
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('somefile.txt'))->toBeTrue();
    expect($this->rule->fillParameters(['.pdf', '.png', '.txt'])->check('somefile.txt'))->toBeTrue();
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('path/to/somefile.txt'))->toBeTrue();
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('./absolute/path/to/somefile.txt'))->toBeTrue();
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('https://site.test/somefile.txt'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check(''))->toBeFalse();
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('.dotfile'))->toBeFalse();
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('notafile'))->toBeFalse();
    expect($this->rule->fillParameters(['pdf', 'png', 'txt'])->check('somefile.php'))->toBeFalse();
    expect($this->rule->fillParameters(['.pdf', '.png', '.txt'])->check('somefile.php'))->toBeFalse();
});
