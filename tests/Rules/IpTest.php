<?php

use Rakit\Validation\Rules\Ip;

beforeEach(function () {
    $this->rule = new Ip;
});

test('valids', function () {
    expect($this->rule->check('1.2.3.4'))->toBeTrue();
    expect($this->rule->check('255.255.255.255'))->toBeTrue();
    expect($this->rule->check('ff02::2'))->toBeTrue();
    expect($this->rule->check('2001:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('1.2.3.4.5'))->toBeFalse();
    expect($this->rule->check('256.255.255.255'))->toBeFalse();
    expect($this->rule->check('hf02::2'))->toBeFalse();
    expect($this->rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeFalse();
});
