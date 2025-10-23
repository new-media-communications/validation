<?php

use Nmc\Validation\Rule;
use Nmc\Validation\RuleNotFoundException;
use Nmc\Validation\RuleQuashException;
use Nmc\Validation\Rules\UploadedFile;
use Nmc\Validation\Validator;
use Tests\Even;
use Tests\Required;

test('passes', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'email' => 'emsifa@gmail.com',
    ], [
        'email' => 'required|email',
    ]);

    expect($validation->passes())->toBeTrue();

    $validation = $validator->validate([], [
        'email' => 'required|email',
    ]);

    expect($validation->passes())->toBeFalse();
});

test('fails', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'email' => 'emsifa@gmail.com',
    ], [
        'email' => 'required|email',
    ]);

    expect($validation->fails())->toBeFalse();

    $validation = $validator->validate([], [
        'email' => 'required|email',
    ]);

    expect($validation->fails())->toBeTrue();
});

test('skip empty rule', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'email' => 'emsifa@gmail.com',
    ], [
        'email' => [
            null,
            'email',
        ],
    ]);

    expect($validation->passes())->toBeTrue();
});

test('required uploaded file', function () {
    $empty_file = [
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_NO_FILE,
    ];

    $validator = new Validator;

    $validation = $validator->validate([
        'file' => $empty_file,
    ], [
        'file' => 'required|uploaded_file',
    ]);

    $errors = $validation->errors();
    expect($validation->passes())->toBeFalse();
    expect($errors->first('file:required'))->not->toBeNull();
});

test('optional uploaded file', function () {
    $emptyFile = [
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_NO_FILE,
    ];

    $validator = new Validator;

    $validation = $validator->validate([
        'file' => $emptyFile,
    ], [
        'file' => 'uploaded_file',
    ]);
    expect($validation->passes())->toBeTrue();
});

test('missing key uploaded file', function ($uploadedFile) {
    $validator = new Validator;

    $validation = $validator->validate([
        'file' => $uploadedFile,
    ], [
        'file' => 'required|uploaded_file',
    ]);

    $errors = $validation->errors();
    expect($validation->passes())->toBeFalse();
    expect($errors->first('file:required'))->not->toBeNull();
})->with(function () {
    $validUploadedFile = [
        'name' => 'foo',
        'type' => 'text/plain',
        'size' => 1000,
        'tmp_name' => __FILE__,
        'error' => UPLOAD_ERR_OK,
    ];

    $samples = [];
    foreach ($validUploadedFile as $key => $value) {
        $uploadedFile = $validUploadedFile;
        unset($uploadedFile[$key]);
        $samples[] = [$uploadedFile];
    }

    return $samples;
});

test('validation should correctly resolve multiple file uploads', function () {
    // Test from input files:
    // <input type="file" name="photos[]"/>
    // <input type="file" name="photos[]"/>
    $sampleInputFiles = [
        'photos' => [
            'name' => [
                'a.png',
                'b.jpeg',
            ],
            'type' => [
                'image/png',
                'image/jpeg',
            ],
            'size' => [
                1000,
                2000,
            ],
            'tmp_name' => [
                __DIR__.'/a.png',
                __DIR__.'/b.jpeg',
            ],
            'error' => [
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK,
            ],
        ],
    ];

    $uploadedFileRule = getMockedUploadedFileRule()->fileTypes('jpeg');
    $validator = new Validator;

    $validation = $validator->validate($sampleInputFiles, [
        'photos.*' => ['required', $uploadedFileRule],
    ]);

    expect($validation->passes())->toBeFalse();
    expect([
        'photos' => [
            1 => [
                'name' => 'b.jpeg',
                'type' => 'image/jpeg',
                'size' => 2000,
                'tmp_name' => __DIR__.'/b.jpeg',
                'error' => UPLOAD_ERR_OK,
            ],
        ],
    ])->toEqual($validation->getValidData());
    expect([
        'photos' => [
            0 => [
                'name' => 'a.png',
                'type' => 'image/png',
                'size' => 1000,
                'tmp_name' => __DIR__.'/a.png',
                'error' => UPLOAD_ERR_OK,
            ],
        ],
    ])->toEqual($validation->getInvalidData());
});

