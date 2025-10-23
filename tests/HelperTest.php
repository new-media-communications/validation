<?php

use Rakit\Validation\Helper;

test('array has', function () {
    $array = [
        'foo' => [
            'bar' => [
                'baz' => null
            ]
        ],
        'one.two.three' => null
    ];

    expect(Helper::arrayHas($array, 'foo'))->toBeTrue();
    expect(Helper::arrayHas($array, 'foo.bar'))->toBeTrue();
    expect(Helper::arrayHas($array, 'foo.bar.baz'))->toBeTrue();
    expect(Helper::arrayHas($array, 'one.two.three'))->toBeTrue();

    expect(Helper::arrayHas($array, 'foo.baz'))->toBeFalse();
    expect(Helper::arrayHas($array, 'bar.baz'))->toBeFalse();
    expect(Helper::arrayHas($array, 'foo.bar.qux'))->toBeFalse();
    expect(Helper::arrayHas($array, 'one.two'))->toBeFalse();
});

test('array get', function () {
    $array = [
        'foo' => [
            'bar' => [
                'baz' => 'abc'
            ]
        ],
        'one.two.three' => 123
    ];

    expect($array['foo'])->toEqual(Helper::arrayGet($array, 'foo'));
    expect($array['foo']['bar'])->toEqual(Helper::arrayGet($array, 'foo.bar'));
    expect($array['foo']['bar']['baz'])->toEqual(Helper::arrayGet($array, 'foo.bar.baz'));
    expect(123)->toEqual(Helper::arrayGet($array, 'one.two.three'));

    expect(Helper::arrayGet($array, 'foo.bar.baz.qux'))->toBeNull();
    expect(Helper::arrayGet($array, 'one.two'))->toBeNull();
});

test('array dot', function () {
    $array = [
        'foo' => [
            'bar' => [
                'baz' => 123,
                'qux' => 456
            ]
        ],
        'comments' => [
            ['id' => 1, 'text' => 'foo'],
            ['id' => 2, 'text' => 'bar'],
            ['id' => 3, 'text' => 'baz'],
        ],
        'one.two.three' => 789
    ];

    expect([
        'foo.bar.baz' => 123,
        'foo.bar.qux' => 456,
        'comments.0.id' => 1,
        'comments.0.text' => 'foo',
        'comments.1.id' => 2,
        'comments.1.text' => 'bar',
        'comments.2.id' => 3,
        'comments.2.text' => 'baz',
        'one.two.three' => 789
    ])->toEqual(Helper::arrayDot($array));
});

test('array set', function () {
    $array = [
        'comments' => [
            ['text' => 'foo'],
            ['id' => 2, 'text' => 'bar'],
            ['id' => 3, 'text' => 'baz'],
        ]
    ];

    Helper::arraySet($array, 'comments.*.id', null, false);
    Helper::arraySet($array, 'comments.*.x.y', 1, false);

    expect([
        'comments' => [
            ['id' => null, 'text' => 'foo', 'x' => ['y' => 1]],
            ['id' => 2, 'text' => 'bar', 'x' => ['y' => 1]],
            ['id' => 3, 'text' => 'baz', 'x' => ['y' => 1]],
        ]
    ])->toEqual($array);
});

test('array unset', function () {
    $array = [
        'users' => [
            'one' => 'user_one',
            'two' => 'user_two',
        ],
        'stuffs' => [1, 'two', ['three'], null, false, true],
        'message' => "lorem ipsum",
    ];

    Helper::arrayUnset($array, 'users.one');
    expect([
        'users' => [
            'two' => 'user_two',
        ],
        'stuffs' => [1, 'two', ['three'], null, false, true],
        'message' => "lorem ipsum",
    ])->toEqual($array);

    Helper::arrayUnset($array, 'stuffs.*');
    expect([
        'users' => [
            'two' => 'user_two',
        ],
        'stuffs' => [],
        'message' => "lorem ipsum",
    ])->toEqual($array);
});

test('join', function () {
    $pieces0 = [];
    $pieces1 = [1];
    $pieces2 = [1, 2];
    $pieces3 = [1, 2, 3];

    $separator = ', ';
    $lastSeparator = ', and ';

    expect('')->toEqual(Helper::join($pieces0, $separator, $lastSeparator));
    expect('1')->toEqual(Helper::join($pieces1, $separator, $lastSeparator));
    expect('1, and 2')->toEqual(Helper::join($pieces2, $separator, $lastSeparator));
    expect('1, 2, and 3')->toEqual(Helper::join($pieces3, $separator, $lastSeparator));
});

test('wraps', function () {
    $inputs = [1, 2, 3];

    expect(['-1-', '-2-', '-3-'])->toEqual(Helper::wraps($inputs, '-'));
    expect(['-1+', '-2+', '-3+'])->toEqual(Helper::wraps($inputs, '-', '+'));
});
