<?php

namespace VerifyMyContent\SDK\Complaint\Entity\Requests;

use VerifyMyContent\SDK\Complaint\Entity\Content;
use VerifyMyContent\SDK\Complaint\Entity\Customer;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

final class CreateStaticContentComplaintRequest extends DTO
{
    protected $fillable = ['customer', 'content', 'webhook'];

    protected $validate = [
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'content' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'webhook' => [
            UrlValidator::class,
        ],
    ];

    protected $casts = [
        'customer' => Customer::class,
        'content' => Content::class,
    ];
}