test('validation should correctly resolve assoc file uploads', function () {
    // Test from input files:
    // <input type="file" name="photos[foo]"/>
    // <input type="file" name="photos[bar]"/>
    $sampleInputFiles = [
        'photos' => [
            'name' => [
                'foo' => 'a.png',
                'bar' => 'b.jpeg',
            ],
            'type' => [
                'foo' => 'image/png',
                'bar' => 'image/jpeg',
            ],
            'size' => [
                'foo' => 1000,
                'bar' => 2000,
            ],
            'tmp_name' => [
                'foo' => __DIR__.'/a.png',
                'bar' => __DIR__.'/b.jpeg',
            ],
            'error' => [
                'foo' => UPLOAD_ERR_OK,
                'bar' => UPLOAD_ERR_OK,
            ],
        ],
    ];

    $uploadedFileRule = getMockedUploadedFileRule()->fileTypes('jpeg');
    $validator = new Validator;

    $validation = $validator->validate($sampleInputFiles, [
        'photos.foo' => ['required', clone $uploadedFileRule],
        'photos.bar' => ['required', clone $uploadedFileRule],
    ]);

    expect($validation->passes())->toBeFalse();
    expect([
        'photos' => [
            'bar' => [
                'name' => 'b.jpeg',
                'type' => 'image/jpeg',
                'size' => 2000,
                'tmp_name' => __DIR__.'/b.jpeg',
                'error' => UPLOAD_ERR_OK,
            ],
        ],
    ])->toEqual($validation->getValidData());
    expect([
        'photos' => [
            'foo' => [
                'name' => 'a.png',
                'type' => 'image/png',
                'size' => 1000,
                'tmp_name' => __DIR__.'/a.png',
                'error' => UPLOAD_ERR_OK,
            ],
        ],
    ])->toEqual($validation->getInvalidData());
});

test('validation should correctly resolve complex file uploads', function () {
    // Test from input files:
    // <input type="file" name="files[foo][bar][baz]"/>
    // <input type="file" name="files[foo][bar][qux]"/>
    // <input type="file" name="files[photos][]"/>
    // <input type="file" name="files[photos][]"/>
    $sampleInputFiles = [
        'files' => [
            'name' => [
                'foo' => [
                    'bar' => [
                        'baz' => 'foo-bar-baz.jpeg',
                        'qux' => 'foo-bar-qux.png',
                    ],
                ],
                'photos' => [
                    'photos-0.png',
                    'photos-1.jpeg',
                ],
            ],
            'type' => [
                'foo' => [
                    'bar' => [
                        'baz' => 'image/jpeg',
                        'qux' => 'image/png',
                    ],
                ],
                'photos' => [
                    'image/png',
                    'image/jpeg',
                ],
            ],
            'size' => [
                'foo' => [
                    'bar' => [
                        'baz' => 500,
                        'qux' => 750,
                    ],
                ],
                'photos' => [
                    1000,
                    2000,
                ],
            ],
            'tmp_name' => [
                'foo' => [
                    'bar' => [
                        'baz' => __DIR__.'/foo-bar-baz.jpeg',
                        'qux' => __DIR__.'/foo-bar-qux.png',
                    ],
                ],
                'photos' => [
                    __DIR__.'/photos-0.png',
                    __DIR__.'/photos-1.jpeg',
                ],
            ],
            'error' => [
                'foo' => [
                    'bar' => [
                        'baz' => UPLOAD_ERR_OK,
                        'qux' => UPLOAD_ERR_OK,
                    ],
                ],
                'photos' => [
                    UPLOAD_ERR_OK,
                    UPLOAD_ERR_OK,
                ],
            ],
        ],
    ];

    $uploadedFileRule = getMockedUploadedFileRule()->fileTypes('jpeg');

    $validator = new Validator;

    $validation = $validator->validate($sampleInputFiles, [
        'files.foo.bar.baz' => ['required', clone $uploadedFileRule],
        'files.foo.bar.qux' => ['required', clone $uploadedFileRule],
        'files.photos.*' => ['required', clone $uploadedFileRule],
    ]);

    expect($validation->passes())->toBeFalse();
    expect([
        'files' => [
            'foo' => [
                'bar' => [
                    'baz' => [
                        'name' => 'foo-bar-baz.jpeg',
                        'type' => 'image/jpeg',
                        'size' => 500,
                        'tmp_name' => __DIR__.'/foo-bar-baz.jpeg',
                        'error' => UPLOAD_ERR_OK,
                    ],
                ],
            ],
            'photos' => [
                1 => [
                    'name' => 'photos-1.jpeg',
                    'type' => 'image/jpeg',
                    'size' => 2000,
                    'tmp_name' => __DIR__.'/photos-1.jpeg',
                    'error' => UPLOAD_ERR_OK,
                ],
            ],
        ],
    ])->toEqual($validation->getValidData());
    expect([
        'files' => [
            'foo' => [
                'bar' => [
                    'qux' => [
                        'name' => 'foo-bar-qux.png',
                        'type' => 'image/png',
                        'size' => 750,
                        'tmp_name' => __DIR__.'/foo-bar-qux.png',
                        'error' => UPLOAD_ERR_OK,
                    ],
                ],
            ],
            'photos' => [
                0 => [
                    'name' => 'photos-0.png',
                    'type' => 'image/png',
                    'size' => 1000,
                    'tmp_name' => __DIR__.'/photos-0.png',
                    'error' => UPLOAD_ERR_OK,
                ],
            ],
        ],
    ])->toEqual($validation->getInvalidData());
});

