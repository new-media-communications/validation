<?php

use Rakit\Validation\Rules\Between;

test('valids', function () {
    $rule = new Between;

    expect($rule->fillParameters([6, 10])->check('foobar'))->toBeTrue();
    expect($rule->fillParameters([6, 10])->check('футбол'))->toBeTrue();
    expect($rule->fillParameters([2, 3])->check([1, 2, 3]))->toBeTrue();
    expect($rule->fillParameters([100, 150])->check(123))->toBeTrue();
    expect($rule->fillParameters([100, 150])->check(123.4))->toBeTrue();
});

test('invalids', function () {
    $rule = new Between;

    expect($rule->fillParameters([2, 5])->check('foobar'))->toBeFalse();
    expect($rule->fillParameters([2, 5])->check('футбол'))->toBeFalse();
    expect($rule->fillParameters([4, 6])->check([1, 2, 3]))->toBeFalse();
    expect($rule->fillParameters([50, 100])->check(123))->toBeFalse();
    expect($rule->fillParameters([50, 100])->check(123.4))->toBeFalse();
});

test('uploaded file value', function () {
    $mb = function ($n) {
        return $n * 1024 * 1024;
    };

    $sampleFile = [
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => $mb(2),
        'tmp_name' => __FILE__,
        'error' => 0,
    ];

    $rule = new Between;

    expect($rule->fillParameters([$mb(2), $mb(5)])->check($sampleFile))->toBeTrue();
    expect($rule->fillParameters(['2M', '5M'])->check($sampleFile))->toBeTrue();
    expect($rule->fillParameters([$mb(1), $mb(2)])->check($sampleFile))->toBeTrue();
    expect($rule->fillParameters(['1M', '2M'])->check($sampleFile))->toBeTrue();

    expect($rule->fillParameters([$mb(2.1), $mb(5)])->check($sampleFile))->toBeFalse();
    expect($rule->fillParameters(['2.1M', '5M'])->check($sampleFile))->toBeFalse();
    expect($rule->fillParameters([$mb(1), $mb(1.9)])->check($sampleFile))->toBeFalse();
    expect($rule->fillParameters(['1M', '1.9M'])->check($sampleFile))->toBeFalse();
});
