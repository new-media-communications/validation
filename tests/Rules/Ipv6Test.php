<?php

use Rakit\Validation\Rules\Ipv6;

test('valids', function () {
    $rule = new Ipv6;

    expect($rule->check('2001:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeTrue();
    expect($rule->check('ff02::2'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Ipv6;

    expect($rule->check('hf02::2'))->toBeFalse();
    expect($rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeFalse();
});