function getMockedUploadedFileRule()
{
    $rule = test()->getMockBuilder(UploadedFile::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $rule->method('isUploadedFile')->willReturn(true);

    return $rule;
}

test('required if rule', function () {
    $validator = new Validator;

    $v1 = $validator->validate([
        'a' => '',
        'b' => '',
    ], [
        'b' => 'required_if:a,1',
    ]);

    expect($v1->passes())->toBeTrue();

    $v2 = $validator->validate([
        'a' => '1',
        'b' => '',
    ], [
        'b' => 'required_if:a,1',
    ]);

    expect($v2->passes())->toBeFalse();
});

test('required unless rule', function () {
    $validator = new Validator;

    $v1 = $validator->validate([
        'a' => '',
        'b' => '',
    ], [
        'b' => 'required_unless:a,1',
    ]);

    expect($v1->passes())->toBeFalse();

    $v2 = $validator->validate([
        'a' => '1',
        'b' => '',
    ], [
        'b' => 'required_unless:a,1',
    ]);

    expect($v2->passes())->toBeTrue();
});

test('required with rule', function () {
    $validator = new Validator;

    $v1 = $validator->validate([
        'b' => '',
    ], [
        'b' => 'required_with:a',
    ]);

    expect($v1->passes())->toBeTrue();

    $v2 = $validator->validate([
        'a' => '1',
        'b' => '',
    ], [
        'b' => 'required_with:a',
    ]);

    expect($v2->passes())->toBeFalse();
});

test('required without rule', function () {
    $validator = new Validator;

    $v1 = $validator->validate([
        'b' => '',
    ], [
        'b' => 'required_without:a',
    ]);

    expect($v1->passes())->toBeFalse();

    $v2 = $validator->validate([
        'a' => '1',
        'b' => '',
    ], [
        'b' => 'required_without:a',
    ]);

    expect($v2->passes())->toBeTrue();
});

test('required with all rule', function () {
    $validator = new Validator;

    $v1 = $validator->validate([
        'b' => '',
        'a' => '1',
    ], [
        'b' => 'required_with_all:a,c',
    ]);

    expect($v1->passes())->toBeTrue();

    $v2 = $validator->validate([
        'a' => '1',
        'b' => '',
        'c' => '2',
    ], [
        'b' => 'required_with_all:a,c',
    ]);

    expect($v2->passes())->toBeFalse();
});

test('required without all rule', function () {
    $validator = new Validator;

    $v1 = $validator->validate([
        'b' => '',
        'a' => '1',
    ], [
        'b' => 'required_without_all:a,c',
    ]);

    expect($v1->passes())->toBeTrue();

    $v2 = $validator->validate([
        'b' => '',
    ], [
        'b' => 'required_without_all:a,c',
    ]);

    expect($v2->passes())->toBeFalse();
});

test('rule present', function () {
    $validator = new Validator;

    $v1 = $validator->validate([], [
        'something' => 'present',
    ]);
    expect($v1->passes())->toBeFalse();

    $v2 = $validator->validate([
        'something' => 10,
    ], [
        'something' => 'present',
    ]);
    expect($v2->passes())->toBeTrue();
});

test('non existent validation rule', function () {
    $validator = new Validator;

    $validation = $validator->make([
        'name' => 'some name',
    ], [
        'name' => 'required|xxx',
    ], [
        'name.required' => 'Fill in your name',
        'xxx' => 'Oops',
    ]);
})->throws(RuleNotFoundException::class);

test('before rule', function () {
    $validator = new Validator;

    $data = ['date' => (new DateTime)->format('Y-m-d')];

    $validation = $validator->make($data, [
        'date' => 'required|before:tomorrow',
    ], []);

    $validation->validate();

    expect($validation->passes())->toBeTrue();

    $validator2 = $validator->make($data, [
        'date' => 'required|before:last week',
    ], []);

    $validator2->validate();

    expect($validator2->passes())->toBeFalse();
});

test('after rule', function () {
    $validator = new Validator;

    $data = ['date' => (new DateTime)->format('Y-m-d')];

    $validation = $validator->make($data, [
        'date' => 'required|after:yesterday',
    ], []);

    $validation->validate();

    expect($validation->passes())->toBeTrue();

    $validator2 = $validator->make($data, [
        'date' => 'required|after:next year',
    ], []);

    $validator2->validate();

    expect($validator2->passes())->toBeFalse();
});

test('new validation rule can be added', function () {
    $validator = new Validator;

    $validator->addValidator('even', new Even);

    $data = [4, 6, 8, 10];

    $validation = $validator->make($data, ['s' => 'even'], []);

    $validation->validate();

    expect($validation->passes())->toBeTrue();
});

test('internal validation rule cannot be overridden', function () {
    $validator = new Validator;

    $validator->addValidator('required', new Required);

    $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

    $validation = $validator->make($data, ['s' => 'required'], []);

    $validation->validate();

})->throws(RuleQuashException::class);

test('internal validation rule can be overridden', function () {
    $validator = new Validator;

    $validator->allowRuleOverride(true);

    // This is a custom rule defined in the fixtures directory
    $validator->addValidator('required', new Required);

    $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

    $validation = $validator->make($data, ['s' => 'required'], []);

    $validation->validate();

    expect($validation->passes())->toBeTrue();
});

test('ignore next rules when implicit rules fails', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'some_value' => 1,
    ], [
        'required_field' => 'required|numeric|min:6',
        'required_if_field' => 'required_if:some_value,1|numeric|min:6',
        'must_present_field' => 'present|numeric|min:6',
        'must_accepted_field' => 'accepted|numeric|min:6',
    ]);

    $errors = $validation->errors();

    expect(4)->toEqual($errors->count());

    expect($errors->first('required_field:required'))->not->toBeNull();
    expect($errors->first('required_field:numeric'))->toBeNull();
    expect($errors->first('required_field:min'))->toBeNull();

    expect($errors->first('required_if_field:required_if'))->not->toBeNull();
    expect($errors->first('required_if_field:numeric'))->toBeNull();
    expect($errors->first('required_if_field:min'))->toBeNull();

    expect($errors->first('must_present_field:present'))->not->toBeNull();
    expect($errors->first('must_present_field:numeric'))->toBeNull();
    expect($errors->first('must_present_field:min'))->toBeNull();

    expect($errors->first('must_accepted_field:accepted'))->not->toBeNull();
    expect($errors->first('must_accepted_field:numeric'))->toBeNull();
    expect($errors->first('must_accepted_field:min'))->toBeNull();
});

