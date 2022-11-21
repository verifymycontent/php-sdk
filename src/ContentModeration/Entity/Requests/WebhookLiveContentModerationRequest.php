<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Requests;

use VerifyMyContent\SDK\ContentModeration\Entity\Content;
use VerifyMyContent\SDK\ContentModeration\Entity\Customer;
use VerifyMyContent\SDK\Core\Casts\DateTime;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

/**
 * Class WebhookLiveContentModerationRequest
 * @package VerifyMyContent\SDK\ContentModeration\Entity\Requests
 * @property-read string $id
 * @property-read string $login_url
 * @property-read string $external_id
 * @property-read string $status
 * @property-read string $notes
 * @property-read array $tags
 * @property-read DateTime $created_at
 * @property-read DateTime $updated_at
 */
final class WebhookLiveContentModerationRequest extends DTO
{
    protected $fillable = ['id', 'login_url', 'external_id', 'status', 'notes', 'tags', 'created_at', 'updated_at'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'login_url' => [
            RequiredValidator::class,
            UrlValidator::class,
        ],
        'external_id' => [
            StringValidator::class,
        ],
        'status' => [
            StringValidator::class,
        ],
        'notes' => [
            StringValidator::class,
        ],
        'tags' => [
            ArrayValidator::class,
        ],
        'created_at' => [
            StringValidator::class,
        ],
        'updated_at' => [
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
    ];
}
