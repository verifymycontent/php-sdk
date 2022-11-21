<?php

namespace VerifyMyContent\SDK\Complaint\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class Customer
 * @package VerifyMyContent\SDK\Complaint\Entity
 * @property-read string $id
 */
final class Customer extends DTO
{
    protected $fillable = ['id'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];
}