test('next rules applied when empty value with present', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'must_present_field' => '',
    ], [
        'must_present_field' => 'present|array',
    ]);

    $errors = $validation->errors();

    expect(1)->toEqual($errors->count());

    expect($errors->first('must_present_field:present'))->toBeNull();
    expect($errors->first('must_present_field:array'))->not->toBeNull();
});

test('ignore other rules when attribute is not required', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'an_empty_file' => [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
        ],
        'required_if_field' => null,
    ], [
        'optional_field' => 'ipv4|in:127.0.0.1',
        'required_if_field' => 'required_if:some_value,1|email',
        'an_empty_file' => 'uploaded_file',
    ]);

    expect($validation->passes())->toBeTrue();
});

test('dont ignore other rules when value is not empty', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'an_error_file' => [
            'name' => 'foo',
            'type' => 'text/plain',
            'size' => 10000,
            'tmp_name' => '/tmp/foo',
            'error' => UPLOAD_ERR_CANT_WRITE,
        ],
        'optional_field' => 'invalid ip address',
        'required_if_field' => 'invalid email',
    ], [
        'an_error_file' => 'uploaded_file',
        'optional_field' => 'ipv4|in:127.0.0.1',
        'required_if_field' => 'required_if:some_value,1|email',
    ]);

    expect(4)->toEqual($validation->errors()->count());
});

test('dont ignore other rules when attribute is required', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'optional_field' => 'have a value',
        'required_if_field' => 'invalid email',
        'some_value' => 1,
    ], [
        'optional_field' => 'required|ipv4|in:127.0.0.1',
        'required_if_field' => 'required_if:some_value,1|email',
    ]);

    $errors = $validation->errors();

    expect(3)->toEqual($errors->count());
    expect($errors->first('optional_field:ipv4'))->not->toBeNull();
    expect($errors->first('optional_field:in'))->not->toBeNull();
    expect($errors->first('required_if_field:email'))->not->toBeNull();
});

test('register rules using invokes', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'a_field' => null,
        'a_number' => 1000,
        'a_same_number' => 1000,
        'a_date' => '2016-12-06',
        'a_file' => [
            'name' => 'foo',
            'type' => 'text/plain',
            'size' => 10000,
            'tmp_name' => '/tmp/foo',
            'error' => UPLOAD_ERR_OK,
        ],
    ], [
        'a_field' => [
            $validator('required')->message('1'),
        ],
        'a_number' => [
            $validator('min', 2000)->message('2'),
            $validator('max', 5)->message('3'),
            $validator('between', 1, 5)->message('4'),
            $validator('in', [1, 2, 3, 4, 5])->message('5'),
            $validator('not_in', [1000, 2, 3, 4, 5])->message('6'),
            $validator('same', 'a_date')->message('7'),
            $validator('different', 'a_same_number')->message('8'),
        ],
        'a_date' => [
            $validator('date', 'd-m-Y')->message('9'),
        ],
        'a_file' => [
            $validator('uploaded_file', 20000)->message('10'),
        ],
    ]);

    $errors = $validation->errors();
    expect('1,2,3,4,5,6,7,8,9,10')->toEqual(implode(',', $errors->all()));
});

