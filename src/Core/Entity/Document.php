<?php

namespace VerifyMyContent\SDK\Core\Entity;

use VerifyMyContent\SDK\Core\Casts\DateTime;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class Document
 * @package VerifyMyContent\SDK\ContentModeration\Entity
 * @property-read string $type
 * @property-read string $country
 * @property-read string $number
 * @property-read DateTime $issued_at
 * @property-read DateTime $expires_at
 * @property-read string $name
 * @property-read DateTime $dob
 * @property-read string[]|null $mrz
 * @property-read string[]|null $photos
 */
final class Document extends DTO
{
    protected $fillable = [
        'type',
        'country',
        'number',
        'issued_at',
        'expires_at',
        'name',
        'dob',
        'mrz',
        'photos',
    ];

    protected $validate = [
        'type' => [
            StringValidator::class,
        ],
        'country' => [
            StringValidator::class,
        ],
        'number' => [
            StringValidator::class,
        ],
        'issued_at' => [
            StringValidator::class,
        ],
        'expires_at' => [
            StringValidator::class,
        ],
        'name' => [
            StringValidator::class,
        ],
        'dob' => [
            StringValidator::class,
        ],
        'mrz' => [
            ArrayValidator::class,
        ],
        'photos' => [
            ArrayValidator::class,
        ],
    ];

    protected $casts = [
        'issued_at' => DateTime::class,
        'expires_at' => DateTime::class,
        'dob' => DateTime::class,
    ];
}
