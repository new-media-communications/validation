<?php

use Rakit\Validation\Rules\Ipv6;

beforeEach(function () {
    $this->rule = new Ipv6;
});

test('valids', function () {
    expect($this->rule->check('2001:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeTrue();
    expect($this->rule->check('ff02::2'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('hf02::2'))->toBeFalse();
    expect($this->rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'))->toBeFalse();
});
