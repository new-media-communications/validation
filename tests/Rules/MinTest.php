<?php

use Nmc\Validation\Rules\Min;

test('valids', function () {
    $rule = new Min;

    expect($rule->fillParameters([100])->check(123))->toBeTrue();
    expect($rule->fillParameters([6])->check('foobar'))->toBeTrue();
    expect($rule->fillParameters([3])->check([1, 2, 3]))->toBeTrue();
});

test('invalids', function () {
    $rule = new Min;

    expect($rule->fillParameters([7])->check('foobar'))->toBeFalse();
    expect($rule->fillParameters([4])->check([1, 2, 3]))->toBeFalse();
    expect($rule->fillParameters([200])->check(123))->toBeFalse();

    expect($rule->fillParameters([4])->check('мин'))->toBeFalse();
    expect($rule->fillParameters([5])->check('كلمة'))->toBeFalse();
    expect($rule->fillParameters([4])->check('ワード'))->toBeFalse();
    expect($rule->fillParameters([2])->check('字'))->toBeFalse();
});

test('uploaded file value', function () {
    $rule = new Min;

    $twoMega = 1024 * 1024 * 2;
    $sampleFile = [
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => $twoMega,
        'tmp_name' => __FILE__,
        'error' => 0,
    ];

    expect($rule->fillParameters([$twoMega])->check($sampleFile))->toBeTrue();
    expect($rule->fillParameters(['2M'])->check($sampleFile))->toBeTrue();

    expect($rule->fillParameters([$twoMega + 1])->check($sampleFile))->toBeFalse();
    expect($rule->fillParameters(['2.1M'])->check($sampleFile))->toBeFalse();
});