test('array assoc validation', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'user' => [
            'email' => 'invalid email',
            'name' => 'John Doe',
            'age' => 16,
        ],
    ], [
        'user.email' => 'required|email',
        'user.name' => 'required',
        'user.age' => 'required|min:18',
    ]);

    $errors = $validation->errors();

    expect(2)->toEqual($errors->count());

    expect($errors->first('user.email:email'))->not->toBeNull();
    expect($errors->first('user.age:min'))->not->toBeNull();
    expect($errors->first('user.name:required'))->toBeNull();
});

test('empty array assoc validation', function () {
    $validator = new Validator;

    $validation = $validator->validate([], [
        'user' => 'required',
        'user.email' => 'email',
    ]);

    expect($validation->passes())->toBeFalse();
});

test('root asterisk validation', function (array $data, array $rules, $errors = null) {
    $validator = new Validator;

    $validation = $validator->validate($data, $rules);
    expect($validation->passes())->toBe(empty($errors));
    $errorBag = $validation->errors();
    if (! empty($errors)) {
        foreach ($errors as $error) {
            $field = $error[0];
            $rule = $error[1] ?? null;
            $error = $errorBag->get($field);
            expect($error)->not->toBeEmpty();
            if ($rule !== null) {
                expect($error)->toHaveKey($rule);
            }
        }
    }
})->with('rootAsteriskProvider');

dataset('rootAsteriskProvider', function () {
    return [
        'control sample success' => [
            ['Body' => ['a' => 1, 'b' => 2]],
            ['Body.*' => 'integer|min:0'],
        ],
        'control sample failure' => [
            ['Body' => ['a' => 1, 'b' => -2]],
            ['Body.*' => 'integer|min:0'],
            [['Body.b', 'min']],
        ],
        'root field success' => [
            ['a' => 1, 'b' => 2],
            ['*' => 'integer|min:0'],
        ],
        'root field failure' => [
            ['a' => 1, 'b' => -2],
            ['*' => 'integer|min:0'],
            [['b', 'min']],
        ],
        'root array success' => [
            [[1], [2]],
            ['*.*' => 'integer|min:0'],
        ],
        'root array failure' => [
            [[1], [-2]],
            ['*.*' => 'integer|min:0'],
            [['1.0', 'min']],
        ],
        'root dict success' => [
            ['a' => ['c' => 1, 'd' => 4], 'b' => ['c' => 'e', 'd' => 8]],
            ['*.c' => 'required'],
        ],
        'root dict failure' => [
            ['a' => ['c' => 1, 'd' => 4], 'b' => ['d' => 8]],
            ['*.c' => 'required'],
            [['b.c', 'required']],
        ],
    ];
});

test('array validation', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'cart_items' => [
            ['id_product' => 1, 'qty' => 10],
            ['id_product' => null, 'qty' => 10],
            ['id_product' => 3, 'qty' => null],
            ['id_product' => 4, 'qty' => 'foo'],
            ['id_product' => 'foo', 'qty' => 10],
        ],
    ], [
        'cart_items.*.id_product' => 'required|numeric',
        'cart_items.*.qty' => 'required|numeric',
    ]);

    $errors = $validation->errors();

    expect(4)->toEqual($errors->count());

    expect($errors->first('cart_items.1.id_product:required'))->not->toBeNull();
    expect($errors->first('cart_items.2.qty:required'))->not->toBeNull();
    expect($errors->first('cart_items.3.qty:numeric'))->not->toBeNull();
    expect($errors->first('cart_items.4.id_product:numeric'))->not->toBeNull();
});

test('set custom messages in validator', function () {
    $validator = new Validator;

    $validator->setMessages([
        'required' => 'foo',
        'email' => 'bar',
        'comments.*.text' => 'baz',
    ]);

    $validator->setMessage('numeric', 'baz');

    $validation = $validator->validate([
        'foo' => null,
        'email' => 'invalid email',
        'something' => 'not numeric',
        'comments' => [
            ['id' => 4, 'text' => ''],
            ['id' => 5, 'text' => 'foo'],
        ],
    ], [
        'foo' => 'required',
        'email' => 'email',
        'something' => 'numeric',
        'comments.*.text' => 'required',
    ]);

    $errors = $validation->errors();
    expect('foo')->toEqual($errors->first('foo:required'));
    expect('bar')->toEqual($errors->first('email:email'));
    expect('baz')->toEqual($errors->first('something:numeric'));
    expect('baz')->toEqual($errors->first('comments.0.text:required'));
});

test('set custom messages in validation', function () {
    $validator = new Validator;

    $validation = $validator->make([
        'foo' => null,
        'email' => 'invalid email',
        'something' => 'not numeric',
        'comments' => [
            ['id' => 4, 'text' => ''],
            ['id' => 5, 'text' => 'foo'],
        ],
    ], [
        'foo' => 'required',
        'email' => 'email',
        'something' => 'numeric',
        'comments.*.text' => 'required',
    ]);

    $validation->setMessages([
        'required' => 'foo',
        'email' => 'bar',
        'comments.*.text' => 'baz',
    ]);

    $validation->setMessage('numeric', 'baz');

    $validation->validate();

    $errors = $validation->errors();
    expect('foo')->toEqual($errors->first('foo:required'));
    expect('bar')->toEqual($errors->first('email:email'));
    expect('baz')->toEqual($errors->first('something:numeric'));
    expect('baz')->toEqual($errors->first('comments.0.text:required'));
});

