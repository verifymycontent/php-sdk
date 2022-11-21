<?php

namespace VerifyMyContent\SDK\Complaint\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class Stream
 * @package VerifyMyContent\SDK\Complaint\Entity
 * @property-read string $external_id
 * @property-read string $tags
 */
final class Stream extends DTO
{
    protected $fillable = ['external_id', 'tags'];

    protected $validate = [
        'external_id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'tags' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ]
    ];
}
