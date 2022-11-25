<?php

namespace Core;

use InvalidArgumentException;
use stdClass;
use VerifyMyContent\SDK\Core\Casts\DateTime;
use VerifyMyContent\SDK\Core\DTO;
use PHPUnit\Framework\TestCase;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\ValidationException;

/***
 * @property int $id
 * @property-read string $name
 * @property-read int $age
 * @property-read sampleDto $child
 * @property-read DateTime $date
 */
class sampleDto extends DTO
{
    protected $fillable = ['id', 'name', 'age', 'date', 'child'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
        ],
    ];

    protected $casts = [
        'child' => sampleDto::class,
        'date' => DateTime::class,
    ];
}

class DTOTest extends TestCase
{
    public function testDtoFill(){
        $child = new sampleDto([
            'id' => 2,
            'name' => "John's child",
            'age' => 5
        ]);

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20, 'child' => $child]);

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('John', $dto->name);
        $this->assertEquals(20, $dto->age);
        $this->assertEquals($dto->getAttributes(), [
            'id' => 1,
            'name' => 'John',
            'age' => 20,
            'child' => $child
        ]);

        $this->assertEquals($dto->child->getAttributes(), [
            'id' => 2,
            'name' => "John's child",
            'age' => 5
        ]);

        $this->assertEquals($dto->toArray(), [
            'id' => 1,
            'name' => 'John',
            'age' => 20,
            'child' => [
                'id' => 2,
                'name' => "John's child",
                'age' => 5
            ]
        ]);

        $this->assertEquals($dto->child->toArray(), [
            'id' => 2,
            'name' => "John's child",
            'age' => 5
        ]);
    }

    public function testDtoShouldThrowExceptionWhenRequiredFieldIsMissing(){
        $this->expectException(ValidationException::class);

        new sampleDto(['name' => 'John', 'age' => 20]);
    }

    public function testDtoShouldThrowExceptionWhenInvalidPropertyIsAccessed(){
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property: invalid');

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20]);
        $dto->invalid;
    }

    public function testDtoShouldThrowExceptionWhenAnyPropertyIsSet(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot set property: id');

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20]);
        $dto->id = 2;
    }

    public function testDtoShouldThrowExceptionWhenInvalidCastIsUsed(){
        $this->expectException(ValidationException::class);

        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20, 'child' => []]);
    }

    public function testDtoShouldThrowExceptionWhenValidatorIsNotAString(){
        $this->expectException(InvalidArgumentException::class);

        $dto = new class(['id' => 1, 'name' => 'John', 'age' => 20]) extends DTO {
            protected $fillable = ['id', 'name', 'age'];
            protected $validate = [
                'id' => [
                    1
                ]
            ];
        };
    }

    public function testDtoShouldThrowExceptionWhenValidatorClassNotExists(){
        $this->expectException(InvalidArgumentException::class);

        $dto = new class(['id' => 1, 'name' => 'John', 'age' => 20]) extends DTO {
            protected $fillable = ['id', 'name', 'age'];
            protected $validate = [
                'id' => [
                    'NotExistsValidator'
                ]
            ];
        };
    }

    public function testDtoShouldThrowExceptionWhenValidatorClassNotExtendsFromValidator(){
        $this->expectException(InvalidArgumentException::class);

        $dto = new class(['id' => 1, 'name' => 'John', 'age' => 20]) extends DTO {
            protected $fillable = ['id', 'name', 'age'];
            protected $validate = [
                'id' => [
                    stdClass::class
                ]
            ];
        };
    }

    public function testDtoShouldUseNowAsDefaultDateIfNotPassed()
    {
        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20, 'date' => '']);
        $now = new \DateTime();
        $this->assertEquals($dto->date->format('Y-m-d'), $now->format('Y-m-d'));
    }

    public function testDtoShouldUseAtomDateOnToArray()
    {
        $dto = new sampleDto(['id' => 1, 'name' => 'John', 'age' => 20, 'date' => '2019-01-01']);
        $this->assertEquals($dto->toArray(), [
            'id' => 1,
            'name' => 'John',
            'age' => 20,
            'date' => '2019-01-01T00:00:00+00:00'
        ]);
    }
}
