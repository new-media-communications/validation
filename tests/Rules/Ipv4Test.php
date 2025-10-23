<?php

use Rakit\Validation\Rules\Ipv4;

beforeEach(function () {
    $this->rule = new Ipv4;
});

test('valids', function () {
    expect($this->rule->check('0.0.0.0'))->toBeTrue();
    expect($this->rule->check('1.2.3.4'))->toBeTrue();
    expect($this->rule->check('255.255.255.255'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('hf02::2'))->toBeFalse();
    expect($this->rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeFalse();
});
