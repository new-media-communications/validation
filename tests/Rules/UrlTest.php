<?php

use Rakit\Validation\Rules\Url;

beforeEach(function () {
    $this->rule = new Url;
});

test('valids', function () {
    // Without specific schemes
    expect($this->rule->check('ftp://foobar.com'))->toBeTrue();
    expect($this->rule->check('any://foobar.com'))->toBeTrue();
    expect($this->rule->check('http://foobar.com'))->toBeTrue();
    expect($this->rule->check('https://foobar.com'))->toBeTrue();
    expect($this->rule->check('https://foobar.com/path?a=123&b=blah'))->toBeTrue();

    // Using specific schemes
    expect($this->rule->fillParameters(['ftp'])->check('ftp://foobar.com'))->toBeTrue();
    expect($this->rule->fillParameters(['any'])->check('any://foobar.com'))->toBeTrue();
    expect($this->rule->fillParameters(['http'])->check('http://foobar.com'))->toBeTrue();
    expect($this->rule->fillParameters(['https'])->check('https://foobar.com'))->toBeTrue();
    expect($this->rule->fillParameters(['http', 'https'])->check('https://foobar.com'))->toBeTrue();
    expect($this->rule->fillParameters(['foo', 'bar'])->check('bar://foobar.com'))->toBeTrue();
    expect($this->rule->fillParameters(['mailto'])->check('mailto:johndoe@gmail.com'))->toBeTrue();
    expect($this->rule->fillParameters(['jdbc'])->check('jdbc:mysql://localhost/dbname'))->toBeTrue();

    // Using forScheme
    expect($this->rule->forScheme('ftp')->check('ftp://foobar.com'))->toBeTrue();
    expect($this->rule->forScheme('http')->check('http://foobar.com'))->toBeTrue();
    expect($this->rule->forScheme('https')->check('https://foobar.com'))->toBeTrue();
    expect($this->rule->forScheme(['http', 'https'])->check('https://foobar.com'))->toBeTrue();
    expect($this->rule->forScheme('mailto')->check('mailto:johndoe@gmail.com'))->toBeTrue();
    expect($this->rule->forScheme('jdbc')->check('jdbc:mysql://localhost/dbname'))->toBeTrue();
});

test('invalids', function () {
    expect($this->rule->check('foo:'))->toBeFalse();
    expect($this->rule->check('mailto:johndoe@gmail.com'))->toBeFalse();
    expect($this->rule->forScheme('mailto')->check('http://www.foobar.com'))->toBeFalse();
    expect($this->rule->forScheme('ftp')->check('http://www.foobar.com'))->toBeFalse();
    expect($this->rule->forScheme('jdbc')->check('http://www.foobar.com'))->toBeFalse();
    expect($this->rule->forScheme(['http', 'https'])->check('any://www.foobar.com'))->toBeFalse();
});
