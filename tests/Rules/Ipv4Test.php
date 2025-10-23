<?php

use Rakit\Validation\Rules\Ipv4;

test('valids', function () {
    $rule = new Ipv4;

    expect($rule->check('0.0.0.0'))->toBeTrue();
    expect($rule->check('1.2.3.4'))->toBeTrue();
    expect($rule->check('255.255.255.255'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Ipv4;

    expect($rule->check('hf02::2'))->toBeFalse();
    expect($rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeFalse();
});
