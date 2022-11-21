<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Requests;

use VerifyMyContent\SDK\ContentModeration\Entity\Content;
use VerifyMyContent\SDK\ContentModeration\Entity\Customer;
use VerifyMyContent\SDK\Core\Casts\DateTimeCast;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

final class WebhookStaticContentModerationRequest extends DTO
{
    protected $fillable = ['id', 'redirect_url', 'external_id', 'status', 'notes', 'tags', 'created_at', 'updated_at'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'redirect_url' => [
            StringValidator::class,
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
        'created_at' => DateTimeCast::class,
        'updated_at' => DateTimeCast::class,
    ];
}
