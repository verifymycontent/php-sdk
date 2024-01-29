<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Requests;

use VerifyMyContent\SDK\ContentModeration\Entity\Content;
use VerifyMyContent\SDK\ContentModeration\Entity\Customer;
use VerifyMyContent\SDK\ContentModeration\Entity\Stream;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\RulesValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

final class CreateLiveContentModerationRequest extends DTO
{
    protected $fillable = [
        'external_id', 'embed_url',
        'title', 'description',
        'stream', 'rule',
        'webhook', 'customer',
        'faces_id', 'type', 'collection_id'
    ];

    protected $validate = [
        'external_id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'embed_url' => [
            RequiredValidator::class,
            UrlValidator::class,
        ],
        'title' => [
            StringValidator::class,
        ],
        'description' => [
            StringValidator::class,
        ],
        'stream' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'faces_id' => [
            ArrayValidator::class,
        ],
        'type' => [
            StringValidator::class,
        ],
        'collection_id' => [
            StringValidator::class,
        ],
        'rule' => [
            StringValidator::class,
        ],
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'webhook' => [
            UrlValidator::class,
        ],
    ];

    protected $casts = [
        'stream' => Stream::class,
        'customer' => Customer::class,
    ];
}