test('custom message in callback rule', function () {
    $validator = new Validator;

    $evenNumberValidator = function ($value) {
        if (! is_numeric($value) or $value % 2 !== 0) {
            return ':attribute must be even number';
        }

        return true;
    };

    $validation = $validator->make([
        'foo' => 'abc',
    ], [
        'foo' => [$evenNumberValidator],
    ]);

    $validation->validate();

    $errors = $validation->errors();
    expect('Foo must be even number')->toEqual($errors->first('foo:callback'));
});

test('specific rule message', function () {
    $validator = new Validator;

    $validation = $validator->make([
        'something' => 'value',
    ], [
        'something' => 'email|max:3|numeric',
    ]);

    $validation->setMessages([
        'something:email' => 'foo',
        'something:numeric' => 'bar',
        'something:max' => 'baz',
    ]);

    $validation->validate();

    $errors = $validation->errors();
    expect('foo')->toEqual($errors->first('something:email'));
    expect('bar')->toEqual($errors->first('something:numeric'));
    expect('baz')->toEqual($errors->first('something:max'));
});

test('set attribute aliases', function () {
    $validator = new Validator;

    $validation = $validator->make([
        'foo' => null,
        'email' => 'invalid email',
        'something' => 'not numeric',
        'comments' => [
            ['id' => 4, 'text' => ''],
            ['id' => 5, 'text' => 'foo'],
        ],
    ], [
        'foo' => 'required',
        'email' => 'email',
        'something' => 'numeric',
        'comments.*.text' => 'required',
    ]);

    $validation->setMessages([
        'required' => ':attribute foo',
        'email' => ':attribute bar',
        'numeric' => ':attribute baz',
        'comments.*.text' => ':attribute qux',
    ]);

    $validation->setAliases([
        'foo' => 'Foo',
        'email' => 'Bar',
    ]);

    $validation->setAlias('something', 'Baz');
    $validation->setAlias('comments.*.text', 'Qux');

    $validation->validate();

    $errors = $validation->errors();
    expect('Foo foo')->toEqual($errors->first('foo:required'));
    expect('Bar bar')->toEqual($errors->first('email:email'));
    expect('Baz baz')->toEqual($errors->first('something:numeric'));
    expect('Qux qux')->toEqual($errors->first('comments.0.text:required'));
});

test('using defaults', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'is_active' => null,
        'is_published' => 'invalid-value',
    ], [
        'is_active' => 'defaults:0|required|in:0,1',
        'is_enabled' => 'defaults:1|required|in:0,1',
        'is_published' => 'required|in:0,1',
    ]);

    expect($validation->passes())->toBeFalse();

    $errors = $validation->errors();
    expect($errors->first('is_active'))->toBeNull();
    expect($errors->first('is_enabled'))->toBeNull();
    expect($errors->first('is_published'))->not->toBeNull();

    // Getting (all) validated data
    $validatedData = $validation->getValidatedData();
    expect([
        'is_active' => '0',
        'is_enabled' => '1',
        'is_published' => 'invalid-value',
    ])->toEqual($validatedData);

    // Getting only valid data
    $validData = $validation->getValidData();
    expect([
        'is_active' => '0',
        'is_enabled' => '1',
    ])->toEqual($validData);

    // Getting only invalid data
    $invalidData = $validation->getInvalidData();
    expect([
        'is_published' => 'invalid-value',
    ])->toEqual($invalidData);
});

test('humanized key in array validation', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'cart' => [
            [
                'qty' => 'xyz',
            ],
        ],
    ], [
        'cart.*.itemName' => 'required',
        'cart.*.qty' => 'required|numeric',
    ]);

    $errors = $validation->errors();

    expect('The Cart 1 qty must be numeric')->toEqual($errors->first('cart.*.qty'));
    expect('The Cart 1 item name is required')->toEqual($errors->first('cart.*.itemName'));
});

test('custom message in array validation', function () {
    $validator = new Validator;

    $validation = $validator->make([
        'cart' => [
            [
                'qty' => 'xyz',
                'itemName' => 'Lorem ipsum',
            ],
            [
                'qty' => 10,
                'attributes' => [
                    [
                        'name' => 'color',
                        'value' => null,
                    ],
                ],
            ],
        ],
    ], [
        'cart.*.itemName' => 'required',
        'cart.*.qty' => 'required|numeric',
        'cart.*.attributes.*.value' => 'required',
    ]);

    $validation->setMessages([
        'cart.*.itemName:required' => 'Item [0] name is required',
        'cart.*.qty:numeric' => 'Item {0} qty is not a number',
        'cart.*.attributes.*.value' => 'Item {0} attribute {1} value is required',
    ]);

    $validation->validate();

    $errors = $validation->errors();

    expect('Item 1 qty is not a number')->toEqual($errors->first('cart.*.qty'));
    expect('Item 1 name is required')->toEqual($errors->first('cart.*.itemName'));
    expect('Item 2 attribute 1 value is required')->toEqual($errors->first('cart.*.attributes.*.value'));
});

