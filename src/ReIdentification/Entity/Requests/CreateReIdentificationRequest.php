<?php

namespace VerifyMyContent\SDK\ReIdentification\Entity\Requests;

use VerifyMyContent\SDK\Core\DTO;
use VerifyMyContent\SDK\Core\Validator\ArrayValidator;
use VerifyMyContent\SDK\Core\Validator\RequiredValidator;
use VerifyMyContent\SDK\Core\Validator\UrlValidator;
use VerifyMyContent\SDK\ReIdentification\Entity\Customer;

/**
 * Class CreateReIdentificationRequest
 * @package VerifyMyContent\SDK\ReIdentification\Entity\Requests
 * @property-read Customer $customer
 * @property-read string $redirect_uri
 * @property-read string $webhook
 */
final class CreateReIdentificationRequest extends DTO
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
