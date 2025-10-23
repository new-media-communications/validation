<?php

use Rakit\Validation\Rules\Mimes;

beforeEach(function () {
    $this->rule = new Mimes();
});

test('valid mimes', function () {
    $file = [
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => UPLOAD_ERR_OK
    ];

    $uploadedFileRule = $this->getMockBuilder(Mimes::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $uploadedFileRule->expects($this->once())
        ->method('isUploadedFile')
        ->willReturn(true);

    expect($uploadedFileRule->check($file))->toBeTrue();
});

test('validate without mock should be invalid', function () {
    expect($this->rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => UPLOAD_ERR_OK
    ]))->toBeFalse();
});

test('empty mimes should be valid', function () {
    expect($this->rule->check([
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_NO_FILE
    ]))->toBeTrue();
});

test('upload error', function () {
    expect($this->rule->check([
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => 5
    ]))->toBeFalse();
});

test('file types', function () {
    $rule = $this->getMockBuilder(Mimes::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $rule->expects($this->exactly(3))
        ->method('isUploadedFile')
        ->willReturn(true);

    $rule->allowTypes('png|jpeg');

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => 1024, // 1K
        'tmp_name' => __FILE__,
        'error' => 0
    ]))->toBeFalse();

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'image/png',
        'size' => 10 * 1024,
        'tmp_name' => __FILE__,
        'error' => 0
    ]))->toBeTrue();

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'image/jpeg',
        'size' => 10 * 1024,
        'tmp_name' => __FILE__,
        'error' => 0
    ]))->toBeTrue();
});

test('missing akey should be valid', function () {
    // missing name
    expect($this->rule->check([
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => 0
    ]))->toBeTrue();

    // missing type
    expect($this->rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => 0
    ]))->toBeTrue();

    // missing size
    expect($this->rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'tmp_name' => __FILE__,
        'error' => 0
    ]))->toBeTrue();

    // missing tmp_name
    expect($this->rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'error' => 0
    ]))->toBeTrue();
});