test('required if on array attribute', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'products' => [
            // invalid because has_notes is not empty
            '10' => [
                'quantity' => 8,
                'has_notes' => 1,
                'notes' => '',
            ],
            // valid because has_notes is null
            '12' => [
                'quantity' => 0,
                'has_notes' => null,
                'notes' => '',
            ],
            // valid because no has_notes
            '14' => [
                'quantity' => 0,
                'notes' => '',
            ],
        ],
    ], [
        'products.*.notes' => 'required_if:products.*.has_notes,1',
    ]);

    expect($validation->passes())->toBeFalse();

    $errors = $validation->errors();
    expect($errors->first('products.10.notes'))->not->toBeNull();
    expect($errors->first('products.12.notes'))->toBeNull();
    expect($errors->first('products.14.notes'))->toBeNull();
});

test('required unless on array attribute', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'products' => [
            // valid because has_notes is 1
            '10' => [
                'quantity' => 8,
                'has_notes' => 1,
                'notes' => '',
            ],
            // invalid because has_notes is not 1
            '12' => [
                'quantity' => 0,
                'has_notes' => null,
                'notes' => '',
            ],
            // invalid because no has_notes
            '14' => [
                'quantity' => 0,
                'notes' => '',
            ],
        ],
    ], [
        'products.*.notes' => 'required_unless:products.*.has_notes,1',
    ]);

    expect($validation->passes())->toBeFalse();

    $errors = $validation->errors();
    expect($errors->first('products.10.notes'))->toBeNull();
    expect($errors->first('products.12.notes'))->not->toBeNull();
    expect($errors->first('products.14.notes'))->not->toBeNull();
});

test('same rule on array attribute', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'users' => [
            [
                'password' => 'foo',
                'password_confirmation' => 'foo',
            ],
            [
                'password' => 'foo',
                'password_confirmation' => 'bar',
            ],
        ],
    ], [
        'users.*.password_confirmation' => 'required|same:users.*.password',
    ]);

    expect($validation->passes())->toBeFalse();

    $errors = $validation->errors();
    expect($errors->first('users.0.password_confirmation:same'))->toBeNull();
    expect($errors->first('users.1.password_confirmation:same'))->not->toBeNull();
});

test('get valid data', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'items' => [
            [
                'product_id' => 1,
                'qty' => 'invalid',
            ],
        ],
        'emails' => [
            'foo@bar.com',
            'something',
            'foo@blah.com',
        ],
        'stuffs' => [
            'one' => '1',
            'two' => '2',
            'three' => 'three',
        ],
        'thing' => 'exists',
    ], [
        'thing' => 'required',
        'items.*.product_id' => 'required|numeric',
        'emails.*' => 'required|email',
        'items.*.qty' => 'required|numeric',
        'something' => 'default:on|required|in:on,off',
        'stuffs' => 'required|array',
        'stuffs.one' => 'required|numeric',
        'stuffs.two' => 'required|numeric',
        'stuffs.three' => 'required|numeric',
    ]);

    $validData = $validation->getValidData();

    expect($validData)->toEqual([
        'items' => [
            [
                'product_id' => 1,
            ],
        ],
        'emails' => [
            0 => 'foo@bar.com',
            2 => 'foo@blah.com',
        ],
        'thing' => 'exists',
        'something' => 'on',
        'stuffs' => [
            'one' => '1',
            'two' => '2',
        ],
    ]);

    $stuffs = $validData['stuffs'];
    expect(isset($stuffs['three']))->toBeFalse();
});

test('get invalid data', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'items' => [
            [
                'product_id' => 1,
                'qty' => 'invalid',
            ],
        ],
        'emails' => [
            'foo@bar.com',
            'something',
            'foo@blah.com',
        ],
        'stuffs' => [
            'one' => '1',
            'two' => '2',
            'three' => 'three',
        ],
        'thing' => 'exists',
    ], [
        'thing' => 'required',
        'items.*.product_id' => 'required|numeric',
        'emails.*' => 'required|email',
        'items.*.qty' => 'required|numeric',
        'something' => 'required|in:on,off',
        'stuffs' => 'required|array',
        'stuffs.one' => 'numeric',
        'stuffs.two' => 'numeric',
        'stuffs.three' => 'numeric',
    ]);

    $invalidData = $validation->getInvalidData();

    expect($invalidData)->toEqual([
        'items' => [
            [
                'qty' => 'invalid',
            ],
        ],
        'emails' => [
            1 => 'something',
        ],
        'something' => null,
        'stuffs' => [
            'three' => 'three',
        ],
    ]);

    $stuffs = $invalidData['stuffs'];
    expect(isset($stuffs['one']))->toBeFalse();
    expect(isset($stuffs['two']))->toBeFalse();
});

