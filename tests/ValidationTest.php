<?php

use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

test('parse rule', function ($rules, $expectedResult) {
    $class = new ReflectionClass(Validation::class);
    $method = $class->getMethod('parseRule');
    $method->setAccessible(true);

    $validation = new Validation(new Validator(), [], []);

    $result = $method->invokeArgs($validation, [$rules]);
    expect($result)->toBe($expectedResult);
})->with('parseRuleProvider');

/**
 * @return array
 */
dataset('parseRuleProvider', function () {
    return [
        [
            'email',
            [
                'email',
                [],
            ],
        ],
        [
            'min:6',
            [
                'min',
                ['6'],
            ],
        ],
        [
            'uploaded_file:0,500K,png,jpeg',
            [
                'uploaded_file',
                ['0', '500K', 'png', 'jpeg'],
            ],
        ],
        [
            'same:password',
            [
                'same',
                ['password'],
            ],
        ],
        [
            'regex:/^([a-zA-Z\,]*)$/',
            [
                'regex',
                ['/^([a-zA-Z\,]*)$/'],
            ],
        ],
    ];
});
