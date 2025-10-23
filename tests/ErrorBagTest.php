<?php

use Nmc\Validation\ErrorBag;

test('count', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => 'foo',
            'unique' => 'bar',
        ],
        'age' => [
            'numeric' => 'baz',
            'min' => 'qux',
        ],
    ]);

    expect(4)->toEqual($errors->count());
});

test('add', function () {
    $errors = new ErrorBag;

    $errors->add('email', 'email', 'foo');
    $errors->add('email', 'unique', 'bar');
    $errors->add('age', 'numeric', 'baz');
    $errors->add('age', 'min', 'qux');

    expect([
        'email' => [
            'email' => 'foo',
            'unique' => 'bar',
        ],
        'age' => [
            'numeric' => 'baz',
            'min' => 'qux',
        ],
    ])->toEqual($errors->toArray());
});

test('has', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => 'foo',
            'unique' => 'bar',
        ],
        'items.0.id_product' => [
            'numeric' => 'qwerty',
        ],
        'items.1.id_product' => [
            'numeric' => 'qwerty',
        ],
        'items.2.id_product' => [
            'numeric' => 'qwerty',
        ],
    ]);

    expect($errors->has('email'))->toBeTrue();
    expect($errors->has('email:unique'))->toBeTrue();
    expect($errors->has('email:email'))->toBeTrue();
    expect($errors->has('items.0.*'))->toBeTrue();
    expect($errors->has('items.*.id_product'))->toBeTrue();
    expect($errors->has('items.0.*:numeric'))->toBeTrue();
    expect($errors->has('items.*.id_product:numeric'))->toBeTrue();

    expect($errors->has('not_exists'))->toBeFalse();
    expect($errors->has('email:unregistered_rule'))->toBeFalse();
    expect($errors->has('items.3.*'))->toBeFalse();
    expect($errors->has('items.*.not_exists'))->toBeFalse();
    expect($errors->has('items.0.*:unregistered_rule'))->toBeFalse();
});

test('first', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => '1',
            'unique' => '2',
        ],
        'items.0.id_product' => [
            'numeric' => '3',
        ],
        'items.1.id_product' => [
            'numeric' => '4',
        ],
        'items.2.id_product' => [
            'numeric' => '5',
        ],
    ]);

    expect('1')->toEqual($errors->first('email'));
    expect('1')->toEqual($errors->first('email:email'));
    expect('2')->toEqual($errors->first('email:unique'));

    expect('3')->toEqual($errors->first('items.*'));
    expect('3')->toEqual($errors->first('items.*.id_product'));
    expect('3')->toEqual($errors->first('items.0.*'));
    expect('3')->toEqual($errors->first('items.0.*:numeric'));
    expect('4')->toEqual($errors->first('items.1.*'));

    expect($errors->first('not_exists'))->toBeNull();
    expect($errors->first('email:unregistered_rule'))->toBeNull();
    expect($errors->first('items.99.*'))->toBeNull();
    expect($errors->first('items.*.not_exists'))->toBeNull();
    expect($errors->first('items.1.id_product:unregistered_rule'))->toBeNull();
});

