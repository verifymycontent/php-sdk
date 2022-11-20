<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Requests;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;
use VerifyMyContent\SDK\IdentityVerification\Entity\Customer;

/**
 * Class CreateIdentityCheckRequest
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Requests
 * @property-read Customer $customer
 * @property-read string $redirect_uri
 * @property-read string $webhook
 */
final class CreateIdentityVerificationRequest extends DTO
{
    protected $fillable = ['customer', 'redirect_uri', 'webhook'];

    protected $validate = [
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'redirect_uri' => UrlValidator::class,
        'webhook' => UrlValidator::class,
    ];

    protected $casts = [
        'customer' => Customer::class
    ];
}
