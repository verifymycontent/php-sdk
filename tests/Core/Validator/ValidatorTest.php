<?php

namespace Core\Validator;

use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\EmailValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;
use VerifyMyContent\SDK\Core\Validator\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequiredShouldThrowIfValueIsNull(){
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('my-field is required');

        RequiredValidator::validate(null, 'my-field');
    }

    public function testRequiredShouldThrowIfValueIsEmptyString(){
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('my-field is required');

        RequiredValidator::validate('', 'my-field');
    }

    public function testRequiredPass(){
        RequiredValidator::validate('any', 'my-field');

        $this->assertTrue(true);
    }

    public function testStringShouldNotThrowIfValueIsNull(){
        StringValidator::validate(null, 'my-field');

        $this->assertTrue(true);
    }

    public function testStringShouldThrowIfValueIsNotString(){
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('my-field must be a string');

        StringValidator::validate(1, 'my-field');
    }

    public function testStringPass(){
        StringValidator::validate('any', 'my-field');

        $this->assertTrue(true);
    }

    public function testUrlShouldNotThrowIfValueIsNull(){
        UrlValidator::validate(null, 'my-field');

        $this->assertTrue(true);
    }

    public function testUrlShouldThrowIfValueIsNotUrl(){
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('my-field must be a valid URL');

        UrlValidator::validate('not-a-url', 'my-field');
    }

    public function testUrlPass(){
        UrlValidator::validate('http://www.google.com', 'my-field');

        $this->assertTrue(true);
    }

    public function testUrlPassWithHttps(){
        UrlValidator::validate('https://www.google.com', 'my-field');

        $this->assertTrue(true);
    }

    public function testUrlPassWithPort(){
        UrlValidator::validate('http://www.google.com:8080', 'my-field');

        $this->assertTrue(true);
    }

    public function testEmailShouldNotThrowIfValueIsNull(){
        EmailValidator::validate(null, 'my-field');

        $this->assertTrue(true);
    }
    public function testEmailShouldNotThrowIfValueIsEmpty(){
        EmailValidator::validate('', 'my-field');

        $this->assertTrue(true);
    }

    public function testEmailShouldThrowIfValueIsNotEmail(){
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('my-field is not a valid email');

        EmailValidator::validate('not-an-email', 'my-field');
    }

    public function testEmailPass()
    {
        EmailValidator::validate('a@b.com', 'my-field');
        $this->assertTrue(true);
    }

    public function testArrayShouldNotThrowIfValueIsNull(){
        ArrayValidator::validate(null, 'my-field');

        $this->assertTrue(true);
    }

    public function testArrayShouldThrowIfValueIsNotArray(){
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('my-field must be an array');

        ArrayValidator::validate('not-an-array', 'my-field');
    }

    public function testArrayPass(){
        ArrayValidator::validate(['a', 'b'], 'my-field');

        $this->assertTrue(true);
    }

    public function testArrayPassWithEmptyArray(){
        ArrayValidator::validate([], 'my-field');

        $this->assertTrue(true);
    }
}