test('get', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => '1',
            'unique' => '2',
        ],

        'items.0.id_product' => [
            'numeric' => '3',
            'etc' => 'x',
        ],
        'items.0.qty' => [
            'numeric' => 'a',
        ],

        'items.1.id_product' => [
            'numeric' => '4',
            'etc' => 'y',
        ],
        'items.1.qty' => [
            'numeric' => 'b',
        ],
    ]);

    expect([
        'email' => 'prefix 1 suffix',
        'unique' => 'prefix 2 suffix',
    ])->toEqual($errors->get('email', 'prefix :message suffix'));

    expect([
        'email' => 'prefix 1 suffix',
    ])->toEqual($errors->get('email:email', 'prefix :message suffix'));

    expect([
        'items.0.id_product' => [
            'numeric' => 'prefix 3 suffix',
            'etc' => 'prefix x suffix',
        ],
        'items.0.qty' => [
            'numeric' => 'prefix a suffix',
        ],
        'items.1.id_product' => [
            'numeric' => 'prefix 4 suffix',
            'etc' => 'prefix y suffix',
        ],
        'items.1.qty' => [
            'numeric' => 'prefix b suffix',
        ],
    ])->toEqual($errors->get('items.*', 'prefix :message suffix'));

    expect([
        'items.0.id_product' => [
            'numeric' => 'prefix 3 suffix',
            'etc' => 'prefix x suffix',
        ],
        'items.0.qty' => [
            'numeric' => 'prefix a suffix',
        ],
    ])->toEqual($errors->get('items.0.*', 'prefix :message suffix'));

    expect([
        'items.0.id_product' => [
            'numeric' => 'prefix 3 suffix',
            'etc' => 'prefix x suffix',
        ],
        'items.1.id_product' => [
            'numeric' => 'prefix 4 suffix',
            'etc' => 'prefix y suffix',
        ],
    ])->toEqual($errors->get('items.*.id_product', 'prefix :message suffix'));

    expect([
        'items.0.id_product' => [
            'etc' => 'prefix x suffix',
        ],
        'items.1.id_product' => [
            'etc' => 'prefix y suffix',
        ],
    ])->toEqual($errors->get('items.*.id_product:etc', 'prefix :message suffix'));

    expect([
        'items.0.id_product' => [
            'etc' => 'prefix x suffix',
        ],
        'items.1.id_product' => [
            'etc' => 'prefix y suffix',
        ],
    ])->toEqual($errors->get('items.*:etc', 'prefix :message suffix'));
});

test('all', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => '1',
            'unique' => '2',
        ],
        'items.0.id_product' => [
            'numeric' => '3',
            'etc' => 'x',
        ],
        'items.0.qty' => [
            'numeric' => 'a',
        ],
        'items.1.id_product' => [
            'numeric' => '4',
            'etc' => 'y',
        ],
        'items.1.qty' => [
            'numeric' => 'b',
        ],
    ]);

    expect([
        'prefix 1 suffix',
        'prefix 2 suffix',

        'prefix 3 suffix',
        'prefix x suffix',
        'prefix a suffix',

        'prefix 4 suffix',
        'prefix y suffix',
        'prefix b suffix',
    ])->toEqual($errors->all('prefix :message suffix'));
});

test('first of all', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => '1',
            'unique' => '2',
        ],
        'items.0.id_product' => [
            'numeric' => '3',
            'etc' => 'x',
        ],
        'items.0.qty' => [
            'numeric' => 'a',
        ],
        'items.1.id_product' => [
            'numeric' => '4',
            'etc' => 'y',
        ],
        'items.1.qty' => [
            'numeric' => 'b',
        ],
    ]);

    expect([
        'email' => 'prefix 1 suffix',
        'items' => [
            [
                'id_product' => 'prefix 3 suffix',
                'qty' => 'prefix a suffix',
            ],
            [
                'id_product' => 'prefix 4 suffix',
                'qty' => 'prefix b suffix',
            ],
        ],
    ])->toEqual($errors->firstOfAll('prefix :message suffix'));
});

test('first of all dot notation', function () {
    $errors = new ErrorBag([
        'email' => [
            'email' => '1',
            'unique' => '2',
        ],
        'items.0.id_product' => [
            'numeric' => '3',
            'etc' => 'x',
        ],
        'items.0.qty' => [
            'numeric' => 'a',
        ],
        'items.1.id_product' => [
            'numeric' => '4',
            'etc' => 'y',
        ],
        'items.1.qty' => [
            'numeric' => 'b',
        ],
    ]);

    expect([
        'email' => 'prefix 1 suffix',
        'items.0.id_product' => 'prefix 3 suffix',
        'items.0.qty' => 'prefix a suffix',
        'items.1.id_product' => 'prefix 4 suffix',
        'items.1.qty' => 'prefix b suffix',
    ])->toEqual($errors->firstOfAll('prefix :message suffix', true));
});
