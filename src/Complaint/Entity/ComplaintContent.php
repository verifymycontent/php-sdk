<?php

namespace VerifyMyContent\SDK\Complaint\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class ComplaintContent
 * @package VerifyMyContent\SDK\Complaint\Entity
 * @property-read string $external_id
 */
final class ComplaintContent extends DTO
{
    protected $fillable = ['external_id'];

    protected $validate = [
        'external_id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];
}
