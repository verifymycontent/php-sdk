<?php

namespace VerifyMyContent\SDK\ReIdentification\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class Customer
 * @package VerifyMyContent\SDK\ReIdentification\Entity
 * @property-read string $id
 * @property-read string|null $email
 */
final class Customer extends DTO
{
    protected $fillable = ['id', 'email'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'email' => [
            StringValidator::class,
        ],
    ];
}
