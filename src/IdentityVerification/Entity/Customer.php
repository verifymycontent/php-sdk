<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class Customer
 * @package VerifyMyContent\SDK\IdentityVerification\Entity
 * @property-read string $id
 * @property-read string $email
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
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];

}
