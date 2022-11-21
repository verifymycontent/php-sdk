<?php

namespace VerifyMyContent\SDK\Complaint\Entity;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class Content
 * @package VerifyMyContent\SDK\Complaint\Entity
 * @property-read string $external_id
 * @property-read string $type
 * @property-read string $url
 * @property-read string $title
 * @property-read string $description
 * @property-read string $tags
 */
final class Content extends DTO
{
    protected $fillable = ['external_id', 'type', 'url', 'title', 'description', 'tags'];

    protected $validate = [
        'external_id' => [
            StringValidator::class,
        ],
        'type' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'url' => [
            RequiredValidator::class,
            UrlValidator::class,
        ],
        'title' => StringValidator::class,
        'description' => StringValidator::class,
        'tags' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ]
    ];
}
