<?php

namespace Core\Casts;

use VerifyMyContent\SDK\Core\Casts\DateTime;
use PHPUnit\Framework\TestCase;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

class DateTimeCastTest extends TestCase
{
    public function testShouldThrowIfValueIsNotString()
    {
        $this->expectException(ValidationException::class);

        new DateTime(1);
    }

    public function testShouldThrowIfValueIsNotValidDate()
    {
        $this->expectException(ValidationException::class);

        new DateTime('2019-13-01');
    }

    public function testShouldThrowIfValueIsNotValidTime()
    {
        $this->expectException(ValidationException::class);

        new DateTime('2019-01-01 25:00:00');
    }

    public function testShouldThrowIfValueIsNotValidDateTime()
    {
        $this->expectException(ValidationException::class);

        new DateTime('2019-01-01 25:00:00');
    }

    public function testShouldReturnDateTimeObject()
    {
        $dateTime = new DateTime('2019-01-01 00:00:00');

        $this->assertInstanceOf(\DateTime::class, $dateTime);
    }

    public function testShouldReturnDateTimeObjectWithCorrectDate()
    {
        $dateTime = new DateTime('2019-01-01 00:00:00');

        $this->assertEquals('2019-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testShouldReturnDateForZeroDate(){
        $dateTime = new DateTime('0001-01-01T00:00:00Z');

        $this->assertEquals('0001-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }
}
