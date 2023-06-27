<?php

namespace VerifyMyContent\SDK\ContentModeration\Entity\Requests;

use VerifyMyContent\SDK\ContentModeration\Entity\Content;
use VerifyMyContent\SDK\ContentModeration\Entity\Customer;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

final class CreateStaticContentModerationRequest extends DTO
{
    protected $fillable = ['content', 'customer', 'webhook', 'redirect_url'];

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
    ];

    protected $casts = [
        'content' => Content::class,
        'customer' => Customer::class,
    ];
}
