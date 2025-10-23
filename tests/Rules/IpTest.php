<?php

use Rakit\Validation\Rules\Ip;

test('valids', function () {
    $rule = new Ip;

    expect($rule->check('1.2.3.4'))->toBeTrue();
    expect($rule->check('255.255.255.255'))->toBeTrue();
    expect($rule->check('ff02::2'))->toBeTrue();
    expect($rule->check('2001:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Ip;

    expect($rule->check('1.2.3.4.5'))->toBeFalse();
    expect($rule->check('256.255.255.255'))->toBeFalse();
    expect($rule->check('hf02::2'))->toBeFalse();
    expect($rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeFalse();
});
