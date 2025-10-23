<?php

use Rakit\Validation\Rules\After;

beforeEach(function () {
    $this->validator = new After();
});

test('only awell formed date can be validated', function ($date) {
    expect($this->validator->fillParameters(["3 years ago"])->check($date))->toBeTrue();
})->with('getValidDates');

test('anon well formed date cannot be validated', function ($date) {
    expect(fn () => $this->validator->fillParameters(["tomorrow"])->check($date))->toThrow(\Exception::class);
})->with('getInvalidDates');

test('user provided param cannot be validated because it is invalid', function () {
    
    expect(fn () => $this->validator->fillParameters(["to,morrow"])->check("now"))->toThrow(\Exception::class);
});

dataset('getInvalidDates', function () {
    $now = new DateTime();

    return [
        [12], //12 instead of 2012
        ["09"], //like '09 instead of 2009
        [$now->format("Y m d")],
        [$now->format("Y m d h:i:s")],
        ["tommorow"], //typo
        ["lasst year"] //typo
    ];
});

dataset('getValidDates', function () {
    $now = new DateTime();

    return [
        [2016],
        [$now->format("Y-m-d")],
        [$now->format("Y-m-d h:i:s")],
        ["now"],
        ["tomorrow"],
        ["2 years ago"]
    ];
});

test('provided date fails validation', function () {
    $now = (new DateTime("today"))->format("Y-m-d");
    $today = "today";

    expect($this->validator->fillParameters(['tomorrow'])->check($now))->toBeFalse();

    expect($this->validator->fillParameters(['tomorrow'])->check($today))->toBeFalse();
});