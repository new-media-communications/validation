<?php

use Rakit\Validation\Rules\Min;

beforeEach(function () {
    $this->rule = new Min;
});

test('valids', function () {
    expect($this->rule->fillParameters([100])->check(123))->toBeTrue();
    expect($this->rule->fillParameters([6])->check('foobar'))->toBeTrue();
    expect($this->rule->fillParameters([3])->check([1, 2, 3]))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->fillParameters([7])->check('foobar'))->toBeFalse();
    expect($this->rule->fillParameters([4])->check([1, 2, 3]))->toBeFalse();
    expect($this->rule->fillParameters([200])->check(123))->toBeFalse();

    expect($this->rule->fillParameters([4])->check('мин'))->toBeFalse();
    expect($this->rule->fillParameters([5])->check('كلمة'))->toBeFalse();
    expect($this->rule->fillParameters([4])->check('ワード'))->toBeFalse();
    expect($this->rule->fillParameters([2])->check('字'))->toBeFalse();
});

test('uploaded file value', function () {
    $twoMega = 1024 * 1024 * 2;
    $sampleFile = [
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => $twoMega,
        'tmp_name' => __FILE__,
        'error' => 0,
    ];

    expect($this->rule->fillParameters([$twoMega])->check($sampleFile))->toBeTrue();
    expect($this->rule->fillParameters(['2M'])->check($sampleFile))->toBeTrue();

    expect($this->rule->fillParameters([$twoMega + 1])->check($sampleFile))->toBeFalse();
    expect($this->rule->fillParameters(['2.1M'])->check($sampleFile))->toBeFalse();
});
