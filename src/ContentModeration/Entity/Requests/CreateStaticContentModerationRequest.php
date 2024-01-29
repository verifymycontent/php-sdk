<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Requests;

use VerifyMyContent\SDK\ContentModeration\Entity\Content;
use VerifyMyContent\SDK\ContentModeration\Entity\Customer;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

final class CreateStaticContentModerationRequest extends DTO
{
    protected $fillable = [
        'content', 'customer', 'webhook', 'redirect_url',
        'faces_id', 'type', 'collection_id'
    ];

    protected $validate = [
        'content' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'webhook' => UrlValidator::class,
        'faces_id' => [
            ArrayValidator::class,
        ],
        'type' => [
            StringValidator::class,
        ],
        'collection_id' => [
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'content' => Content::class,
        'customer' => Customer::class,
    ];
}
