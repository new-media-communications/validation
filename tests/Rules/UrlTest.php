<?php

use Rakit\Validation\Rules\Url;

test('valids', function () {
    $rule = new Url;

    // Without specific schemes
    expect($rule->check('ftp://foobar.com'))->toBeTrue();
    expect($rule->check('any://foobar.com'))->toBeTrue();
    expect($rule->check('http://foobar.com'))->toBeTrue();
    expect($rule->check('https://foobar.com'))->toBeTrue();
    expect($rule->check('https://foobar.com/path?a=123&b=blah'))->toBeTrue();

    // Using specific schemes
    expect($rule->fillParameters(['ftp'])->check('ftp://foobar.com'))->toBeTrue();
    expect($rule->fillParameters(['any'])->check('any://foobar.com'))->toBeTrue();
    expect($rule->fillParameters(['http'])->check('http://foobar.com'))->toBeTrue();
    expect($rule->fillParameters(['https'])->check('https://foobar.com'))->toBeTrue();
    expect($rule->fillParameters(['http', 'https'])->check('https://foobar.com'))->toBeTrue();
    expect($rule->fillParameters(['foo', 'bar'])->check('bar://foobar.com'))->toBeTrue();
    expect($rule->fillParameters(['mailto'])->check('mailto:johndoe@gmail.com'))->toBeTrue();
    expect($rule->fillParameters(['jdbc'])->check('jdbc:mysql://localhost/dbname'))->toBeTrue();

    // Using forScheme
    expect($rule->forScheme('ftp')->check('ftp://foobar.com'))->toBeTrue();
    expect($rule->forScheme('http')->check('http://foobar.com'))->toBeTrue();
    expect($rule->forScheme('https')->check('https://foobar.com'))->toBeTrue();
    expect($rule->forScheme(['http', 'https'])->check('https://foobar.com'))->toBeTrue();
    expect($rule->forScheme('mailto')->check('mailto:johndoe@gmail.com'))->toBeTrue();
    expect($rule->forScheme('jdbc')->check('jdbc:mysql://localhost/dbname'))->toBeTrue();
});

test('invalids', function () {
    $rule = new Url;

    expect($rule->check('foo:'))->toBeFalse();
    expect($rule->check('mailto:johndoe@gmail.com'))->toBeFalse();
    expect($rule->forScheme('mailto')->check('http://www.foobar.com'))->toBeFalse();
    expect($rule->forScheme('ftp')->check('http://www.foobar.com'))->toBeFalse();
    expect($rule->forScheme('jdbc')->check('http://www.foobar.com'))->toBeFalse();
    expect($rule->forScheme(['http', 'https'])->check('any://www.foobar.com'))->toBeFalse();
});
