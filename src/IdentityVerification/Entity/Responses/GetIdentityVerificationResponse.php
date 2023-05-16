<?php

namespace VerifyMyContent\SDK\IdentityVerification\Entity\Responses;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\StringValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;
use VerifyMyContent\SDK\Core\Entity\Document;
use VerifyMyContent\SDK\IdentityVerification\Entity\Customer;

/**
 * Class GetIdentityCheckResponse
 * @package VerifyMyContent\SDK\IdentityVerification\Entity\Responses
 * @property-read string $id
 * @property-read string $redirect_uri
 * @property-read string $webhook
 * @property-read string $status
 * @property-read Customer $customer
 * @property-read Document $document
 * @property-read string $face
 */
final class GetIdentityVerificationResponse extends DTO
{
    protected $fillable = ['id', 'customer', 'redirect_uri', 'webhook', 'status'];

    protected $validate = [
        'id' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
        'customer' => [
            RequiredValidator::class,
            ArrayValidator::class,
        ],
        'redirect_uri' => UrlValidator::class,
        'webhook' => UrlValidator::class,
        'status' => [
            RequiredValidator::class,
            StringValidator::class,
        ],
    ];

    protected $casts = [
        'customer' => Customer::class,
    ];
}
