<?php

use Nmc\Validation\Rules\UploadedFile;

test('valid uploaded file', function () {
    $file = [
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => UPLOAD_ERR_OK,
    ];

    $uploadedFileRule = $this->getMockBuilder(UploadedFile::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $uploadedFileRule->expects($this->once())
        ->method('isUploadedFile')
        ->willReturn(true);

    expect($uploadedFileRule->check($file))->toBeTrue();
});

test('validate without mock should be invalid', function () {
    expect((new UploadedFile)->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => UPLOAD_ERR_OK,
    ]))->toBeFalse();
});

test('empty uploaded file should be valid', function () {
    expect((new UploadedFile)->check([
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_NO_FILE,
    ]))->toBeTrue();
});

test('upload error', function () {
    expect((new UploadedFile)->check([
        'name' => '',
        'type' => '',
        'size' => '',
        'tmp_name' => '',
        'error' => 5,
    ]))->toBeFalse();
});

test('max size', function () {
    $rule = $this->getMockBuilder(UploadedFile::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $rule->expects($this->exactly(2))
        ->method('isUploadedFile')
        ->willReturn(true);

    $rule->maxSize('1MB');

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => 1024 * 1024 * 1.1,
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeFalse();

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => 1000000,
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();
});

test('min size', function () {
    $rule = $this->getMockBuilder(UploadedFile::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $rule->expects($this->exactly(2))
        ->method('isUploadedFile')
        ->willReturn(true);

    $rule->minSize('10K');

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => 1024, // 1K
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeFalse();

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => 10 * 1024,
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();
});

test('file types', function () {
    $rule = $this->getMockBuilder(UploadedFile::class)
        ->onlyMethods(['isUploadedFile'])
        ->getMock();

    $rule->expects($this->exactly(3))
        ->method('isUploadedFile')
        ->willReturn(true);

    $rule->fileTypes('png|jpeg');

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => 1024, // 1K
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeFalse();

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'image/png',
        'size' => 10 * 1024,
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();

    expect($rule->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'image/jpeg',
        'size' => 10 * 1024,
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();
});

test('missing akey should be valid', function () {
    // missing name
    expect((new UploadedFile)->check([
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();

    // missing type
    expect((new UploadedFile)->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'size' => filesize(__FILE__),
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();

    // missing size
    expect((new UploadedFile)->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'tmp_name' => __FILE__,
        'error' => 0,
    ]))->toBeTrue();

    // missing tmp_name
    expect((new UploadedFile)->check([
        'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
        'type' => 'text/plain',
        'size' => filesize(__FILE__),
        'error' => 0,
    ]))->toBeTrue();
});
