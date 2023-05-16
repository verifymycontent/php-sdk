<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\EmailValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;

/**
 * Class Customer
 * @package VerifyMyContent\SDK\ContentModeration\Entity
 * @property-read string $id
 * @property-read string $email
 * @property-read string $phone
 */
final class Customer extends DTO
{
    protected $fillable = ['id', 'email', 'phone'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'email' => [
            RequiredValidator::class,
            EmailValidator::class,
        ],
        'phone' => [
            StringValidator::class,
        ],
    ];
}