test('rule in invalid messages', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'number' => 1,
    ], [
        'number' => 'in:7,8,9',
    ]);

    expect("The Number only allows '7', '8', or '9'")->toEqual($validation->errors()->first('number'));

    // Using translation
    $validator->setTranslation('or', 'atau');

    $validation = $validator->validate([
        'number' => 1,
    ], [
        'number' => 'in:7,8,9',
    ]);

    expect("The Number only allows '7', '8', atau '9'")->toEqual($validation->errors()->first('number'));
});

test('rule not in invalid messages', function () {
    $validator = new Validator;

    $validation = $validator->validate([
        'number' => 1,
    ], [
        'number' => 'not_in:1,2,3',
    ]);

    expect("The Number is not allowing '1', '2', and '3'")->toEqual($validation->errors()->first('number'));

    // Using translation
    $validator->setTranslation('and', 'dan');

    $validation = $validator->validate([
        'number' => 1,
    ], [
        'number' => 'not_in:1,2,3',
    ]);

    expect("The Number is not allowing '1', '2', dan '3'")->toEqual($validation->errors()->first('number'));
});

test('rule mimes invalid messages', function () {
    $validator = new Validator;

    $file = [
        'name' => 'sample.txt',
        'type' => 'plain/text',
        'tmp_name' => __FILE__,
        'size' => 1000,
        'error' => UPLOAD_ERR_OK,
    ];

    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => 'mimes:jpeg,png,bmp',
    ]);

    $expectedMessage = "The Sample file type must be 'jpeg', 'png', or 'bmp'";
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));

    // Using translation
    $validator->setTranslation('or', 'atau');

    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => 'mimes:jpeg,png,bmp',
    ]);

    $expectedMessage = "The Sample file type must be 'jpeg', 'png', atau 'bmp'";
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));
});

test('rule uploaded file invalid messages', function () {
    $file = [
        'name' => 'sample.txt',
        'type' => 'plain/text',
        'tmp_name' => __FILE__,
        'size' => 1024 * 1024 * 2, // 2M
        'error' => UPLOAD_ERR_OK,
    ];

    $rule = getMockedUploadedFileRule();

    $validator = new Validator;

    // Invalid uploaded file (!is_uploaded_file($file['tmp_name']))
    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => 'uploaded_file',
    ]);

    $expectedMessage = 'The Sample is not valid uploaded file';
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));

    // Invalid min size
    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => [(clone $rule)->minSize('3M')],
    ]);

    $expectedMessage = 'The Sample file is too small, minimum size is 3M';
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));

    // Invalid max size
    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => [(clone $rule)->maxSize('1M')],
    ]);

    $expectedMessage = 'The Sample file is too large, maximum size is 1M';
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));

    // Invalid file types
    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => [(clone $rule)->fileTypes(['jpeg', 'png', 'bmp'])],
    ]);

    $expectedMessage = "The Sample file type must be 'jpeg', 'png', or 'bmp'";
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));

    // Invalid file types with translation
    $validator->setTranslation('or', 'atau');
    $validation = $validator->validate([
        'sample' => $file,
    ], [
        'sample' => [(clone $rule)->fileTypes(['jpeg', 'png', 'bmp'])],
    ]);

    $expectedMessage = "The Sample file type must be 'jpeg', 'png', atau 'bmp'";
    expect($expectedMessage)->toEqual($validation->errors()->first('sample'));
});

test('ignore next rules with nullable rule', function () {
    $emptyFile = [
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_NO_FILE,
    ];

    $invalidFile = [
        'name' => 'sample.txt',
        'type' => 'plain/text',
        'tmp_name' => __FILE__,
        'size' => 1000,
        'error' => UPLOAD_ERR_OK,
    ];

    $data1 = [
        'file' => $emptyFile,
        'name' => '',
    ];

    $data2 = [
        'file' => $invalidFile,
        'name' => 'a@b.c',
    ];

    $rules = [
        'file' => 'nullable|uploaded_file:0,500K,png,jpeg',
        'name' => 'nullable|email',
    ];

    $validation1 = (new Validator)->validate($data1, $rules);
    $validation2 = (new Validator)->validate($data2, $rules);

    expect($validation1->passes())->toBeTrue();
    expect($validation2->passes())->toBeFalse();
});

test('numeric string size without numeric rule', function () {
    $validation = (new Validator)->validate([
        'number' => '1.2345',
    ], [
        'number' => 'max:2',
    ]);

    expect($validation->passes())->toBeFalse();
});

test('numeric string size with numeric rule', function () {
    $validation = (new Validator)->validate([
        'number' => '1.2345',
    ], [
        'number' => 'numeric|max:2',
    ]);

    expect($validation->passes())->toBeTrue();
});
