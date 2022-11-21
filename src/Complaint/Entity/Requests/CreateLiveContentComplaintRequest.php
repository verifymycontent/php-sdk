<?php

namespace VerifyMyContent\SDK\Complaint\Entity\Requests;

use VerifyMyContent\SDK\Complaint\Entity\Customer;
use VerifyMyContent\SDK\Complaint\Entity\Stream;
use VerifyMyContent\SDK\Core\Casts\DateTime;
use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;

final class CreateLiveContentComplaintRequest extends DTO
{
    protected $fillable = ['complained_at', 'customer', 'stream', 'webhook'];

    protected $validate = [
        'complained_at' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'stream' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'webhook' => [
            UrlValidator::class,
        ],
    ];

    protected $casts = [
        'customer' => Customer::class,
        'stream' => Stream::class,
    ];
}
