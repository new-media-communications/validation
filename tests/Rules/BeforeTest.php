<?php

use Nmc\Validation\Rules\Before;

test('only awell formed date can be validated', function ($date) {
    expect((new Before)->fillParameters(['next week'])->check($date))->toBeTrue();
})->with('getValidDates');

dataset('getValidDates', function () {
    $now = new DateTime;

    return [
        [2016],
        [$now->format('Y-m-d')],
        [$now->format('Y-m-d h:i:s')],
        ['now'],
        ['tomorrow'],
        ['2 years ago'],
    ];
});

test('anon well formed date cannot be validated', function ($date) {
    expect(fn () => (new Before)->fillParameters(['tomorrow'])->check($date))->toThrow(\Exception::class);
})->with('getInvalidDates');

dataset('getInvalidDates', function () {
    $now = new DateTime;

    return [
        [12], // 12 instead of 2012
        ['09'], // like '09 instead of 2009
        [$now->format('Y m d')],
        [$now->format('Y m d h:i:s')],
        ['tommorow'], // typo
        ['lasst year'], // typo
    ];
});

test('provided date fails validation', function () {
    $now = (new DateTime('today'))->format('Y-m-d');
    $today = 'today';

    expect((new Before)->fillParameters(['yesterday'])->check($now))->toBeFalse();

    expect((new Before)->fillParameters(['yesterday'])->check($today))->toBeFalse();
});

test('user provided param cannot be validated because it is invalid', function () {
    expect(fn () => (new Before)->fillParameters(['to,morrow'])->check('now'))->toThrow(\Exception::class);
});
